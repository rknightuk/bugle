<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebFingerController extends Controller
{
    public function index(Request $request)
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
