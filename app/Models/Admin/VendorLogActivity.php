<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class VendorLogActivity extends Model {

  protected $fillable = [
        'subject', 'url', 'method', 'ip', 'agent', 'user_id', 'user_type', 'vendor_id', 'trainer_id', 'created_at', 'updated_at'];
  
  
}
