@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <h1>{{ $profile->getAPUsername() }}</h1>

    <p><a href="/dashboard">&laquo; Dashboard</a></p>

    <header class="pub_profile_header">
        <div class="pub_profile_header_image">
            @if ($profile->header) <img src="{{ $profile->getHeaderPath() }}"> @endif
        </div>
        <div class="pub_profile_header_avatar">
            <img src="{{ $profile->getAvatarPath() }}" width="100">
        </div>

        <h2>{{ $profile->name }}</h2>
        <p><a href="/{{"@"}}{{ $profile->username }}" target="_blank">{{ $profile->getAPUsername() }}</a>
        {!! $profile->formatBio() !!}

        @foreach ($profile->links as $link)
            <p>{{ $link->title }}: @if ($link->isUrl())<a href="{{ $link->link}}">{{ $link->link}}</a>@else{{ $link->link}}@endif</p>
        @endforeach

        @if ($profile->followers->count())
            <p><strong>{{ $profile->followers->count() }} follower{{ $profile->followers->count() > 1 ? 's' : ''}}</strong></p>
        @endif
    </header>

    <details>
        <summary>Edit Profile</summary>
        <form method="POST" action="/dashboard/{{"@"}}{{ $profile->username }}" class="ui form" enctype="multipart/form-data">
            @csrf

            <h3>Info</h3>

            <div class="field">
                <label>Name</label>
                <input type="text" name="name" value="{{ $profile->name }}">
            </div>

            <div class="field">
                <label>Bio</label>
                <textarea name="bio" placeholder="Guitar player, skateboarder, time traveller">{{ $profile->bio }}</textarea>
            </div>

            <h3>Profile Metadata</h3>

            @foreach ($links as $index => $link)
                <div class="two fields">
                    <div class="field" style="margin-right: 5px;">
                        <input type="text" name="links[{{ $index }}][label]" placeholder="label" value="{{ $link['title'] }}">
                    </div>
                    <div style="width: 20px"></div>
                    <div class="field">
                        <input type="text" name="links[{{ $index }}][content]" placeholder="content" value="{{ $link['link'] }}">
                    </div>

                    <input type="hidden" name="links[{{ $index }}][id]" value="{{ $link['id'] }}">
                </div>
            @endforeach

            <h3>Images</h3>

            <div class="two fields">
                <div class="field">
                    <label>Avatar</label>
                    <input type="file" name="avatar">
                </div>

                <div style="width: 25px"></div>

                <div class="field">
                    <label>Header Image</label>
                    <input type="file" name="header">
                </div>
            </div>

            <input type="submit" value="Update Profile" class="button">

        </form>
    </details>

    <h2>Toots</h2>

    @foreach ($profile->posts()->orderBy('id', 'desc')->get() as $post)
        <div>
            {!! $post->formatContent()[0] !!}
            <p class="post_date">{{ $post->created_at }} <a href="/dashboard/{{"@"}}{{ $profile->username }}/{{ $post->id }}">edit</a> <a href="/{{"@"}}{{ $profile->username }}/{{ $post->uuid }}">permalink</a></p>
        </div>
    @endforeach

@endsection
