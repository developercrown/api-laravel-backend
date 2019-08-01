<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';

    //Relaciòn de muchos a uno
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }


}
