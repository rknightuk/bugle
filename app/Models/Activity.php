<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $guarded = [];

    protected $dates = 'created_at';

    const TYPE_REPLY = 1;
    const TYPE_LIKE = 2;
    const TYPE_BOOST = 3;

    const NAMES = [
        self::TYPE_REPLY => 'replied',
        self::TYPE_LIKE => 'liked',
        self::TYPE_BOOST => 'boosted',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }

    public function getType()
    {
        if ($this->isReply() && is_null($this->post_id))
        {
            return 'mentioned you';
        }
        return self::NAMES[$this->type];
    }

    public function getActorUsername()
    {
        $x = explode('/', $this->actor);

        return '@' . $x[array_key_last($x)];
    }

    public function getActorFullUsername()
    {
        $parts = explode('/', $this->actor);
        $urlParts = parse_url($this->actor);

        return '@' . $parts[array_key_last($parts)] . '@' . $urlParts['host'];
    }

    public function isReply()
    {
        return $this->type === self::TYPE_REPLY;
    }
}
