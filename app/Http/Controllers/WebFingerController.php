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

        preg_match('/:(.*?)@/', $resource, $nameMatches);
        $username = $nameMatches[1] ?? null;

        preg_match('/@(.*)/', $resource, $domainMatches);
        $domain = $domainMatches[1] ?? null;

        if ($domain !== config('bugle.domain.host')) {
            abort(400);
        }

        $profile = $this->findProfile($username);

        return [
            'subject' => sprintf('acct:%s@%s', $username, $domain),
            'links' => [
                [
                    'rel' => 'self',
                    'type' => 'application/activity+json',
                    'href' => sprintf('https://%s/@%s', $domain, $username)
                ],
            ],
        ];
    }
}
