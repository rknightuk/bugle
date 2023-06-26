<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachment';

    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getFullUrl()
    {
        return config('bugle.assetpath') . $this->file;
    }
}
