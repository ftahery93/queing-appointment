<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        //insert some dummy records
//         DB::table('governorates')->insert(array(
//             array('name_en'=>'Al Asimah Governorate (Capital)','name_ar'=>'Al Asimah Governorate (Capital)','status'=>1,'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')),
//             array('name_en'=>'Hawalli Governorate','name_ar'=>'Hawalli Governorate','status'=>1,'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')),
//            array('name_en'=>'Farwaniya Governorate','name_ar'=>'Farwaniya Governorate','status'=>1,'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')),
//             array('name_en'=>'Mubarak Al-Kabeer Governorate','name_ar'=>'Mubarak Al-Kabeer Governorate','status'=>1,'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')),
//             array('name_en'=>'Ahmadi Governorate','name_ar'=>'Ahmadi Governorate','status'=>1,'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')),
//             array('name_en'=>'Jahra Governorate','name_ar'=>'Jahra Governorate','status'=>1,'created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at'=>Carbon::now()->format('Y-m-d H:i:s')),
//          ));
        //insert some dummy records in orders and related tables
        $insert = DB::table('orders')->insert(array(
            array('vendor_id' => 2, 'customer_id' => 1, 'name' => 'Noor', 'email' => 'noor@gmail.com',
                'mobile' => '99696989', 'area_id' => '1', 'gender_id' => '1',
                'dob' => '1999-01-01', 'total' => '11.000', 'order_status_id' => 1,
                'commission' => '2.000', 'profit' => '9.000', 'pick_from_store' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')),
        ));

        DB::table('order_history')->insert(array(
            array('order_id' => 1, 'order_status_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')),
        ));
        DB::table('order_product')->insert(array(
            array('order_id' => 1, 'product_id' => 1, 'name_en' => 'Energy Drink', 'name_ar' => 'Energy Drink', 'description_en' => 'Energy Drink'
                , 'description_ar' => 'Energy Drink', 'model' => 1, 'location' => 1, 'quantity' => 1, 'price' => '10.000', 'total' => '11.000'
               ,'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')),
        ));
        DB::table('order_total')->insert(array(
            array('order_id' => 1, 'sub_total' => '11.000', 'delivery_charge' => '2.000', 'total' => '13.000',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')),
        ));
        DB::table('products')->where('id', 1)->update(['quantity' => 2, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);

        DB::table('product_option_value')->where('product_option_value_id', 1)->update(['quantity' => 0, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
    }

}
