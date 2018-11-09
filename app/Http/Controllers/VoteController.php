<?php

namespace App\Http\Controllers;

use App\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'option_id' => 'required|integer|exists:options,id',
        ]);

        $vote = new Vote;
        $vote->option_id = $request->input('option_id');
        $vote->save();

        return redirect()->route('polls.show', $vote->option->poll);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vote $vote)
    {
        $vote->delete();
        return redirect()->route('votes.index');
    }
}
