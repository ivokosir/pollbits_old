@extends('layouts.card')

@section('card')
<h4 class="card-header">
    {{$poll->question}}
    @if ($poll->closed)
    <span class="badge badge-warning">Closed</span>
    @endif
</h4>

<div class="list-group list-group-flush">
    @foreach ($results as $result)
        @php
        $average = $voteCount ? $result->score / $voteCount : 0;
        switch ($poll->type) {
            case 'approval':
            case 'fptp':
                $barWidth = $average * 100;
                $display = number_format($barWidth) . '%';
                $details = $result->score . ' votes';
                break;
            case 'score':
                $barWidth = $average / 5 * 100;
                $display = number_format($average, 1);
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
                <div class="col-auto text-right">
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
    <div class="row">
        <div class="col">
            <a class="btn btn-secondary" href="{{ route('polls.resultsCSV', $poll->id) }}">Export CSV</a>
        </div>

        <div class="col-sm-auto mt-3 mt-sm-0">
            @if (!$poll->closed)
            <a class="btn btn-secondary" href="{{ route('polls.show', $poll->id) }}">Vote</a>
            @endif
            @if ($owned)
            <a class="btn btn-secondary" href="{{ route('polls.edit', $poll->id) }}">Edit</a>
            @endif
        </div>
    </div>
</div>

<div class="card-footer text-muted">
    <div class="row">
        <small class="col">
            Created on {{ $poll->created_at }}
            @isset($poll->user)
                by {{ $poll->user->name }}
            @endisset
        </small>
        <small class="col-sm-auto">
            Total votes: {{ $voteCount }}
        </small>
    </div>
</div>
@endsection
