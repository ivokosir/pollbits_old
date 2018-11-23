<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    public function user() {
        return $this->belongsTo('App\User');
    }

    public function options() {
        return $this->hasMany('App\Option');
    }

    public function votes() {
        return $this->hasMany('App\Vote');
    }

    public function owned(?User $user) {
        return ($user ? $user->id === $this->user_id : false);
    }

    public function canSeeResults(?User $user) {
        $hidden = ($this->results_hidden and !$this->closed);
        return (!$hidden or $this->owned($user));
    }
}
