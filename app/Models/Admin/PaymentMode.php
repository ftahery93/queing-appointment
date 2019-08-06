<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    protected $fillable = ['name_en', 'name_ar', 'status', 'created_at', 'updated_at'];
}