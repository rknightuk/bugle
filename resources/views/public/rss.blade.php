<?xml version="1.0" encoding="utf-8"?>
<feed
    xmlns="http://www.w3.org/2005/Atom">
    <title>{{ $title }}</title>
    <subtitle>{{ $description }}</subtitle>
    <link href="{{ $feedUrl }}" rel="self"/>
    <link href="{{ $url }}"/>
    <updated>{{ now() }}</updated>
    <id>{{ $url }}</id>
    @foreach($posts as $post)
        <entry>
            <title>{{ $post->formatContent()[0] }}</title>
            <link href="{{ $post->getUrl()}}"/>
            <updated>{{ $post->created_at->toISOString() }}</updated>
            <id>{{ $post->getUrl() }}</id>
            <content type="html">{{ $post->formatContent()[0] }}</content>
        </entry>
    @endforeach
</feed>
