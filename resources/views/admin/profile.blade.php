@extends('layouts.app', ['profiles' => $profiles])

@section('title', 'Dashboard')

@section('content')

    <h1>{{ $currentProfile->name }}</h1>

    <p>
        <a href="/{{"@"}}{{ $currentProfile->username }}" target="_blank" class="button small">View Profile</a>
    </p>

    <h2>New Toot</h2>

    @if (isset($replyingTo) && $replyingTo)
        <div class="alert alert-success">
            <i class="far fa-info-circle"></i>
            <span class="alert-content">
                <strong>Replying to <a target="_blank" href="{{ $replyingTo->url }}">{{ $replyingTo->getActorUsername() }}</a></strong>:<br>
                {!! $replyingTo->content !!}
            </span>
        </div>
    @endif

    <form method="POST" action="/dashboard/{{"@"}}{{ $currentProfile->username }}/posts" class="ui tiny form" enctype="multipart/form-data">
        <input type="hidden" name="reply_to" value="@if (isset($replyingTo) && $replyingTo){{ str_replace('/activity', '', $replyingTo->ap_id) }}@endif">

        <div class="field">
            <label>Toot Content</label>
            <textarea name="content" maxlength="500" placeholder="me, lightly touching miette with the side of my foot: miette move out of the way please so I don’t trip on you

miette, her eyes enormous: you KICK miette? you kick her body like the football? oh! oh! jail for mother! jail for mother for One Thousand Years!!!!">@if (isset($mention) && $mention){{ $mention }} @endif</textarea>
        </div>

        <div class="field">
            <label>Content Warning</label>
            <input type="text" name="spoiler_text" placeholder="spiders">
        </div>

        <details>
         <summary>Attachments</summary>

            @foreach (range(0, 3) as $i => $attachment)
                <div class="field">
                    <label>Attachment {{ $i + 1 }}</label>
                    <input type="file" name="attachments[{{ $i }}]">
                </div>

                <div class="field">
                    <label>Attachment {{ $i + 1 }} Alt Text</label>
                    <input type="text" name="attachment_alt[{{ $i }}]">
                </div>
            @endforeach

            <div class="field">
                <label>Pinned to profile</label>
                <input type="checkbox" name="featured">
            </div>
        </details>

        <input type="submit" value="Send Toot" class="button">
    </form>

    <h2>Edit Profile</h2>
    <form method="POST" action="/dashboard/{{"@"}}{{ $currentProfile->username }}" class="ui form" enctype="multipart/form-data">
        @csrf

        <div class="field">
            <label>Name</label>
            <input type="text" name="name" value="{{ $currentProfile->name }}">
        </div>

        <div class="field">
            <label>Bio</label>
            <textarea name="bio" placeholder="Guitar player, skateboarder, time traveller">{{ $currentProfile->bio }}</textarea>
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

@endsection
