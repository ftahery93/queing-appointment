<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Contactus extends Model {

    protected $table = 'contactus';
    
  protected $fillable = [
        'fullname', 'email', 'mobile', 'message'];
}
