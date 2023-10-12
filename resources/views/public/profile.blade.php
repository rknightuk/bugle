@extends('layouts.app', ['post' => isset($singleToot) && $singleToot ? $posts[0] : null])

@section('title', $profile->getAPUsername())

@section('content')

    <a style="display:none" rel="me" href="{{ $profile->getProfileUrl() }}"></a>

    <div class="alert alert-success">
        <i class="far fa-info-circle"></i>
        <span class="alert-content">
            Find on Mastodon - search for <strong>{{ $profile->getAPUsername() }}</strong>
        </span>
    </div>

    <header class="pub_profile_header">
        <div class="pub_profile_header_image">
            @if ($profile->header) <img src="{{ $profile->getHeaderPath() }}"> @endif
        </div>
        <div class="pub_profile_header_avatar">
            <img src="{{ $profile->getAvatarPath() }}" width="100">
        </div>

        <h2>{{ $profile->name }}</h2>
        <p>{{ $profile->getAPUsername() }}</p>
        {!! $profile->formatBio() !!}

        @foreach ($profile->links as $link)
            <p>{{ $link->title }}: @if ($link->isUrl())<a href="{{ $link->link}}">{{ $link->link}}</a>@else{{ $link->link}}@endif</p>
        @endforeach

        @if ($profile->followers->count())
            <p><strong>{{ $profile->followers->count() }} follower{{ $profile->followers->count() > 1 ? 's' : ''}}</strong></p>
        @endif

        @if (isset($singleToot))
            <p><a href="/{{"@" . $profile->username}}">View all toots from {{"@" . $profile->username}}</a><p>
        @endif
    </header>

    <div class="public_posts">
        @if (!isset($singleToot))
            <h3>Toots</h3>
        @endif
        @foreach ($posts as $p)
            <div class="public_post">
                {!! $p->formatContent()[0] !!}
                <p class="post_date">{{ $p->created_at }} <a href="/{{"@"}}{{ $profile->username }}/{{ $p->uuid }}">Permalink</a></p>
                @if ($p->attachments->count())
                    <div class="public_post_images">
                        @foreach ($p->attachments as $attachment)
                            <div>
                                <a target="_blank" href="{{$attachment->getFullUrl()}}"><img src="{{ $attachment->getFullUrl()}}"></a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <hr>
        @endforeach
        @if (isset($singleToot))
            <h3>Activity</h3>
            @foreach ($p->activities as $a)
                <p><a href="{{ $a->actor }}">{{ $a->getActorUsername() }}</a> {{ $a->getType() }} @if ($a->url)<a href="{{ $a->url}}">{{ $a->created_at->diffForHumans() }}</a>@else{{ $a->created_at->diffForHumans() }}@endif</p>
                @if($a->isReply())
                    <div class="activity_reply">
                        {!! $a->content !!}
                    </div>
                @endif
            @endforeach
        @endif
    </div>

@endsection
