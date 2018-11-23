@extends('layouts.app')

@section('content')
@if ($polls->isEmpty())
<h5>No polls found.</h5>
@else
<div class="list-group">
    @foreach ($polls as $poll)
        <a href="{{ route('polls.show', $poll->id) }}" class="list-group-item list-group-item-action">
            <h4>
                {{ $poll->question }}
                @if ($poll->private)
                <span class="badge badge-info">Private</span>
                @endif
                @if ($poll->closed)
                <span class="badge badge-warning">Closed</span>
                @endif
            </h4>
            <small class="text-muted">
                Created on {{ $poll->created_at }}
                @isset($poll->user)
                    by {{ $poll->user->name }}
                @endisset
            </small>
        </a>
    @endforeach
</div>
{{ $polls->links() }}
@endif
@endsection
