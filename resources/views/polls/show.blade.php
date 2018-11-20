@extends('layouts.card')

@section('card')
<div class="card-header">
    <h3>{{$poll->question}}</h3>
</div>

<div class="list-group list-group-flush">
    @foreach ($poll->options as $option)
        @php ($id = 'option' . $loop->index)
        @switch($poll->type)
            @case('approval')
                <label for="{{ $id }}" class="list-group-item list-group-item-action cursor-pointer">
                    <div class="custom-control custom-checkbox">
                        <input form="voteForm" class="custom-control-input" id="{{ $id }}" type="checkbox" name="option_ids[]" value="{{ $option->id }}">
                        <div class="custom-control-label">
                            {{ $option->text }}
                        </div>
                    </div>
                </label>
                @break
            @case('fptp')
                <label for="{{ $id }}" class="list-group-item list-group-item-action cursor-pointer">
                    <div class="custom-control custom-radio">
                        <input form="voteForm" class="custom-control-input" id="{{ $id }}" type="radio" name="option_id" value="{{ $option->id }}">
                        <div class="custom-control-label">
                            {{ $option->text }}
                        </div>
                    </div>
                </label>
                @break
            @case('score')
                <div class="list-group-item">
                    <input form="voteForm" type="hidden" name="scores[{{ $loop->index }}][option_id]" value="{{ $option->id }}">
                    <div class="row">
                        <div class="col-2">
                            <input form="voteForm" class="custom-range score-input" type="range" name="scores[{{ $loop->index }}][score]" value="0" min="0" max="5">
                            <p class="score-input-text"></p>
                        </div>
                        <div class="col">
                            {{ $option->text }}
                        </div>
                    </div>
                </div>
                @break
        @endswitch
    @endforeach
</div>

<div class="card-body">
    <button form="voteForm" class="btn btn-primary">Vote</button>
    <div class="float-right">
        <a class="btn btn-secondary" href="{{ route('polls.results', $poll->id) }}">Results</a>
        @if (Auth::check() and Auth::user()->id === $poll->user_id)
            <a class="btn btn-secondary" href="{{ route('polls.edit', $poll->id) }}">Edit</a>
        @endif
    </div>
</div>

<form id="voteForm" method="POST" action="{{ route('votes.store') }}" hidden>
    @csrf
    <input type="hidden" name="poll_id" value="{{ $poll->id }}">
</form>
@endsection

@if ($poll->type === 'score')
@section('script')

function updateScoreText(element) {
    $element = $(element);
    $element.parent().find('.score-input-text').text($element.val());
}

$('.score-input').each((i, e) => updateScoreText(e));

$('.score-input').on('input', (e) => updateScoreText(e.target));

@endsection
@endif
