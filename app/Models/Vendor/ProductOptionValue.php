<?php

namespace App\Models\Vendor;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor\OptionValue;

class ProductOptionValue extends Model {

    protected $table = 'product_option_value';
    protected $primaryKey = "product_option_value_id";
    protected $guarded = [];

    public static function getoptionValueName($option_value_id) {

        $optionValue = OptionValue::select('name_en')->where('id', $option_value_id)->first();

        return $optionValue->name_en;
    }

}
