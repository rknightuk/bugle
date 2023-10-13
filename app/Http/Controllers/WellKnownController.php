<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Profile;
use Illuminate\Http\Request;

class WellKnownController extends Controller
{
    public function nodeinfoBasic()
    {
        return [
            'links' => [
                [
                    'rel' => 'http://nodeinfo.diaspora.software/ns/schema/2.1',
                    'href' => config('bugle.domain.full') . '/nodeinfo/2.1',
                ]
            ]
        ];
    }

    public function nodeinfo()
    {
        $count = Profile::count();
        return [
            'version' => '2.1',
            'software' => [
                'name' => 'Bugle',
                'version' => config('bugle.version'),
                'repository' => 'https://github.com/rknightuk/bugle',
                'homepage' => 'https://bugle.lol',
            ],
            'protocols' => [
                'activitypub'
            ],
            'services' => [
                'outbound' => [],
                'inbound' => []
            ],
            'usage' => [
                'users' => [
                    'total' => $count,
                    'activeMonth' => $count,
                    'activeHalfyear' => $count,
                ],
                'localPosts' => Post::count(),
            ],
            'openRegistrations' => false,
            'metadata' => [
                'author' => 'Robb Knight',
            ]
        ];

    }

    public function webfinger(Request $request)
    {
        $resource = $request->input('resource');
        if (is_null($resource)) {
            abort(400);
        }

        $url = parse_url($resource);
        $host = $url['host'] ?? null;
        $path = $url['path'] ?? null;

        if (!$path)
        {
            abort(400);
        }

        if ($host !== config('bugle.domain.host')) {
            abort(400);
        }

        $username = array_values(array_filter(explode('/', $path)))[0] ?? null;

        if (empty($username))
        {
            abort(400);
        }

        $username = str_replace('@', '', $username);

        $profile = $this->findProfile($username);

        if (is_null($profile))
        {
            abort(404);
        }

        return [
            'subject' => sprintf('acct:%s@%s', $username, $host),
            'aliases' => [
                $profile->getProfileUrl()
            ],
            'links' => [
                [
                    'type' => 'text/html',
                    'rel' => 'http://webfinger.net/rel/profile-page',
                    'href' => $profile->getProfileUrl(),
                ],
                [
                    'rel' => 'self',
                    'type' => 'application/activity+json',
                    'href' => $profile->getProfileUrl(),
                ],
                [
                    'rel' => 'http://webfinger.net/rel/avatar',
                    'type' => 'image/jpeg',
                    'href' => $profile->getAvatarPath(),
                ]
            ],
        ];
    }
}
