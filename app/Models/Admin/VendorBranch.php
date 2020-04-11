<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class VendorBranch extends Model {
    
    protected $guarded = ['contact_person_en,contact_person_ar'];
}
