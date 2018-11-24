@extends('layouts.card')

@section('card')
<div class="card-header">Create Poll</div>

<form class="card-body" action="{{ route('polls.store') }}" method="POST">
    @csrf
    <input type="text" class="form-control form-control-lg mb-3" placeholder="Poll Question" name="question" maxlength="250" required autofocus>
    @include('includes.option')
    @include('includes.option')
    <div id="insertBefore" class="form-group text-center">
        <button id="addNewOption" type="button" class="btn btn-secondary">Add Option</button>
    </div>

    <div class="form-inline mb-3">
        <label for="pollType">Poll Type</label>
        <select class="custom-select ml-2" id="pollType" name="type">
            <option value="fptp" selected>Simple (FPTP)</option>
            <option value="approval">Simple with multiple answers (Approval)</option>
            <option value="score">Score</option>
        </select>
    </div>

    @auth
    <div class="custom-control custom-checkbox mb-3">
        <input type="checkbox" class="custom-control-input" id="resultsHidden" name="results_hidden">
        <label class="custom-control-label" for="resultsHidden">Hide Results</label>
        <small id="passwordHelpBlock" class="form-text text-muted">
            You will still be able to see results.
        </small>
    </div>
    <div class="custom-control custom-checkbox mb-3">
        <input type="checkbox" class="custom-control-input" id="private" name="private">
        <label class="custom-control-label" for="private">Private</label>
        <small id="passwordHelpBlock" class="form-text text-muted">
            Poll will not show in public search.
        </small>
    </div>
    @endauth

    @guest
    <small class="d-block text-muted mb-3">
        <a href="{{ route('register') }}">Create account</a> to access more features.
    </small>
    @endguest

    <button type="submit" class="btn btn-primary">Create</button>
</form>
@endsection

@section('script')

function updateOptions() {
    deletes = $(".poll-option-delete");

    if (deletes.length <= 2) {
        deletes.attr("disabled", "disabled");
    } else {
        deletes.removeAttr("disabled");
    }

    $(".poll-option-id").each((i, element) => {
        element.textContent = i + 1 + ".";
    });
}

updateOptions();

const optionHTML = `
@include('includes.option')
`;

$("#addNewOption").on("click", () => {
    $("#insertBefore").before(optionHTML);
    updateOptions();
    $(".poll-option-input:last").focus();
});

$("form").on("click", ".poll-option-delete", (e) => {
    $(e.target).parents(".poll-option").remove();
    updateOptions();
});

@endsection
