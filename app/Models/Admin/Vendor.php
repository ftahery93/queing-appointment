<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\Admin\PaymentDetail;

class Vendor extends Model {
    
    use SoftDeletes,
        Notifiable;
    
    protected $guarded = ['password_confirmation', 'uploaded_image_removed', 'uploaded_image_removed_estore'];

  
  protected $hidden = [
        'password', 'remember_token',
    ];

  protected $dates = ['deleted_at'];
  
  public function paymentdetail($id) {
        $PaymentDetail = PaymentDetail::
                where(array('vendor_id'=>$id))
                ->count();
        return $PaymentDetail;        
    }
}
