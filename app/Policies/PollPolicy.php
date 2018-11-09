<?php

namespace App\Policies;

use App\User;
use App\Poll;

class PollPolicy
{
    /**
     * Determine whether the user can update the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function update(User $user, Poll $poll)
    {
        return $user->id === $poll->user_id;
    }

    /**
     * Determine whether the user can delete the poll.
     *
     * @param  \App\User  $user
     * @param  \App\Poll  $poll
     * @return mixed
     */
    public function delete(User $user, Poll $poll)
    {
        return $user->id === $poll->user_id;
    }
}
