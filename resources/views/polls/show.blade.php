@extends('layouts.card')

@section('card')
<h4 class="card-header">
    {{$poll->question}}
</h4>

<div class="list-group list-group-flush">
@foreach ($options as $option)
@if ($voted)

@switch($poll->type)
    @case('approval')
        <label class="list-group-item">
            <div class="custom-control custom-checkbox">
                <input form="voteForm" class="custom-control-input" type="checkbox" disabled{{ $option->score ? ' checked' : ''}}>
                <div class="custom-control-label">
                    {{ $option->text }}
                </div>
            </div>
        </label>
        @break
    @case('fptp')
        <label class="list-group-item">
            <div class="custom-control custom-radio">
                <input form="voteForm" class="custom-control-input" type="radio" disabled{{ $option->score ? ' checked' : ''}}>
                <div class="custom-control-label">
                    {{ $option->text }}
                </div>
            </div>
        </label>
        @break
    @case('score')
        <div class="list-group-item">
            <div class="row">
                <div class="col-auto align-self-center">
                    <input form="voteForm" class="custom-range score-input" type="range" min="0" max="5" disabled value="{{ $option->score}}">
                    <div class="score-input-text">{{ $option->score}}</div>
                </div>
                <div class="col">
                    {{ $option->text }}
                </div>
            </div>
        </div>
        @break
@endswitch

@else

@switch($poll->type)
    @case('approval')
        <label class="list-group-item list-group-item-action cursor-pointer">
            <div class="custom-control custom-checkbox">
                <input form="voteForm" class="custom-control-input score" type="checkbox" name="option_ids[]" value="{{ $option->id }}">
                <div class="custom-control-label">
                    {{ $option->text }}
                </div>
            </div>
        </label>
        @break
    @case('fptp')
        <label class="list-group-item list-group-item-action cursor-pointer">
            <div class="custom-control custom-radio">
                <input form="voteForm" class="custom-control-input score" type="radio" name="option_id" value="{{ $option->id }}">
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
                <div class="col-auto align-self-center">
                    <input form="voteForm" class="custom-range score-input score" type="range" name="scores[{{ $loop->index }}][score]" value="0" min="0" max="5">
                    <div class="score-input-text"></div>
                </div>
                <div class="col">
                    {{ $option->text }}
                </div>
            </div>
        </div>
        @break
@endswitch

@endif
@endforeach
</div>

<div class="card-body">
    <div class="row">
        @if ($voted)
        <div class="col">
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#clearVoteWarning">Clear Vote</button>
        </div>
        @else
        <form class="col" id="voteForm" action="{{ route('votes.store') }}" method="POST">
            @csrf
            <input type="hidden" name="poll_id" value="{{ $poll->id }}">
            <button id="voteButton" class="btn btn-primary">Vote</button>
        </form>
        @endif

        <div class="col-sm-auto mt-3 mt-sm-0">
            @if ($canSeeResults)
            <a class="btn btn-secondary" href="{{ route('polls.results', $poll->id) }}">Results</a>
            @endif
            @if ($owned)
            <a class="btn btn-secondary" href="{{ route('polls.edit', $poll->id) }}">Edit</a>
            @endif
        </div>
    </div>
</div>

<small class="card-footer text-muted">
    Created on {{ $poll->created_at }}
    @isset($poll->user)
        by {{ $poll->user->name }}
    @endisset
</small>

@if ($voted)
<div class="modal fade" id="clearVoteWarning" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Poll</h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>
                    This poll and all votes associated with it will be deleted.
                    <br>
                    This operation cannot be undone.
                </p>
                <p class="font-weight-bold">Do you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('votes.destroy', $vote->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-warning">Clear Vote</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@if (!$voted)
@section('script')

@if ($poll->type === 'score')
function updateScoreText(element) {
    $element = $(element);
    $element.parent().find('.score-input-text').text($element.val());
}

$('.score-input').each((i, e) => updateScoreText(e));

$('.score-input').on('input', (e) => updateScoreText(e.target));
@endif

function updateVoteEnabled() {

@switch($poll->type)
    @case('approval')
    @case('fptp')

        let enabled = $('.score:checked').length > 0;

        @break
    @case('score')

        let enabled = false;
        $('.score').each((index, element) => {
            if (element.valueAsNumber !== 0) {
                enabled = true;
            }
        });

        @break
@endswitch

    $('#voteButton').attr('disabled', enabled ? null : 'disabled');
}

updateVoteEnabled();
$('.score').change(updateVoteEnabled);

@endsection
@endif
