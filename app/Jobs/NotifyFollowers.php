<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Models\Follower;
use App\Models\Post;
use App\Services\HttpSignature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class NotifyFollowers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Post $post, private string $follower, private ?bool $toGlobalInbox = false) {}

    public function handle(): void
    {
        $type = $this->post->isDeleted() ? 'Delete' : 'Create';

        [$content, $tags] = $this->post->formatContent();

        $id = $this->post->profile->getProfileUrl($this->post->uuid);
        $profileUrl = $this->post->profile->getProfileUrl();
        $to = ['https://www.w3.org/ns/activitystreams#Public'];
        $cc = [$this->post->profile->getProfileUrl('followers'), $this->follower];

        $message = [
            '@context' => 'https://www.w3.org/ns/activitystreams',
            'id' => $id,
            'type' => $type,
            'actor' => $profileUrl,
            'to' => $to,
            'cc' => $cc,
            'object' => [
                'id' => $id,
                'type' => 'Note',
                'published' => str_replace('+00:00', 'Z', $this->post->created_at->toIso8601String()),
                'attributedTo' => $profileUrl,
                'content' => $content,
                'to' => $to,
                'cc' => $cc,
                'senstive' => (bool) $this->post->sensitive,
                'summary' => $this->post->spoiler_text,
                'attachment' => $this->post->attachments->map(function(Attachment $attachment) {
                    return [
                        "type" => "Document",
                        "mediaType" => $attachment->mime,
                        "url" => $attachment->getFullUrl(),
                        "name" => $attachment->alt,
                        'blurhash' => $attachment->blurhash,
                        'width' => $attachment->width,
                        'height' => $attachment->height,
                    ];
                })->toArray(),
                'tag' => $tags,
            ],
        ];

        $urlParts = parse_url($this->follower);
        $targetDomain = $urlParts['scheme'] . '://' . $urlParts['host'];
        $inboxFragment = $urlParts['path'] . '/inbox';

        if ($this->toGlobalInbox)
        {
            $inboxFragment = '/inbox';
        }

        $headers = HttpSignature::generateHeaders(
            $message,
            $this->post->profile->key_pri,
            $urlParts['host'],
            $inboxFragment,
            config('bugle.domain.full') . '/@' . $this->post->profile->username,
        );

        $response = Http::withHeaders($headers)
            ->withBody(json_encode($message), 'application/json')
            ->post($targetDomain . $inboxFragment);

        if ($response->failed())
        {
            $response->throw();
        }
    }
}
