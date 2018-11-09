@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('votes.store') }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <h1>{{$poll->question}}</h1>
        </div>

        <ul class="list-group list-group-flush">
            @foreach ($poll->options as $option)
                <li class="list-group-item">
                    <div class="row">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option_id" value="{{ $option->id }}">
                                <label class="form-check-label" for="exampleRadios1">
                                    {{ $option->text }}
                                </label>
                            </div>
                        </div>
                        <div class="col text-right">{{ $option->votes()->count() }}</div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="card-body">
            <button class="btn btn-primary">Vote</a>

            @if (Auth::check() and Auth::user()->id === $poll->user_id)
                <a class="btn btn-secondary float-right" href="{{ route('polls.edit', $poll->id) }}">Edit</a>
            @endif
        </div>
    </div>
</form>
@endsection
