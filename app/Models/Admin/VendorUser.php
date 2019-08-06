<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class VendorUser extends Model {
    
    protected $guarded = ['deleted_at', 'acc_name', 'acc_num', 'ibn_num','modules','password_confirmation','profile_image','commission','uploaded_image_removed'];

  
  protected $hidden = [
        'password', 'remember_token',
    ];
 
}
