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
            [
                '@context' => [
                    'https://www.w3.org/ns/activitystreams',
                    [
                        'ostatus' => 'http://ostatus.org#',
                        'atomUri' => 'ostatus:atomUri',
                        'inReplyToAtomUri' => 'ostatus:inReplyToAtomUri',
                        'conversation' => 'ostatus:conversation',
                        'sensitive' => 'as:sensitive',
                        'toot' => 'http://joinmastodon.org/ns#',
                        'votersCount' => 'toot:votersCount',
                    ],
                ],
                'id' => $profile->getProfileUrl($this->uuid),
                'type' => 'Note',
                'summary' => null,
                'inReplyTo' => null,
                'published' => str_replace('+00:00', 'Z', $this->created_at->toIso8601String()),
                'url' => $profile->getProfileUrl($this->uuid),
                'attributedTo' => $profile->getProfileUrl(),
                'to' => ['https://www.w3.org/ns/activitystreams#Public'],
                'cc' => array_merge([$profile->getProfileUrl('followers')], $cc),
                'sensitive' => false,
                'atomUri' => $profile->getProfileUrl($this->uuid),
                'inReplyToAtomUri' => null,
                'conversation' => 'tag:' . config('bugle.domain.host') . ',' . $this->created_at->format('Y-m-d') . ':objectId=' . $this->id . ':objectType=Conversation',
                'content' => $content,
                'contentMap' => ['en' => $content],
                'attachment' => $this->post->attachments->map(function (Attachment $attachment) {
                    return [
                        "type" => "Document",
                        "mediaType" => $this->post->attachment->mime,
                        "url" => $this->post->attachment->getFullUrl(),
                        "name" => $this->post->attachment->alt,
                        'blurhash' => $this->post->attachment->blurhash,
                        'width' => $this->post->attachment->width,
                        'height' => $this->post->attachment->height,
                    ];
                })->toArray(),
                'tag' => $tags,
                'replies' => [
                    'id' =>
                    $profile->getProfileUrl($this->uuid . '/replies'),
                    'type' => 'Collection',
                    'first' => [
                        'type' => 'CollectionPage',
                        'next' =>
                        $profile->getProfileUrl() . '/' . $this->uuid . '/replies?only_other_accounts=true&page=true',
                        'partOf' =>
                        $profile->getProfileUrl() . '/' . $this->uuid . '/replies',
                        'items' => [],
                    ],
                ],
            ]
        ];
    }

    public function isDeleted(): bool
    {
        return (bool) $this->deleted_at;
    }
}
