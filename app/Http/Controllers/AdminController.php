<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Profile;
use App\Models\ProfileLink;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $profiles = Profile::get();

        // dd(Activity::orderBy('id', 'desc')->get()->toArray());

        return view('admin.dashboard', [
            'profiles' => $profiles,
            'activity' => Activity::orderBy('id', 'desc')->get(),
        ]);
    }

    public function showProfile(string $username)
    {
        $profile = $this->findProfile($username);

        $links = $profile->links->map(function(ProfileLink $link) {
            return [
                'id' => $link->id,
                'title' => $link->title,
                'link' => $link->link,
            ];
        })->values()->toArray();

        if (count($links) < 4)
        {
            foreach (range(0, 3 - count($links)) as $_) {
                $links[] = [
                    'id' => '',
                    'title' => '',
                    'link' => ''
                ];
            }
        }

        return view('admin.profile', ['profile' => $profile, 'links' => $links]);
    }

    public function createProfile(Request $request)
    {
        $username = $request->input('username');

        $existing = Profile::where('username', $username)->first();

        if (!is_null($existing))
        {
            return redirect('/dashboard')->with('error', 'Username already taken');
        }

        $config = [
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];
        $keypair = openssl_pkey_new($config);
        openssl_pkey_export($keypair, $private_key);

        $public_key = openssl_pkey_get_details($keypair);
        $public_key = $public_key["key"];

        Profile::create([
            'username' => $request->input('username'),
            'name' => $request->input('username'),
            'key_pub' => $public_key,
            'key_pri' => $private_key,
        ]);

        return redirect('/dashboard/@' . $username)->with('success', '@' . $username . ' created!');
    }

    // TODO can I notify servers of profile change?
    public function updateProfile(string $username, Request $request)
    {
        $profile = $this->findProfile($username);

        foreach ($request->input('links') as $link)
        {
            $hasValidValues = !empty($link['label']) && !empty($link['content']);
            if ($link['id'])
            {
                if ($hasValidValues)
                {
                    $profile->links()->where('id', $link['id'])->update([
                        'title' => $link['label'],
                        'link' => $link['content']
                    ]);
                } else {
                    $profile->links()->where('id', $link['id'])->delete();
                }
            } else if ($hasValidValues) {
                $profile->links()->create([
                    'title' => $link['label'],
                    'link' => $link['content']
                ]);
            }
        }
        $profile->name = $request->input('name');
        $profile->bio = $request->input('bio');
        $profile->save();

        $profile = $this->assignImages($profile, $request);

        return redirect('/dashboard/@' . $username)->with('success', '@' . $username . ' updated!');
    }

    private function assignImages(Profile $profile, Request $request)
    {
        $changed = false;

        if ($request->file('avatar')) {
            $avatarPath = $request->file('avatar')->storePubliclyAs('avatars', $profile->username . '.png');
            $profile->avatar = $avatarPath;
            $changed = true;
        }

        if ($request->file('header')) {
            $headerPath = $request->file('header')->storePubliclyAs('headers', $profile->username . '.png');
            $profile->header = $headerPath;
            $changed = true;
        }

        if ($changed) $profile->save();

        return $profile;
    }
}
