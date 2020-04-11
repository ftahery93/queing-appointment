<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\OrderProductOption;

class OrderProduct extends Model {

    protected $table = 'order_product';
    protected $guarded = [];
    
//    public function orderproductOption()
//    {
//        return $this->hasMany(OrderProductOption::class,'product_id');
//    }

    
    public static function orderproductOptionValue($id)
    {
        $options=OrderProductOption::where('order_product_id',$id)->get();
        return $options;
    }

    

}
