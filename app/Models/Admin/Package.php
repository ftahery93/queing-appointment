<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Package extends Model {

  protected $fillable = [
        'name_en', 'name_ar', 'num_points', 'unlimited', 'price', 'num_days', 'description_en', 'description_ar', 
      'status', 'expired_notify_duration', 'created_at', 'updated_at', 'has_offer'];

}
