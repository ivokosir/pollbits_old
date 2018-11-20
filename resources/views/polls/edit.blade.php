@extends('layouts.card')

@section('card')
<div class="card-header">Edit Poll</div>
<div class="card-body">
    <form method="POST" action="{{ route('polls.update', $poll->id) }}">
        @csrf
        @method('PUT')
        <input type="text" class="form-control form-control-lg mb-3" value="{{ $poll->question }}" placeholder="Poll Question" name="question" required autofocus>
        @foreach ($poll->options as $option)
            @include('includes.option', [
                'edit' => true,
                'value' => $option->text,
                'index' => $loop->iteration . '.',
            ])
        @endforeach
        <div class="btn-group">
            <button type="submit" class="btn btn-primary">Update</button>
            <a class="btn btn-secondary" href="{{ route('polls.show', $poll->id) }}">Cancel</a>
        </div>

        <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#deleteWarning">Delete</button>

    </form>
</div>

<div class="modal fade" id="deleteWarning" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Poll</h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>This poll and all votes associated with it will be deleted.<br>This operation cannot be undone.</p>
                <p class="font-weight-bold">Do you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" form="delete-form">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" action="{{ route('polls.destroy', $poll->id) }}" method="POST" hidden>
    @csrf
    @method('DELETE')
</form>
@endsection
