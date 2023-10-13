@extends('layouts.app', ['profiles' => $profiles])

@section('title', 'Dashboard')

@section('content')

    <h1>{{ $currentProfile->getAPUsername() }}</h1>

    <h2>Edit Toot</h2>

    <form class="ui tiny form" method="post">
        <div class="field">
            <label>Toot Content</label>
            <textarea name="content" maxlength="500">{{ $post->content }}</textarea>
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
            <input type="checkbox" name="featured" @if ($post->featured)checked="checked"@endif>
        </div>

        <input type="submit" value="Update Toot" class="button">
    </form>

    <h2>Delete Toot</h2>

    <form class="ui tiny form" method="post">
        @method('delete')
        <input type="submit" value="Delete Toot" class="button">
    </form>

@endsection
