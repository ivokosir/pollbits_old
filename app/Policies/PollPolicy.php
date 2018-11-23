<?php

namespace App\Policies;

use App\User;
use App\Poll;

class PollPolicy
{
    /**
     * Determine whether the poll is owned by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function owned(?User $user, Poll $poll)
    {
        return $poll->owned($user);
    }

    /**
     * Determine whether the user can see poll results.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function results(?User $user, Poll $poll)
    {
        return $poll->canSeeResults($user);
    }
}
