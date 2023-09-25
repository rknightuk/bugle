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

    public function serialize()
    {
        return [
            '@context' => [
                'https://www.w3.org/ns/activitystreams',
                'https://w3id.org/security/v1',
                [
                    'manuallyApprovesFollowers' => 'as:manuallyApprovesFollowers',
                    'toot' => 'http://joinmastodon.org/ns#',
                    'featured' => [
                        '@id' => 'toot:featured',
                        '@type' => '@id',
                    ],
                    'featuredTags' => [
                        '@id' => 'toot:featuredTags',
                        '@type' => '@id',
                    ],
                    'alsoKnownAs' => [
                        '@id' => 'as:alsoKnownAs',
                        '@type' => '@id',
                    ],
                    'movedTo' => [
                        '@id' => 'as:movedTo',
                        '@type' => '@id',
                    ],
                    'schema' => 'http://schema.org#',
                    'PropertyValue' => 'schema:PropertyValue',
                    'value' => 'schema:value',
                    'discoverable' => 'toot:discoverable',
                    'Device' => 'toot:Device',
                    'Ed25519Signature' => 'toot:Ed25519Signature',
                    'Ed25519Key' => 'toot:Ed25519Key',
                    'Curve25519Key' => 'toot:Curve25519Key',
                    'EncryptedMessage' => 'toot:EncryptedMessage',
                    'publicKeyBase64' => 'toot:publicKeyBase64',
                    'deviceId' => 'toot:deviceId',
                    'claim' => [
                        '@type' => '@id',
                        '@id' => 'toot:claim',
                    ],
                    'fingerprintKey' => [
                        '@type' => '@id',
                        '@id' => 'toot:fingerprintKey',
                    ],
                    'identityKey' => [
                        '@type' => '@id',
                        '@id' => 'toot:identityKey',
                    ],
                    'devices' => [
                        '@type' => '@id',
                        '@id' => 'toot:devices',
                    ],
                    'messageFranking' => 'toot:messageFranking',
                    'messageType' => 'toot:messageType',
                    'cipherText' => 'toot:cipherText',
                    'suspended' => 'toot:suspended',
                    'Emoji' => 'toot:Emoji',
                    'focalPoint' => [
                        '@container' => '@list',
                        '@id' => 'toot:focalPoint',
                    ],
                ],
            ],
            'id' => $this->getProfileUrl(),
            'type' => 'Person',
            'following' => $this->getProfileUrl('following'),
            'followers' => $this->getProfileUrl('followers'),
            'inbox' => $this->getProfileUrl('inbox'),
            'outbox' => $this->getProfileUrl('outbox'),
            'featured' => $this->getProfileUrl('collections/featured'),
            'featuredTags' => $this->getProfileUrl('collections/tags'),
            'preferredUsername' => $this->username,
            'name' => $this->name,
            'summary' => $this->formatBio(),
            'url' => $this->getProfileUrl(),
            'manuallyApprovesFollowers' => false,
            'discoverable' => true,
            'published' => '2022-12-16T00:00:00Z',
            'publicKey' => [
                'id' => $this->getProfileUrl('#main-key'),
                'owner' => $this->getProfileUrl(),
                'publicKeyPem' => $this->key_pub,
            ],
            'tag' => [],
            'attachment' => $this->links->map(function (ProfileLink $link) {
                return $link->serialize();
            })->toArray(),
            'endpoints' => [
                'sharedInbox' => config('bugle.domain.full') . '/inbox',
            ],
            'icon' => [
                'type' => 'Image',
                'mediaType' => 'image/jpeg',
                'url' => $this->getAvatarPath(),
            ],
            'image' => [
                'type' => 'Image',
                'mediaType' => 'image/jpeg',
                'url' => $this->getHeaderPath(),
            ],
        ];
    }
}
