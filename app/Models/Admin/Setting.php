<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

  
     public static function getTitle() {
       
            return Setting::select('title')->first();
       
    }
    
}
