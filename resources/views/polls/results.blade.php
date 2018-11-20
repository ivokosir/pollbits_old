@extends('layouts.card')

@section('card')
<div class="card-header">
    <h3>{{$poll->question}}</h3>
</div>

<div class="list-group list-group-flush">
    @foreach ($results as $result)
        @php
        switch ($poll->type) {
            case 'approval':
            case 'fptp':
                $barWidth = $result->average * 100;
                $display = number_format($barWidth) . '%';
                $details = $result->score . ' votes';
                break;
            case 'score':
                $barWidth = $result->average / 5 * 100;
                $display = number_format($result->average, 1);
                $details = $result->score . ' points';
                break;
        }
        @endphp
        <div class="list-group-item">
            <div class="result-bar" style="width:{{ $barWidth }}%"></div>
            <div class="row">
                <div class="col">
                    {{ $result->text }}
                </div>
                <div class="col text-right">
                    {{ $display }}
                </div>
                <div class="col-auto">
                    <div class="text-right" style="width:5.2rem;margin-left:-2.2rem">
                        <small>({{ $details }})</small>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card-body">
    <a class="btn btn-secondary float-right" href="{{ route('polls.show', $poll->id) }}">Vote</a>
</div>
@endsection
