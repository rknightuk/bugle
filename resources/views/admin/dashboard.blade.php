@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <h1>Dashboard</h1>

    <h2>Profiles</h2>

    <div class="admin_profiles">
        @foreach ($profiles as $profile)
            <div class="admin_profile">
                <a href="/dashboard/{{"@"}}{{ $profile->username }}">
                    <img src="{{ $profile->getAvatarPath() }}" width="100">
                    <div>{{"@"}}{{ $profile->username }}</div>
                </a>
            </div>
        @endforeach
    </div>

    <form method="post" class="ui form admin_create">
        @csrf
        <div class="field">
            <label>Add new profile</label>
        </div>
        <div class="two fields">
            <input type="text" name="username" id="username" placeholder="bugle" style="margin-right: 5px;">
            <input type="submit" value="Create Profile" class="button">
        </div>
    </form>

    <h2>New Toot</h2>

    <form method="POST" action="/dashboard/posts" class="ui form">
        <div class="field">
            <label>Profile</label>
            <select name="username">
                @foreach ($profiles as $profile)
                    <option value="{{ $profile->username }}">{{"@"}}{{ $profile->username }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Toot Content</label>
            <textarea name="content" maxlength="500" placeholder="me, lightly touching miette with the side of my foot: miette move out of the way please so I don’t trip on you

miette, her eyes enormous: you KICK miette? you kick her body like the football? oh! oh! jail for mother! jail for mother for One Thousand Years!!!!"></textarea>
        </div>

        <div class="field">
            <label>Visibility</label>
            <select name="visibility" disabled>
                <option value="0">Public</option>
                <option value="1">Unlisted</option>
                <option value="2">Private</option>
            </select>
        </div>

        <div class="field">
            <label>Content Warning</label>
            <input type="text" name="spoiler_text" placeholder="spiders">
        </div>

        <div class="field">
            <label>Pinned to profile</label>
            <input type="checkbox" name="featured">
        </div>

        <input type="submit" value="Send Toot" class="button">
    </form>

    <h2>Activity</h2>

    <div class="admin_activity">

    @foreach ($activity as $a)
        <p><a href="{{ $a->actor }}">{{ $a->getActorUsername() }}</a> {{ $a->getType() }} @if ($a->post) to <a href="/{{"@" . $profile->username}}/{{ $a->post->uuid}}">your post</a> @endif @if ($a->url)<a href="{{ $a->url}}">{{ $a->created_at->diffForHumans() }}</a>@else{{ $a->created_at->diffForHumans() }}@endif</p>
        @if($a->isReply())
        <div class="activity_reply">
            {!! $a->content !!}
        </div>
        @endif
    @endforeach

    </div>

@endsection
