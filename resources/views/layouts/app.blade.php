<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Bugle - @yield('title')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=atkinson-hyperlegible:400,400i,700,700i|antonio:600" rel="stylesheet" />

        <script defer src="/assets/js/fa-all.min.js"></script>

        @vite(['resources/css/app.css'])

        <link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/icons/favicon-16x16.png">
        <link rel="manifest" href="/assets/icons/site.webmanifest">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        @if (isset($profile))
            <meta property="og:title" content="Bugle - @yield('title')" />
            @if (isset($post))
                <meta property="description" content="{{ strip_tags($post->formatContent()[0]) }}" />
                <meta property="og:description" content="{{ strip_tags($post->formatContent()[0]) }}" />
                <meta property="og:image" content="{{ $post->firstImageOrAvatar() }}">
            @else
                <meta property="description" content="{{ strip_tags($profile->formatBio()) }}" />
                <meta property="og:description" content="{{ strip_tags($profile->formatBio()) }}" />
                <meta property="og:image" content="{{ $profile->getAvatarPath() }}">
            @endif
            <meta property="og:type" content="article" />
            <meta property="og:url" content="{{ Request::url() }}" />
        @else
            <meta property="og:title" content="Bugle - @yield('title')" />
            <meta property="description" content="A minimal ActivityPub server built with Laravel" />
            <meta property="og:description" content="A minimal ActivityPub server built with Laravel" />
            <meta property="og:type" content="article" />
            <meta property="og:url" content="{{ Request::url() }}" />
        @endif

    </head>
    <body>
        <a rel="me" href="https://bugle.lol/@bugle" style="display: none;">Mastodon</a>

        <nav class="main">
            <div class="wrapper">
                <div class="wrapper-title">
                    <span class="icon"><i class="fad fa-trumpet"></i></span> Bugle
                </div>
                <div class="wrapper-links">
                    <a href="/@bugle" style="display: flex;"><img src="/assets/ap.svg" style="height: 25px;"></a>
                    <a href="https://github.com/rknightuk/bugle"><i class="fab fa-github"></i></a>
                    @if (Auth::user())
                        <a href="/dashboard">Dashboard</a>
                        <a href="/logout">Logout</a>
                    @else
                        <a href="/login">Login</a>
                    @endif
                </div>
            </div>
        </nav>


        <div class="content">
            @yield('content')

            <footer>
                <p> <a href="https://github.com/rknightuk/bugle">Bugle</a> by <a href="https://rknight.me">Robb Knight</a> @if (Auth::user())&bull; <a href="/logout">Log Out</a>@endif</p>
            </footer>
        </div>
    </body>
</html>
