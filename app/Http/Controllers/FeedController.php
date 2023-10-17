<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function timelineJson()
    {
        return $this->json(
            'Bugle timeline',
            'All public posts from bugle.lol',
            '/timeline',
            Post::orderBy('id', 'desc')->limit(50)->get()
        );
    }

    public function timelineRSS()
    {
        return $this->rss(
            'Bugle timeline',
            'All public posts from bugle.lol',
            '/timeline',
            Post::orderBy('id', 'desc')->limit(50)->get()
        );
    }

    public function profileJson(string $username)
    {
        $profile = $this->findProfile($username);

        return $this->json(
            $profile->name,
            formatForMeta($profile->formatBio()),
            '/@' . $profile->username,
            $profile->posts()->orderBy('id', 'desc')->limit(50)->get()
        );
    }

    public function profileRSS(string $username)
    {
        $profile = $this->findProfile($username);

        return $this->rss(
            $profile->name,
            formatForMeta($profile->formatBio()),
            '/@' . $profile->username,
            $profile->posts()->orderBy('id', 'desc')->limit(50)->get()
        );
    }

    private function json(string $title, string $description, string $path, Collection $posts)
    {
        return [
            'version' => 'https://jsonfeed.org/version/1.1',
            'title' => $title,
            'description' => $description,
            'home_page_url' => config('bugle.domain.full') . $path,
            'feed_url' => config('bugle.domain.full') . $path . '/feed.xml',
            'items' => $posts->map(function (Post $post) {
                return [
                    'id' => $post->uuid,
                    'url' => $post->getUrl(),
                    'title' => $post->content,
                    'content_text' => $post->content,
                    'date_published' => $post->created_at->toIso8601String(),
                    'attachments' => $post->attachments->map(function(Attachment $attachment) {
                        return [
                            'url' => $attachment->getFullUrl(),
                            'mime_type' => $attachment->mime,
                            'title' => $attachment->alt,
                        ];
                    }),
                ];
            }),
        ];
    }

    private function rss(string $title, string $description, string $path, Collection $posts)
    {
        $data = [
            'title' => $title,
            'description' => $description,
            'url' => config('bugle.domain.full') . $path,
            'feedUrl' => config('bugle.domain.full') . $path . '/feed.xml',
            'posts' => $posts,
        ];

        return response()->view('public.rss', $data)->header('Content-Type', 'application/xml');
    }
}
