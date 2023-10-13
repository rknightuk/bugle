
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <header class="home">
        <h1>A minimal ActivityPub server built with Laravel</h1>
    </header>

    <div class="home-content">

        <img src="/assets/screenshot_small.png">

        <p><a href="/timeline">View Bugle's local timeline</a></p>

        <h3>What <em>does</em> it do?</h3>
        <ul class="fa-ul">
            <li><span class="fa-li"><i class="fas fa-user"></i></span>Create ActivityPub profiles</li>
            <li><span class="fa-li"><i class="fas fa-link"></i></span>Mastodon profile links</li>
            <li><span class="fa-li"><i class="fas fa-portrait"></i></span>Avatar and header images</li>
            <li><span class="fa-li"><i class="fas fa-envelope-open-text"></i></span>Send toots (with Markdown!)</li>
            <li><span class="fa-li"><i class="fas fa-edit"></i></span>Edit toots</li>
            <li><span class="fa-li"><i class="fas fa-heart-circle"></i></span>See replies, boosts, and likes</li>
            <li><span class="fa-li"><i class="fas fa-images"></i></span>Attach photos to toots</li>
            <li><span class="fa-li"><i class="fas fa-at"></i></span>Reply and @mention people in toots</li>
        </ul>

        <h3>What <em>doesn't</em> it do?</h3>
        <ul class="fa-ul">
            <li><span class="fa-li"><i class="fas fa-mobile"></i></span>Use this with your mastodon client</li>
            <li><span class="fa-li"><i class="fas fa-star"></i></span>Show featured posts on profile</li>
            <li><span class="fa-li"><i class="fas fa-plus"></i></span>Allow you to follow accounts</li>
        </ul>

        <h3>Can I use it for my ActivityPub needs?</h3>

        <p>You can but you probably shouldn't. I built it as a way to learn how ActivityPub works so although I am using it, I probably wouldn't recommend it. You'd almost definitely be better off with a full Mastodon instance. The installation instructions are <a href="https://github.com/rknightuk/bugle">on GitHub</a> if you really want to use it.</p>
    </div>

@endsection
