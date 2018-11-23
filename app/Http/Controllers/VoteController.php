<?php

namespace App\Http\Controllers;

use App\Vote;
use App\Poll;
use App\Score;
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
        $this->validate($request, ['poll_id' => 'required|integer|exists:polls,id']);

        $poll = Poll::find($request->input('poll_id'));
        if ($poll->closed) {
            return back()->withErrors('The poll is closed.');
        }

        $ip = $request->ip();

        $voted = Vote::where('ip', $ip)->where('poll_id', $poll->id)->exists();
        if ($voted) {
            return back()->withErrors('This IP address already voted.');
        }

        $scores_raw = [];

        switch ($poll->type) {
            case 'approval':
                $this->validate($request, [
                    'option_ids' => 'required|array',
                    'option_ids.*' => 'required|integer|distinct|exists:options,id',
                ]);

                foreach ($request->input('option_ids') as $option_id) {
                    $scores_raw[] = [
                        'option_id' => $option_id,
                        'score' => 1,
                    ];
                }

                break;
            case 'fptp':
                $this->validate($request, [
                    'option_id' => 'required|integer|distinct|exists:options,id',
                ]);

                $scores_raw[] = [
                    'option_id' => $request->input('option_id'),
                    'score' => 1,
                ];

                break;
            case 'score':
                $this->validate($request, [
                    'scores' => 'required|array',
                    'scores.*.option_id' => 'required|integer|distinct|exists:options,id',
                    'scores.*.score' => 'required|integer|min:0|max:5',
                ]);

                foreach ($request->input('scores') as $score) {
                    $scores_raw[] = [
                        'option_id' => $score['option_id'],
                        'score' => $score['score'],
                    ];
                }

                break;
        }

        $vote = new Vote;
        $vote->ip = $ip;
        $vote->poll_id = $poll->id;
        $vote->save();

        foreach ($scores_raw as $score_raw) {
            $score = new Score;
            $score->vote_id = $vote->id;
            $score->option_id = $score_raw['option_id'];
            $score->score = $score_raw['score'];
            $score->save();
        }

        return redirect()->route('polls.results', $vote->poll);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Vote $vote)
    {
        $poll = $vote->poll;

        if($vote->ip !== $request->ip()) {
            return back()->withErrors('IP address has changed.');
        }
        if ($poll->closed) {
            return back()->withErrors('The poll is closed.');
        }

        $vote->delete();

        return redirect()->route('polls.show', $poll);
    }
}
