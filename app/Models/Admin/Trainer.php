<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\Admin\PaymentDetail;

class Trainer extends Model {

    use SoftDeletes,
        Notifiable;

    protected $fillable = [
        'username', 'name','name_ar', 'email', 'password', 'original_password', 'mobile', 'status', 'activities', 'commission', 'profile_image', 'acc_name', 'acc_num', 'ibn_num', 'bank_id', 'created_at', 'updated_at','contract_name', 'contract_startdate', 'contract_enddate', 'area', 'gender_type','description_en', 'description_ar'];
    protected $hidden = [
        'password', 'remember_token',
    ];
   
    protected $dates = ['deleted_at'];

    public function paymentdetail($id) {
        $PaymentDetail = PaymentDetail::
                where(array('trainer_id'=>$id))
                ->count();
        return $PaymentDetail;        
    }

}
