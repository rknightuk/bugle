@extends('layouts.app', ['public' => true])

@section('title', 'Bugle Timeline')

@section('content')

    <div class="alert alert-success">
        <i class="far fa-info-circle"></i>
        <span class="alert-content">
            You are viewing the local timeline for Bugle
        </span>
    </div>

    @include('includes.subscribe')

    @foreach ($posts as $p)
        @include('includes.toot')
    @endforeach

    {{ $posts->links() }}

@endsection
