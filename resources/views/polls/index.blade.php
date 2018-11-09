@extends('layouts.app')

@section('content')
<h1>Polls</h1>
<div class="list-group">
    @foreach ($polls as $poll)
        <a href="/polls/{{ $poll->id }}" class="list-group-item list-group-item-action">
            <h3>{{ $poll->question }}</h3>
            <small>
                Created on {{ $poll->created_at }}
                @isset($poll->user)
                    by {{ $poll->user->name }}
                @endisset
            </small>
        </a>
    @endforeach
</div>
@endsection
