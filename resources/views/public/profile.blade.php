@extends('layouts.app', ['public' => true, 'post' => isset($singleToot) && $singleToot ? $posts[0] : null])

@section('title', $profile->getAPUsername())

@section('content')

    <a style="display:none" rel="me" href="{{ $profile->getProfileUrl() }}"></a>

    <div class="alert alert-success">
        <i class="far fa-info-circle"></i>
        <span class="alert-content">
            Find <em>{{ $profile->name }}</em> on Mastodon - search for <strong>{{ $profile->getAPUsername() }}</strong>
        </span>
    </div>

    @include('includes.subscribe')

    @if (!isset($singleToot))
        <header class="profile">
            <div class="profile__image">
                @if ($profile->header) <img src="{{ $profile->getHeaderPath() }}"> @endif
            </div>
            <div class="profile__avatar">
                <img src="{{ $profile->getAvatarPath() }}" width="100">
            </div>
            <div class="profile__container">
                <p class="profile__name">
                    <strong>{{ $profile->name }}</strong> <br> <em>{{ $profile->getAPUsername() }}</em>
                    @if ($profile->followers->count())
                        <br>{{ $profile->followers->count() }} follower{{ $profile->followers->count() > 1 ? 's' : ''}}
                    @endif
                </p>

                <div class="profile__bio">
                    {!! $profile->formatBio() !!}
                </div>
                <div class="profile__links">
                    @foreach ($profile->links as $link)
                        <p><span class="profile__link">{{ $link->title }}</span> @if ($link->isUrl())<a href="{{ $link->link}}">{{ $link->link}}</a>@else{{ $link->link}}@endif</p>
                    @endforeach
                </div>
            </div>
        </header>
    @endif

    @foreach ($posts as $p)
        @include('includes.toot')
    @endforeach

    @if (!isset($singleToot))
        {{ $posts->links() }}
    @endif

@endsection
