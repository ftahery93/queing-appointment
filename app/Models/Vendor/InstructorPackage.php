<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class InstructorPackage extends Model {

    protected $fillable = [
        'vendor_id', 'name_en', 'name_ar', 'price',  'description_en', 'description_ar', 'status',
        'created_at', 'updated_at', 'branch_id', 'has_offer', 'num_points'];
    
    protected $guarded = ['num_days', 'expired_notify_duration'];

}
