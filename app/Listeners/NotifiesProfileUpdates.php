<?php

namespace App\Listeners;

use App\Events\ProfileChanged;
use App\Jobs\NotifyProfileUpdate;

class NotifiesProfileUpdates
{

    public function __construct() {}

    public function handle(ProfileChanged $event): void
    {
        foreach ($event->profile->followers as $follower)
        {
            NotifyProfileUpdate::dispatch($event->profile, $follower->follower);
        }
    }
}
