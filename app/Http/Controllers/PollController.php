<?php

namespace App\Http\Controllers;

use App\Poll;
use App\Option;
use Illuminate\Http\Request;

class PollController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'create', 'show', 'store');
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
        $polls = Poll::orderBy('created_at','desc')->paginate(10);
        return view('polls.index')->with('polls', $polls);
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
            'option' => 'required|array|min:2',
            'option.*' => 'required|string|distinct|max:255',
        ]);

        $poll = new Poll;
        $poll->question = $request->input('question');
        $poll->user_id = auth()->user()->id ?? null;
        $poll->save();

        foreach ($request->input('option') as $text) {
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
        return view('polls.show')->with('poll', $poll);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function edit(Poll $poll)
    {
        return view('polls.edit')->with('poll', $poll);
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
            'option' => 'required|array|size:' . sizeof($poll->options),
            'option.*' => 'required|string|distinct|max:255',
        ]);

        $poll->question = $request->input('question');
        $poll->save();

        foreach ($poll->options as $i => $option) {
            $option->text = $request->input('option')[$i];
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
}
