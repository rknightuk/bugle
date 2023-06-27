<?php

namespace App\Listeners;

use App\Events\PostChanged;
use App\Jobs\NotifyFollowers;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendsNewPosts
{

    public function __construct() {}

    public function handle(PostChanged $event): void
    {
        foreach ($event->post->profile->followers as $follower)
        {
            NotifyFollowers::dispatch($event->post, $follower->follower);
        }

        foreach ($event->post->formatContent()[1] as $mention) {
            NotifyFollowers::dispatch($event->post, $mention['href'], true);
        }
    }
}
