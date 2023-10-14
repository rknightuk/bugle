
@extends('layouts.app', ['public' => true])

@section('title', '🎺')

@section('content')

    <h1>Login to Bugle</h1>

    <form class="ui form" method="post" action="/login">
        <div class="field">
            <label>email</label>
            <input type="text" name="email">
        </div>
        <div class="field">
            <label>password</label>
            <input type="password" name="password">
        </div>

        <input type="submit" value="Login" class="button">
    </form>

@endsection
