<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Models\Follower;
use App\Models\Post;
use App\Models\Profile;
use App\Services\HttpSignature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class NotifyProfileUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Profile $profile, private string $follower) {}

    public function handle(): void
    {
        $id = $this->profile->getProfileUrl();
        $profileUrl = $this->profile->getProfileUrl();
        $to = ['https://www.w3.org/ns/activitystreams#Public'];
        $cc = [$this->profile->getProfileUrl('followers'), $this->follower];

        $message = [
            '@context' => 'https://www.w3.org/ns/activitystreams',
            'id' => $id,
            'type' => 'Update',
            'actor' => $profileUrl,
            'to' => $to,
            'cc' => $cc,
            'object' => $this->profile->serialize(),
        ];

        $urlParts = parse_url($this->follower);
        $targetDomain = $urlParts['scheme'] . '://' . $urlParts['host'];
        $inboxFragment = $urlParts['path'] . '/inbox';

        $headers = HttpSignature::generateHeaders(
            $message,
            $this->profile->key_pri,
            $urlParts['host'],
            $inboxFragment,
            config('bugle.domain.full') . '/@' . $this->profile->username,
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
