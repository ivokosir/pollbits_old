<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public $timestamps = false;

    public function poll() {
        return $this->belongsTo('App\Poll');
    }

    public function votes() {
        return $this->hasMany('App\Vote');
    }
}
