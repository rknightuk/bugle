<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Bugle - @yield('title')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=atkinson-hyperlegible:400,400i,700,700i" rel="stylesheet" />

        <script defer src="/assets/js/fa-all.min.js"></script>

        @vite(['resources/css/app.css'])

        <link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/icons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/icons/favicon-16x16.png">
        <link rel="manifest" href="/assets/icons/site.webmanifest">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#212A2E">

        @if (isset($profile))
            <meta property="og:title" content="Bugle - @yield('title')" />
            @if (isset($post))
                <meta property="description" content="{!! formatForMeta($post->formatContent()[0]) !!}" />
                <meta property="og:description" content="{!! formatForMeta($post->formatContent()[0]) !!}" />
                <meta property="og:image" content="{{ $post->firstImageOrAvatar() }}">
            @else
                <meta property="description" content="{!! formatForMeta($profile->formatBio()) !!}" />
                <meta property="og:description" content="{!! formatForMeta($profile->formatBio()) !!}" />
                <meta property="og:image" content="{{ $profile->getAvatarPath() }}">
            @endif
            <meta property="og:type" content="article" />
            <meta property="og:url" content="{{ Request::url() }}" />
        @else
            <meta property="og:title" content="Bugle - @yield('title')" />
            <meta property="description" content="A minimal ActivityPub server built with Laravel" />
            <meta property="og:description" content="A minimal ActivityPub server built with Laravel" />
            <meta property="og:type" content="article" />
            <meta property="og:image" content="/assets/icons/preview.png">
            <meta property="og:url" content="{{ Request::url() }}" />
        @endif

    </head>
    <body class="@if (isset($public)) public @endif">
        <a rel="me" href="https://bugle.lol/@bugle" style="display: none;"></a>

        <div class="wrapper">
            <div class="sidebar">
                <a class="sidebar__profile" href="/dashboard/add">
                    <div class="sidebar__profile__image"><i class="fas fa-plus"></i></div>
                    <span class="sidebar__profile__meta">Add Profile</a>
                </a>
                @if (isset($profiles))
                    @foreach ($profiles as $p)
                        <a class="sidebar__profile @if (isset($currentProfile) && $currentProfile->id === $p->id) active @endif "href="/dashboard/{{"@"}}{{ $p->username }}">
                            <img class="sidebar__profile__image" src="{{ $p->getAvatarPath() }}">
                            <span class="sidebar__profile__meta">{{ $p->name }}<br><span class="sidebar__profile__username">{{ $p->getAPUsername() }}</span></a>
                        </a>
                    @endforeach
                @endif
            </div>
            <div class="main">
                <div class="nav">
                    <div class="nav__wrapper">
                        <a href="/" class="nav__title">
                            <svg class="bugleicon">
                                <use xlink:href="#bugleicon"></use>
                            </svg> Bugle
                        </a>

                        <div class="nav__links">
                            @if (Auth::user())
                                <a href="/dashboard">Dashboard</a>
                                <a href="/logout">Logout</a>
                            @else
                                <a href="/login">Login</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="content">
                    @yield('content')
                </div>
            </div>
            <footer class="footer">
                <p><a href="https://github.com/rknightuk/bugle">Bugle</a> by <a href="https://rknight.me">Robb Knight</a></p>
            </footer>


            <svg width="0" height="0" class="hidden">
                <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 314 311" id="bugleicon">
                    <g>
                    <g>
                        <g id="292">
                        <path id="Path-225" fill="none" stroke="currentColor" stroke-width="26.877487" stroke-linecap="round" stroke-linejoin="round" d="M 202.209427 229.741409 L 60.751945 291.118195 C 20.12454 309.174835 1.800675 290.9375 19.883932 250.250183 L 81.260727 108.792709"></path>
                        <path id="Oval-158" fill="none" stroke="currentColor" stroke-width="26.877487" d="M 124.137413 186.75412 C 157.46608 220.082794 191.808243 239.173767 201.629227 229.352783 C 211.450195 219.531799 193.030991 184.517868 159.702332 151.189209 C 126.373657 117.860535 91.393967 98.803772 81.572983 108.624771 C 71.751999 118.44574 90.808746 153.425446 124.137413 186.75412 Z"></path>
                        <path id="Path-226" fill="none" stroke="currentColor" stroke-width="26.877487" stroke-linecap="round" stroke-linejoin="round" d="M 132.214935 88.668533 C 132.214935 88.668533 147.779175 51.489197 163.531982 14.389435"></path>
                        <path id="Path-226-copy-3" fill="none" stroke="currentColor" stroke-width="26.877487" stroke-linecap="round" stroke-linejoin="round" d="M 226.467789 182.098053 C 226.467789 182.098053 263.186218 165.475571 299.988495 149.039841"></path>
                        <path id="Path-226-copy-2" fill="none" stroke="currentColor" stroke-width="26.877487" stroke-linecap="round" stroke-linejoin="round" d="M 187.574219 123.030777 C 187.574219 123.030777 215.987671 94.443634 244.545883 66.001221"></path>
                        <path id="Path-226-copy" fill="none" stroke="currentColor" stroke-width="26.877487" stroke-linecap="round" stroke-linejoin="round" visibility="hidden" d="M 142.555801 205.253235 C 142.555801 205.253235 212.263626 164.723663 282.176208 124.548767"></path>
                        <path id="Path-228" fill="none" stroke="currentColor" stroke-width="26.877487" stroke-linecap="round" visibility="hidden" d="M 202.209427 135.670197 C 202.209427 135.670197 170.814224 84.174072 188.770676 75.195847 C 202.209427 68.476486 229.086914 102.073334 242.52565 108.792709 C 255.964401 115.512085 222.367538 55.037735 235.806274 41.598999 C 249.245026 28.160248 282.841888 68.476486 282.841888 68.476486"></path>
                        <path id="Oval-159" fill="currentColor" fill-rule="evenodd" stroke="none" visibility="hidden" d="M 296.28064 149.108948 C 296.28064 156.53096 290.263885 162.547684 282.841888 162.547684 C 275.419891 162.547684 269.403137 156.53096 269.403137 149.108948 C 269.403137 141.68692 275.419891 135.670197 282.841888 135.670197 C 290.263885 135.670197 296.28064 141.68692 296.28064 149.108948 Z"></path>
                        <path id="path1" fill="currentColor" fill-rule="evenodd" stroke="none" visibility="hidden" d="M 255.964401 243.180145 C 255.964401 250.602158 249.947662 256.618896 242.52565 256.618896 C 235.103653 256.618896 229.086914 250.602158 229.086914 243.180145 C 229.086914 235.758148 235.103653 229.741409 242.52565 229.741409 C 249.947662 229.741409 255.964401 235.758148 255.964401 243.180145 Z"></path>
                        <path id="path2" fill="currentColor" fill-rule="evenodd" stroke="none" visibility="hidden" d="M 303 223.022034 C 303 230.444031 296.983276 236.460785 289.561249 236.460785 C 282.139252 236.460785 276.122498 230.444031 276.122498 223.022034 C 276.122498 215.600006 282.139252 209.583282 289.561249 209.583282 C 296.983276 209.583282 303 215.600006 303 223.022034 Z"></path>
                        <path id="path3" fill="currentColor" fill-rule="evenodd" stroke="none" visibility="hidden" d="M 303 21.440887 C 303 28.862885 296.983276 34.879608 289.561249 34.879608 C 282.139252 34.879608 276.122498 28.862885 276.122498 21.440887 C 276.122498 14.01886 282.139252 8.002136 289.561249 8.002136 C 296.983276 8.002136 303 14.01886 303 21.440887 Z"></path>
                        <path id="path4" fill="currentColor" fill-rule="evenodd" stroke="none" visibility="hidden" d="M 101.418839 75.195847 C 101.418839 82.617859 95.402115 88.634598 87.980095 88.634598 C 80.558083 88.634598 74.541351 82.617859 74.541351 75.195847 C 74.541351 67.773849 80.558083 61.757111 87.980095 61.757111 C 95.402115 61.757111 101.418839 67.773849 101.418839 75.195847 Z"></path>
                        <path id="path5" fill="currentColor" fill-rule="evenodd" stroke="none" visibility="hidden" d="M 188.770676 28.160248 C 188.770676 35.582245 182.753952 41.598999 175.33194 41.598999 C 167.909912 41.598999 161.893188 35.582245 161.893188 28.160248 C 161.893188 20.738251 167.909912 14.721497 175.33194 14.721497 C 182.753952 14.721497 188.770676 20.738251 188.770676 28.160248 Z"></path>
                        <path id="path6" fill="currentColor" fill-rule="evenodd" stroke="none" visibility="hidden" d="M 63.790524 55.037735 C 63.790524 62.459747 57.773792 68.476486 50.35178 68.476486 C 42.929764 68.476486 36.913033 62.459747 36.913033 55.037735 C 36.913033 47.615723 42.929764 41.598999 50.35178 41.598999 C 57.773792 41.598999 63.790524 47.615723 63.790524 55.037735 Z"></path>
                        </g>
                    </g>
                    </g>
                </symbol>
            </svg>

        </div>
    </body>
</html>
