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
            'username', 'name', 'email', 'password', 'original_password', 'civilid', 'mobile', 'branch_id', 'status', 'trainer_id'];
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
