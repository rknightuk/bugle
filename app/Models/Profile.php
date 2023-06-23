<?php

namespace App\Models;

use App\Services\TootFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profile';

    protected $guarded = [];

    public function links()
    {
        return $this->hasMany(ProfileLink::class);
    }

    public function followers()
    {
        return $this->hasMany(Follower::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function getProfileUrl(?string $path = null)
    {
        $url = config('bugle.domain.full') . '/@' . $this->username;

        if ($path)
        {
            $url .= '/' . $path;
        }

        return $url;
    }

    public function getAPUsername(): string
    {
        return '@' . $this->username . '@' . config('bugle.domain.host');
    }

    public function getAvatarPath(): string
    {
        if (!$this->avatar) return '/assets/placeholder.png';
        return config('bugle.assetpath') . $this->avatar;
    }

    public function getHeaderPath(): string
    {
        return config('bugle.assetpath') . $this->header;
    }

    public function formatBio()
    {
        if (!$this->bio) return '';
        return TootFormatter::format($this->bio)[0];
    }
}
