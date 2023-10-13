<div class="toot">
    <div class="toot__profile">
    <a style="display:none" rel="me" href="{{ $p->profile->getProfileUrl() }}"></a>
        <img class="toot__profile__image" src="{{ $p->profile->getAvatarPath() }}">
        <div class="toot__profile__meta">
            <a class="toot__profile__name" href="{{ $p->profile->getProfileUrl() }}">{{ $p->profile->name }}</a><br>
            <span class="toot__profile__date">{{ $p->created_at }} @if (!isset($singleToot))<a href="/{{"@"}}{{ $p->profile->username }}/{{ $p->uuid }}">Permalink</a>@endif
        </div>
    </div>
    <div class="toot__content">
        {!! $p->formatContent()[0] !!}
    </div>
    <div class="toot__images">
        @foreach ($p->attachments as $attachment)
            <div>
                <a target="_blank" href="{{$attachment->getFullUrl()}}"><img src="{{ $attachment->getFullUrl()}}"></a>
            </div>
        @endforeach
    </div>
    <div class="toot__boosts">
        <div>
            <i class="fas fa-comment"></i> {{ $p->getActivityCounts()['replies'] > 0 ? $p->getActivityCounts()['replies'] : '' }}
        </div>
        <div>
            <i class="fas fa-star"></i> {{ $p->getActivityCounts()['likes'] > 0 ? $p->getActivityCounts()['likes'] : '' }}
        </div>
        <div>
            <i class="fas fa-rocket-launch"></i> {{ $p->getActivityCounts()['boosts'] > 0 ? $p->getActivityCounts()['boosts'] : '' }}
        </div>
        @if (Auth::user())
            <div>
                <a href="/dashboard/{{"@"}}{{ $p->profile->username }}/{{ $p->id }}"><i class="fas fa-edit"></i></a>
            </div>
        @endif
    </div>
</div>
