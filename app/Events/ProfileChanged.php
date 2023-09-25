<?php

namespace App\Events;

use App\Models\Profile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Profile $profile)
    {
    }
}
