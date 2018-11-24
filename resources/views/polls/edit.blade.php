@extends('layouts.card')

@section('card')
<div class="card-header">Edit Poll</div>

<form class="card-body" action="{{ route('polls.update', $poll->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" class="form-control form-control-lg mb-3" value="{{ $poll->question }}" placeholder="Poll Question" name="question" maxlength="250" required autofocus>
    @foreach ($poll->options as $option)
        @include('includes.option', [
            'edit' => true,
            'value' => $option->text,
            'index' => $loop->iteration . '.',
        ])
    @endforeach

    <div class="custom-control custom-checkbox mb-3">
        <input type="checkbox" class="custom-control-input" id="resultsHidden" name="results_hidden"{{ $poll->results_hidden ? ' checked' : '' }}>
        <label class="custom-control-label" for="resultsHidden">Hide Results</label>
        <small id="passwordHelpBlock" class="form-text text-muted">
            You will still be able to see results.
        </small>
    </div>
    <div class="custom-control custom-checkbox mb-3">
        <input type="checkbox" class="custom-control-input" id="private" name="private"{{ $poll->private ? ' checked' : '' }}>
        <label class="custom-control-label" for="private">Private</label>
        <small id="passwordHelpBlock" class="form-text text-muted">
            Poll will not show in public search.
        </small>
    </div>

    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary">Update</button>
            <a class="btn btn-secondary" href="{{ route('polls.show', $poll->id) }}">Cancel</a>
        </div>

        <div class="col-sm-auto mt-3 mt-sm-0">
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#closeWarning">
                {{ $poll->closed ? 'Open' : 'Close'}} Poll
            </button>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteWarning">Delete</button>
        </div>
    </div>
</form>

<div class="modal fade" id="deleteWarning" tabindex="-1">
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
                <form action="{{ route('polls.destroy', $poll->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="closeWarning" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ $poll->closed ? 'Open' : 'Close'}} Poll
                </h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>
                    @if ($poll->close)
                    This poll will close and voting will stop.
                    <br>
                    Results will become visible if they were previously hidden.
                    @else
                    This poll will reopen and voting will resume.
                    <br>
                    Result visibility will be set according to settings.
                    @endif
                </p>
                <p class="font-weight-bold">Do you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route($poll->closed ? 'polls.open' : 'polls.close', $poll->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-warning">
                        {{ $poll->closed ? 'Open' : 'Close'}} Poll
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
