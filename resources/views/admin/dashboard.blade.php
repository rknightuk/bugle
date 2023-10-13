@extends('layouts.app', ['profiles' => $profiles])

@section('title', 'Dashboard')

@section('content')

    <h1>Activity</h1>

    <div class="admin_activity">

    @foreach ($activity as $a)
        <div class="toot compact">
            <p>
                <i class="fas fa-{{ $a->getIcon() }}"></i> <a href="{{ $a->actor }}">{{ $a->getActorUsername() }}</a> {!! $a->getType() !!} @if ($a->post) <a href="/{{"@" . $a->profile->username}}/{{ $a->post->uuid}}">{{"@"}}{{ $a->profile->username }}'s post</a> @endif @if ($a->url)<a href="{{ $a->url}}">{{ $a->created_at->diffForHumans() }}</a>@else{{ $a->created_at->diffForHumans() }}@endif @if ($a->isReply())<a class="reply-to-mention" href="/dashboard/{{"@"}}{{ $a->profile->username }}?activity_id={{ str_replace('/activity', '', $a->id) }}&mention={{ $a->getActorUsername() }}">Reply</a>@endif
            </p>
            @if($a->isReply())
                <div class="toot-reply">
                    {!! $a->content !!}
                </div>
            @endif
        </div>
    @endforeach

    {{ $activity->links() }}

    </div>

@endsection
