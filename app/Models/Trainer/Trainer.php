<?php

namespace App\Models\Trainer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Models\Trainer\PaymentDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Trainer extends Authenticatable {

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
