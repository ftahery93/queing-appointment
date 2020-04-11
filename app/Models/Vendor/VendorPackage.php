<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;

class VendorPackage extends Model {

    protected $fillable = [
        'vendor_id', 'name_en', 'name_ar', 'price', 'num_days', 'description_en', 'description_ar', 'status', 'expired_notify_duration',
        'created_at', 'updated_at', 'branch_id', 'has_offer'];

}
