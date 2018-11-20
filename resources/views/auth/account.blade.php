@extends('layouts.card')

@section('card')
<div class="card-header">My Account</div>

<div class="card-body">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    You are logged in!
</div>
@endsection
