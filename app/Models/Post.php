<?php

namespace App\Models;

use App\Services\TootFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use League\CommonMark\CommonMarkConverter;

class Post extends Model
{
    const VIS_PUBLIC = 0;
    const VIS_UNLISTED = 1;
    CONST VIS_PRIVATE = 2;
    const VIS_DIRECT = 3; // not supported

    use HasFactory;

    use SoftDeletes;

    protected $dates = ['published_at'];

    protected $guarded = [];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function formatContent()
    {
        return TootFormatter::format($this->content);
    }

    public function serialize(Profile $profile, ?array $cc = [])
    {
        [$content, $tags] = $this->formatContent();

        return [
            'id' => $this->profile->getProfileUrl($this->uuid),
            'type' => 'Note',
            'inReplyTo' => $this->reply_to,
            'published' => str_replace('+00:00', 'Z', $this->created_at->toIso8601String()),
            'updated' => $this->updated_at ? str_replace('+00:00', 'Z', $this->updated_at->toIso8601String()) : null,
            'attributedTo' => $this->profile->getProfileUrl(),
            'content' => $content,
            'to' => ['https://www.w3.org/ns/activitystreams#Public'],
            'cc' => array_merge([$this->profile->getProfileUrl('followers')], $cc),
            'senstive' => (bool) $this->sensitive,
            'summary' => $this->spoiler_text,
            'attachment' => $this->attachments->map(function (Attachment $attachment) {
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
        ];
    }

    public function isDeleted(): bool
    {
        return (bool) $this->deleted_at;
    }
}
