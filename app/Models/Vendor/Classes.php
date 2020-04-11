<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model {
    
    protected $table = 'classes';

   protected $guarded = ['geo_address'];

}
