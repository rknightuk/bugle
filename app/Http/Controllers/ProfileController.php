<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Post;
use App\Models\Profile;
use App\Models\ProfileLink;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(string $username, Request $request)
    {
        $profile = $this->findProfile($username);
        $profilePath = config('bugle.domain.full') . '/@' . $username;

        if ($request->expectsJson()) {
            return $this->jsonProfile($profile, $profilePath);
        }

        $posts = $profile->posts()->orderBy('id', 'desc')->get();

        return view('public.profile', ['profile' => $profile, 'posts' => $posts]);
    }

    public function followers(string $username, Request $request)
    {
        $profile = $this->findProfile($username);

        $core = [
            '@context' => 'https://www.w3.org/ns/activitystreams',
            'id' => $profile->getProfileUrl() . '/followers',
            'type' => 'OrderedCollection',
            'totalItems' => $profile->followers->count(),
        ];

        if ($request->has('page'))
        {
            return array_merge($core, [
                'next' => $profile->getProfileUrl() . '/followers?page=2',
                'partOf' => $profile->getProfileUrl() . '/followers',
                'orderedItems' => array_map(function(Follower $follower) {
                    return $follower->follower;
                }, $profile->followers()->paginate(10)->items()),
            ]);
        }

        return array_merge($core, [
            'first' => $profile->getProfileUrl() . '/followers?page=1',
        ]);
    }

    public function following(string $username, Request $request)
    {
        $profile = $this->findProfile($username);

        $core = [
            '@context' => 'https://www.w3.org/ns/activitystreams',
            'id' => $profile->getProfileUrl() . '/following',
            'type' => 'OrderedCollection',
            'totalItems' => 0,
        ];

        // TODO update this if I implement following
        if ($request->has('page')) {
            return array_merge($core, [
                'next' => $profile->getProfileUrl() . '/following?page=2',
                'partOf' => $profile->getProfileUrl() . '/following',
                'orderedItems' => [],
            ]);
        }

        return array_merge($core, [
            'first' => $profile->getProfileUrl() . '/following?page=1',
        ]);
    }

    public function outbox(string $username, Request $request)
    {
        $profile = $this->findProfile($username);

        $core = [
            '@context' => 'https://www.w3.org/ns/activitystreams',
            'id' => $profile->getProfileUrl() . '/outbox',
            'type' => 'OrderedCollection',
            'totalItems' => $profile->posts->count(),
            'orderedItems' => array_map(function (Post $post) use ($profile) {
                return $post->serialize($profile);
            }, $profile->posts()->orderBy('id', 'desc')->paginate(10)->items()),
        ];

        if ($request->has('page')) {
            return [
                "@context" => [
                    "https://www.w3.org/ns/activitystreams",
                    [
                        "ostatus" => "http://ostatus.org#",
                        "atomUri" => "ostatus:atomUri",
                        "inReplyToAtomUri" => "ostatus:inReplyToAtomUri",
                        "conversation" => "ostatus:conversation",
                        "sensitive" => "as:sensitive",
                        "toot" => "http://joinmastodon.org/ns#",
                        "votersCount" => "toot:votersCount",
                        "blurhash" => "toot:blurhash",
                        "focalPoint" => [
                            "@container" => "@list",
                            "@id" => "toot:focalPoint",
                        ],
                    ],
                ],
                "id" => config('bugle.domain.full') . "/outbox?page=true",
                "type" => "OrderedCollectionPage",
                "next" => config('bugle.domain.full') . "/outbox?page=" . $request->input('page') + 1,
                "prev" => config('bugle.domain.full') . "/outbox?page=" . $request->input('page') - 1,
                "partOf" => config('bugle.domain.full') . "/outbox",
                'orderedItems' => array_map(function (Post $post) use ($profile) {
                    return $post->serialize($profile);
                }, $profile->posts()->orderBy('id', 'desc')->paginate(10)->items()),
            ];
        }

        return array_merge($core, [
            'first' => $profile->getProfileUrl() . '/outbox?page=1',
            'last' => $profile->getProfileUrl() . '/outbox?page=1',
        ]);
    }

    public function post(string $username, string $postUuid, Request $request)
    {
        $profile = $this->findProfile($username);
        $post = Post::where('uuid', $postUuid)->first();

        if (is_null($post)) abort(404);

        if ($request->expectsJson())
        {
            return $post->serialize($profile);
        }

        // dd($post->counts());

        return view('public.profile', ['profile' => $profile, 'posts' => [$post], 'singleToot' => true]);
    }

    public function featured(string $username, Request $request)
    {
        $profile = $this->findProfile($username);

        return [
            "https://www.w3.org/ns/activitystreams",
            [
                "ostatus" => "http://ostatus.org#",
                "atomUri" => "ostatus:atomUri",
                "inReplyToAtomUri" => "ostatus:inReplyToAtomUri",
                "conversation" => "ostatus:conversation",
                "sensitive" => "as:sensitive",
                "toot" => "http://joinmastodon.org/ns#",
                "votersCount" => "toot:votersCount",
                "Hashtag" => "as:Hashtag",
            ],
            "id" => $profile->getProfileUrl() . "/collections/featured",
            "type" => "OrderedCollection",
            "totalItems" => $profile->posts()->where('featured', true)->count(),
            'orderedItems' => $profile->posts()->where('featured', true)->get()->map(function(Post $post) use ($profile) {
                return $post->serialize($profile);
            })->toArray(),
        ];
    }


    private function jsonProfile(Profile $profile, string $profilePath)
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
            'id' => $profilePath,
            'type' => 'Person',
            'following' => $profilePath . '/following',
            'followers' => $profilePath . '/followers',
            'inbox' => $profilePath . '/inbox',
            'outbox' => $profilePath . '/outbox',
            'featured' => $profilePath . '/collections/featured',
            'featuredTags' => $profilePath . '/collections/tags',
            'preferredUsername' => $profile->username,
            'name' => $profile->name,
            'summary' => $profile->formatBio(),
            'url' => $profilePath,
            'manuallyApprovesFollowers' => false,
            'discoverable' => true,
            'published' => '2022-12-16T00:00:00Z',
            'publicKey' => [
                'id' => $profilePath . '#main-key',
                'owner' => $profilePath,
                'publicKeyPem' => $profile->key_pub,
            ],
            'tag' => [],
            'attachment' => $profile->links->map(function (ProfileLink $link) {
                return $link->serialize();
            })->toArray(),
            'endpoints' => [
                'sharedInbox' => config('bugle.domain.full') . '/inbox',
            ],
            'icon' => [
                'type' => 'Image',
                'mediaType' => 'image/jpeg',
                'url' => $profile->getAvatarPath(),
            ],
            'image' => [
                'type' => 'Image',
                'mediaType' => 'image/jpeg',
                'url' => $profile->getHeaderPath(),
            ],
        ];
    }
}
