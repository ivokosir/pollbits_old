@extends('layouts.card')

@section('card')
<div class="card-header">Create Poll</div>
<div class="card-body">
    <form id="createForm" method="POST" action="{{ route('polls.store') }}">
        @csrf
        <input type="text" class="form-control form-control-lg mb-3" placeholder="Poll Question" name="question" required autofocus>
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
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
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

$("#createForm").on("click", ".poll-option-delete", (e) => {
    $(e.target).parents(".poll-option").remove();
    updateOptions();
});

@endsection
