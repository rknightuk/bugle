<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Post;
use App\Models\Profile;
use App\Services\HttpSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class InboxController extends Controller
{
    public function globalInbox(Request $request)
    {
        if (!HttpSignature::validateRequest('/inbox', $request))
        {
            abort(400);
        }

        $input = $request->input();

        if ($input['type'] !== 'Create')
        {
            return;
        }

        $this->handleMention($input);
    }

    public function userInbox(string $username, Request $request)
    {
        if (!HttpSignature::validateRequest('/@' . $username . '/inbox', $request))
        {
            abort(400);
        }

        $profile = $this->findProfile($username);
        $input = $request->input();

        if ($input['type'] === 'Undo' && Arr::get($input, 'object.type') === 'Follow') {
            info('Removing follower');
            $follower = Arr::get($input, 'object.actor');
            $profile->followers()->where('follower', $follower)->delete();
            return;
        }

        if ($input['type'] === 'Follow') {
            $response = $this->sendAccept($request, $input, $profile);

            if ($response->status() === 202) {
                $existing = $profile->followers()->where('follower', $input['actor'])->first();

                if (is_null($existing)) {
                    $profile->followers()->create([
                        'follower' => $input['actor']
                    ]);
                }
            }
        } else if ($input['type'] === 'Like') {
            $postId = $this->findPostId($username, Arr::get($input, 'object'));

            $activityData = [
                'ap_id' => $input['id'],
                'post_id' => $postId,
                'type' => Activity::TYPE_LIKE,
                'url' => null,
                'actor' => $input['actor'],
            ];

            $profile->activities()->create($activityData);

        } else if ($input['type'] === 'Announce') // Boost
        {
            $postId = $this->findPostId($username, Arr::get($input, 'object'));

            $activityData = [
                'ap_id' => $input['id'],
                'post_id' => $postId,
                'type' => Activity::TYPE_BOOST,
                'url' => null,
                'actor' => $input['actor'],
            ];

            $profile->activities()->create($activityData);
        }

        return response('Done', 202);
    }

    private function sendAccept(Request $request, array $input, Profile $profile)
    {
        $urlParts = parse_url($input['actor']);
        $targetDomain = $urlParts['scheme'] . '://' . $urlParts['host'];
        [$_, $name] = explode('@', $input['object']);

        $guid = bin2hex(random_bytes(16));

        $message = [
            '@context' => 'https://www.w3.org/ns/activitystreams',
            'id' => config('bugle.domain.full') . '/' . $guid,
            'type' => 'Accept',
            'actor' => config('bugle.domain.full') . '/@' . $name,
            'object' => json_decode($request->getContent(), true),
        ];

        $inbox = $input['actor'] . '/inbox';
        $inboxFragment = str_replace($targetDomain, '', $inbox);

        $headers = HttpSignature::generateHeaders(
            $message,
            $profile->key_pri,
            $urlParts['host'],
            $inboxFragment,
            config('bugle.domain.full') . '/@' . $name,
        );

        return Http::withHeaders($headers)
            ->withBody(json_encode($message), 'application/json')
            ->post($inbox);
    }

    private function handleMention(array $input)
    {
        info('handling mention');

        $username = null;

        $cc = Arr::get($input, 'cc', null);

        if (is_null($cc)) {
            return;
        }

        $usernames = array_map(function ($user) {
            return explode('@', $user)[1] ?? null;
        }, array_filter($cc, function ($c) {
            return str_starts_with($c, config('bugle.domain.full'));
        }));

        foreach ($usernames as $username) {
            if (is_null($username)) continue;

            $profile = Profile::where('username', $username)->first();
            if (is_null($profile)) continue;

            if (Activity::where('ap_id', $input['id'])->exists()) {
                continue;
            }

            $postId = $this->findPostId($username, Arr::get($input, 'object.inReplyTo'));

            $activityData = [
                'ap_id' => $input['id'],
                'post_id' => $postId,
                'type' => Activity::TYPE_REPLY,
                'url' => Arr::get($input, 'object.url'),
                'actor' => $input['actor'],
                'content' => Arr::get($input, 'object.content'),
            ];

            $profile->activities()->create($activityData);
        }
    }

    private function findPostId(string $username, ?string $url = null)
    {
        if (empty($url)) return null;

        $urlParts = parse_url($url);
        if ($urlParts['host'] !== config('bugle.domain.host')) {
            return null;
        }
        $postId = str_replace("/@$username/", '', $urlParts['path']);
        $post = Post::where('uuid', $postId)->first();
        return $post?->id;
    }
}
