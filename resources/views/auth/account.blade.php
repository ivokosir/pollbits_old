@extends('layouts.card')

@section('card')
<div class="card-header">My Account</div>

<div class="card-body">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <p>You are logged in!</p>

    <form id="ownedFrom" action="{{ route('polls.owned') }}">
        <div class="row">
            <div class="col col-lg-6 col-xl-5">
                <input class="form-control" type="text" placeholder="Search" name="search" value="{{ $search ?? '' }}" maxlength="250">
            </div>
            <div class="col-sm-auto mt-3 mt-sm-0 text-center">
                <button class="btn btn-primary" placeholder="Search">Show my polls</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')

$('#ownedFrom').submit(() => {
    $search = $('#ownedFrom input');
    if (!$search.val()) {
        $search.prop('name', '');
    }
});

@endsection
