@extends('layouts.app', ['profiles' => $profiles])

@section('title', 'Create New Profile')

@section('content')

    <h1>Create New Profile</h1>

    <form method="post" class="ui form admin_create">
        @csrf
        <div class="two fields">
            <input type="text" name="username" id="username" placeholder="bugle" style="margin-right: 5px;">
            <input type="submit" value="Create Profile" class="button">
        </div>
    </form>

@endsection
