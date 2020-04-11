<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\PaymentDetail;

class RegisteredUser extends Model {

    use SoftDeletes;

    protected $table = 'registered_users';
    protected $fillable = [
        'name', 'email', 'password', 'original_password', 'mobile', 'dob', 'area_id', 'gender_id', 'status', 'created_at', 'updated_at'];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $dates = ['deleted_at'];
    
    public function paymentdetail($id) {
        $PaymentDetail = PaymentDetail::
                where(array('subscriber_id'=>$id))
                ->count();
        return $PaymentDetail;        
    }

}
