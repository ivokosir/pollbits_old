<?php

namespace App\Http\Controllers;

use App\Poll;
use App\Option;
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
        $this->middleware('auth')->except('index', 'create', 'show', 'store', 'results');
        $this->middleware('can:update,poll')->only('edit', 'update');
        $this->middleware('can:delete,poll')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $polls = Poll::latest()->paginate(10);
        return view('polls.index', ['polls' => $polls]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('polls.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|string|max:255',
            'type' => [
                'required',
                'string',
                Rule::in(['fptp', 'approval', 'score']),
            ],
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|distinct|max:255',
        ]);

        $poll = new Poll;
        $poll->type = $request->input('type');
        $poll->question = $request->input('question');
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
     * Display the specified resource.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function show(Poll $poll)
    {
        return view('polls.show', ['poll' => $poll]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function edit(Poll $poll)
    {
        return view('polls.edit', ['poll' => $poll]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poll $poll)
    {
        $this->validate($request, [
            'question' => 'required|string|max:255',
            'options' => 'required|array|size:' . sizeof($poll->options),
            'options.*' => 'required|string|distinct|max:255',
        ]);

        $poll->question = $request->input('question');
        $poll->save();

        foreach ($poll->options as $i => $option) {
            $option->text = $request->input('options')[$i];
            $option->save();
        }

        return redirect()->route('polls.show', $poll);
    }

    /**
     * Remove the specified resource from storage.
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
     * Display poll results.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function results(Poll $poll)
    {
        $voteCount = $poll->votes()->count();

        $results =
            DB::table('options')
                ->leftJoin('scores', 'options.id', '=', 'scores.option_id')
                ->where('options.poll_id', '=', $poll->id)
                ->groupBy('options.id')
                ->selectRaw('options.text as text, IFNULL(SUM(scores.score), 0) as score, IFNULL(SUM(scores.score) / ?, 0) as average', [$voteCount])
                ->orderBy('average', 'desc')
                ->get();

        return view('polls.results', [
            'poll' => $poll,
            'voteCount' => $voteCount,
            'results' => $results,
        ]);
    }
}
