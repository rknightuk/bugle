<?php

namespace App\Http\Controllers;

use App\Events\PostChanged;
use Bepsvpt\Blurhash\BlurHash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct(private BlurHash $blurhash){}

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
        ]);

        if ($request->file('attachment'))
        {
            $extension = $request->file('attachment')->extension();
            $mimeType = $request->file('attachment')->getMimeType();
            $dimensions = $request->file('attachment')->dimensions();
            $attachmentPath = $request->file('attachment')->storePubliclyAs('posts', $post->id . '-' . time() . '.' . $extension);

            $post->attachment()->create([
                'profile_id' => $profile->id,
                'file' => $attachmentPath,
                'alt' => $request->input('attachment_alt'),
                'mime' => $mimeType,
                'width' => $dimensions[0],
                'height' => $dimensions[1],
                'blurhash' => $this->blurhash->encode($request->file('attachment')),
            ]);
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

        $profile->posts()->where('id', $postId)->update([
            'content' => $request->input('content'),
            'featured' => $request->input('featured') === 'on',
            'visibility' => $request->input('visibility') ?? 0,
            'sensitive' => $request->has('spoiler_text'),
            'spoiler_text' => $request->input('spoiler_text'),
        ]);

        // TODO notify followers of the edit

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
