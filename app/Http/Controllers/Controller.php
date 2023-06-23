<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function findProfile(string $username)
    {
        $profile = Profile::where('username', $username)->first();

        if (is_null($profile)) {
            abort(404);
        }

        return $profile;
    }
}
