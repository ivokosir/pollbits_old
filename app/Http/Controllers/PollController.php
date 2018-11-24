<?php

namespace App\Http\Controllers;

use App\Poll;
use App\Option;
use App\Vote;
use App\Score;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'create', 'show', 'store', 'results', 'resultsCSV');
        $this->middleware('can:owned,poll')->only('edit', 'update', 'destroy');
        $this->middleware('can:results,poll')->only('results', 'resultsCSV');
    }

    /**
     * Display a listing of polls.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => 'string|max:250',
        ]);

        $search = $request->input('search');

        $polls = Poll::latest()
                     ->where('question', 'like', '%'.$search.'%')
                     ->where('private', false)
                     ->paginate(10);

        return view('polls.index', ['polls' => $polls, 'search' => $search]);
    }

    /**
     * Display a listing of owned polls.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function owned(Request $request)
    {
        $this->validate($request, [
            'search' => 'string|max:250',
        ]);

        $search = $request->input('search');

        $polls = Poll::latest()
                     ->where('question', 'like', '%'.$search.'%')
                     ->where('user_id', auth()->user()->id)
                     ->paginate(10);

        return view('polls.index', ['polls' => $polls]);
    }

    /**
     * Show the form for creating a new poll.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('polls.create');
    }

    /**
     * Store a newly created poll in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|string|max:250',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:250',
            'type' => [
                'required',
                'string',
                Rule::in(['fptp', 'approval', 'score']),
            ],
        ]);

        $poll = new Poll;
        $poll->type = $request->input('type');
        $poll->question = $request->input('question');
        $poll->results_hidden = $request->input('results_hidden') ? true : false;
        $poll->private = $request->input('private') ? true : false;
        $poll->closed = false;
        $poll->user_id = auth()->user()->id ?? null;
        $poll->save();

        foreach ($request->input('options') as $text) {
            $option = new Option;
            $option->text = $text;
            $option->poll_id = $poll->id;
            $option->save();
        }

        return redirect()->route('polls.show', $poll);
    }

    /**
     * Display the specified poll.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Poll $poll)
    {
        if ($poll->closed) {
            return redirect()->route('polls.results', $poll);
        }

        $vote = Vote::where('ip', $request->ip())->where('poll_id', $poll->id)->first();

        if ($vote) {
            $options = DB::table('options')
                         ->leftJoin('scores', function ($join) use ($vote) {
                             $join->on('scores.option_id', '=', 'options.id')
                                  ->where('scores.vote_id', $vote->id);
                         })
                         ->where('options.poll_id', $poll->id)
                         ->selectRaw('options.text AS text, IFNULL(scores.score, 0) score')
                         ->get();
        } else {
            $options = $poll->options;
        }

        $user = auth()->user();
        $owned = $poll->owned($user);
        $canSeeResults = $poll->canSeeResults($user);

        return view('polls.show', [
            'poll' => $poll,
            'options' => $options,
            'vote' => $vote,
            'voted' => isset($vote),
            'owned' => $owned,
            'canSeeResults' => $canSeeResults,
        ]);
    }

    /**
     * Show the form for editing the specified poll.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function edit(Poll $poll)
    {
        return view('polls.edit', ['poll' => $poll]);
    }

    /**
     * Update the specified poll in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poll $poll)
    {
        $this->validate($request, [
            'question' => 'required|string|max:250',
            'options' => 'required|array|size:' . sizeof($poll->options),
            'options.*' => 'required|string|max:250',
        ]);

        $poll->question = $request->input('question');
        $poll->results_hidden = $request->input('results_hidden') ? true : false;
        $poll->private = $request->input('private') ? true : false;
        $poll->save();

        foreach ($poll->options as $i => $option) {
            $option->text = $request->input('options')[$i];
            $option->save();
        }

        return redirect()->route('polls.show', $poll);
    }

    /**
     * Remove the specified poll from storage.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poll $poll)
    {
        $poll->delete();
        return redirect()->route('polls.index');
    }

    /**
     * open the specified poll.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function open(Poll $poll)
    {
        $poll->closed = false;
        $poll->save();
        return redirect()->route('polls.show', $poll);
    }

    /**
     * Close the specified poll.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function close(Poll $poll)
    {
        $poll->closed = true;
        $poll->save();
        return redirect()->route('polls.results', $poll);
    }

    /**
     * Display poll results.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function results(Poll $poll)
    {
        $voteCount = $poll->votes()->count();

        $results = DB::table('options')
                     ->leftJoin('scores', 'scores.option_id', '=', 'options.id')
                     ->where('options.poll_id', '=', $poll->id)
                     ->groupBy('options.id')
                     ->selectRaw('options.text as text, IFNULL(SUM(scores.score), 0) as score')
                     ->orderBy('score', 'desc')
                     ->orderBy('options.id')
                     ->get();

        $owned = $poll->owned(auth()->user());

        return view('polls.results', [
            'poll' => $poll,
            'voteCount' => $voteCount,
            'results' => $results,
            'owned' => $owned,
        ]);
    }

    /**
     * Display poll results in CSV.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function resultsCSV(Poll $poll)
    {
        $voteCount = $poll->votes()->count();

        $results = DB::table('options')
                     ->leftJoin('scores', 'scores.option_id', '=', 'options.id')
                     ->where('options.poll_id', '=', $poll->id)
                     ->groupBy('options.id')
                     ->selectRaw('options.text as text, IFNULL(SUM(scores.score), 0) as score')
                     ->orderBy('score', 'desc')
                     ->orderBy('options.id')
                     ->get();

        $owned = $poll->owned(auth()->user());

        $rows = [
            [$poll->question],
            ['Total Votes', $voteCount],
        ];

        switch ($poll->type) {
            case 'approval':
            case 'fptp':
                $rows[] = ['Option', 'Fraction', 'Votes'];
                break;
            case 'score':
                $rows[] = ['Option', 'Average', 'Score'];
                break;
        }

        foreach ($results as $result) {
            $average = $voteCount ? $result->score / $voteCount : 0;
            $rows[] = [$result->text, $average, $result->score];
        }

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'results.csv', ['Content-Type' => 'text/csv']);
    }
}
