<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class ClassPackage extends Model {

  protected $fillable = [
       'vendor_id','has_offer', 'name_en', 'name_ar', 'num_points', 'unlimited', 'price', 'num_days', 'description_en', 'description_ar', 'status', 'expired_notify_duration', 'created_at', 'updated_at'];

}
