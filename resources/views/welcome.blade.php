
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <header class="home">
        <h1><i class="fad fa-trumpet"></i> Bugle</h1>
        <h2>A minimal ActivityPub server built with Laravel</h2>
    </header>

    <div class="home-content">

        <h3>What <em>does</em> it do?</h3>
        <ul class="fa-ul">
            <li><span class="fa-li"><i class="fad fa-user"></i></span>Create ActivityPub profiles</li>
            <li><span class="fa-li"><i class="fad fa-link"></i></span>Mastodon profile links</li>
            <li><span class="fa-li"><i class="fad fa-images"></i></span>Avatar and header images</li>
            <li><span class="fa-li"><i class="fad fa-envelope-open-text"></i></span>Send toots (with Markdown!)</li>
            <li><span class="fa-li"><i class="fad fa-heart-circle"></i></span>See replies, boosts, and likes</li>
            <li><span class="fa-li"><i class="fad fa-images"></i></span>Attach a photo to toots</li>
        </ul>

        <p><i class="fad fa-trumpet"></i> <a href="/@bugle">View @bugle's profile</a></li>

        <h3>What <em>doesn't</em> it do?</h3>
        <ul class="fa-ul">
            <li><span class="fa-li"><i class="fad fa-envelope-open-text"></i></span>Use this with your mastodon client</li>
            <li><span class="fa-li"><i class="fad fa-user"></i></span>Reply and @mention people in toots*</li>
            <li><span class="fa-li"><i class="fad fa-heart-circle"></i></span>Edit toots (edit works, but your followers will never see it)*</li>
        </ul>

        <p>* These are on my list to get working at some point, assuming I continue to use Bugle.</p>

        <h3>Can I use it for my ActivityPub needs?</h3>

        <p>You can but you probably shouldn't. I built it as a way to learn how ActivityPub works so although I am using it, I probably wouldn't recommend it. You'd almost definitely be better off with a full Mastodon instance. The installation instructions are <a href="https://github.com/rknightuk/bugle">on GitHub</a> if you really want to use it.</p>
    </div>

@endsection
