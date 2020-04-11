<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model {

    use SoftDeletes, Notifiable;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

}
