<?php

namespace App\Http\Controllers;

use App\Events\PostChanged;
use Bepsvpt\Blurhash\BlurHash;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct(private BlurHash $blurhash){}

    public function createFromApi(string $username, Request $request)
    {
        $apiKey = $request->input('api_key');

        if ($apiKey !== env('API_KEY'))
        {
            throw new Exception();
        }

        $profile = $this->findProfile($username);

        $post = $profile->posts()->create([
            'uuid' => Str::uuid(),
            'content' => $request->input('content'),
            'featured' => false,
            'visibility' => 0,
            'sensitive' => false,
            'spoiler_text' => null,
            'reply_to' => null,
        ]);

        PostChanged::dispatch($post);

        return $post;
    }

    public function create(Request $request)
    {
        $username = $request->input('username');
        $profile = $this->findProfile($username);

        $post = $profile->posts()->create([
            'uuid' => Str::uuid(),
            'content' => $request->input('content'),
            'featured' => $request->input('featured') === 'on',
            'visibility' => $request->input('visibility') ?? 0,
            'sensitive' => $request->has('spoiler_text'),
            'spoiler_text' => $request->input('spoiler_text'),
            'reply_to' => $request->input('reply_to', null),
        ]);

        $attachments = $request->file('attachments');

        if (!is_null($attachments))
        {
            foreach ($attachments as $i => $attachment) {
                if (is_null($attachment)) continue;

                $extension = $attachment->extension();
                $mimeType = $attachment->getMimeType();
                $dimensions = $attachment->dimensions();
                $attachmentPath = $attachment->storePubliclyAs('posts', $post->id . '-' . time() . '.' . $extension);

                $post->attachments()->create([
                    'profile_id' => $profile->id,
                    'file' => $attachmentPath,
                    'alt' => $request->input('attachment_alt')[$i] ?? '',
                    'mime' => $mimeType,
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                    'blurhash' => $this->blurhash->encode($attachment),
                ]);
            }
        }

        PostChanged::dispatch($post);

        return redirect('/dashboard/@' . $username)->with('success', 'Toot created!');
    }

    public function showEdit(string $username, int $postId)
    {
        $profile = $this->findProfile($username);
        $post = $profile->posts()->where('id', $postId)->first();

        return view('admin.post', ['profile' => $profile, 'post' => $post]);
    }

    public function edit(string $username, int $postId, Request $request)
    {
        $profile = $this->findProfile($username);

        $post = $profile->posts()->where('id', $postId)->first();

        $post->update([
            'content' => $request->input('content'),
            'featured' => $request->input('featured') === 'on',
            'visibility' => $request->input('visibility') ?? 0,
            'sensitive' => $request->has('spoiler_text'),
            'spoiler_text' => $request->input('spoiler_text'),
        ]);

        PostChanged::dispatch($post);

        return redirect('/dashboard/@' . $username)->with('success', 'Toot updated!');
    }

    public function delete(string $username, int $postId)
    {
        $profile = $this->findProfile($username);

        $post = $profile->posts()->where('id', $postId)->first();
        $post->delete();

        PostChanged::dispatch($post);

        return redirect('/dashboard/@' . $username)->with('success', 'Toot deleted!');
    }
}
