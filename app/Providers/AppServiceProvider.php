<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Helpers\VendorDetail;
use App\Helpers\TrainerDetail;
use Carbon\Carbon;
use DB;
use Cookie;
use Crypt;
use Config;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request) {

//        DB::transaction(function () {
//          //Update Trainer & Vendors subscribed package Status
//        TrainerDetail::setSubscriberPackageStatus();
//        VendorDetail::setSubscriberPackageStatusForAllVendors();
//        }, 5);

        

        $store = '';
        if ($request->hasCookie('code')) {
            $value = Crypt::decrypt(Cookie::get('code'));
            $store = '/' . $value;
        }
        // if you need to access in controller and views:
        Config::set('global.storeAddress', $store);
        Config::set('global.M1', \DB::table('modules')->select('slug')->where(array('status' => 1, 'id' => 1))->first()->slug);
        Config::set('global.M2', \DB::table('modules')->select('slug')->where(array('status' => 1, 'id' => 2))->first()->slug);
        Config::set('global.M3', \DB::table('modules')->select('slug')->where(array('status' => 1, 'id' => 3))->first()->slug);
        Config::set('global.M4', \DB::table('modules')->select('slug')->where(array('status' => 1, 'id' => 4))->first()->slug);
        // Using view composer to set following variables globally
        view()->composer('*', function($view) use($store) {
            //check Guard
            if (Auth::guard('trainer')->check()) {
                $userInfo = Auth::guard('trainer')->user();
                $view->with('userInfo', $userInfo);
                $view->with('trainer_profile_size', 'Image size should be Width 770px x Height 300px');
                $view->with('trainer_profile_WH', 'max-width:500px;max-height:340px');
                //$view->with('subscribers_records_count', DB::table('subscribers_package_details')->where('trainer_id', $userInfo->id)->distinct('subscriber_id')->count());
            } if (Auth::guard('web')->check()) {
                $userInfo = Auth::user();
                $view->with('userInfo', $userInfo);
                $view->with('vendor_profile_size', 'Image size should be Width 770px x Height 300px');
                $view->with('vendor_profile_WH', 'max-width:500px;max-height:340px');
                $view->with('vendor_estore_size', 'Image size should be Width 770px x Height 420px');
                $view->with('vendor_estore_WH', 'max-width:500px;max-height:340px');
                $view->with('trainer_profile_size', 'Image size should be Width 770px x Height 300px');
                $view->with('trainer_profile_WH', 'max-width:500px;max-height:340px');
                $view->with('activity_icon_size', 'Image size should be Width 50 x Height 50');
                $view->with('activity_icon_WH', 'max-width:50px;max-height:50px');
                $view->with('module_icon_size', 'Image size should be Width 50 x Height 50');
                $view->with('module_icon_WH', 'max-width:50px;max-height:50px');
                $view->with('sponsoredAd_image_size', 'Image size should be Width 800 x Height 800');
                $view->with('sponsoredAd_image_WH', 'max-width:800px;max-height:800px');
                // $view->with('vendor_records_count', \App\Models\Admin\Vendor::count());
                //$view->with('registered_users_records_count', \App\Models\Admin\RegisteredUser::count());
                //$view->with('trainer_records_count', \App\Models\Admin\Trainer::count());
                //$view->with('android_users_count', \DB::table('push_registration')->where('mobile_type', '=', 'a')->count());
                // $view->with('ios_users_count', \DB::table('push_registration')->where('mobile_type', '=', 'i')->count());
                //$view->with('total_device_users', \DB::table('push_registration')->where('mobile_type', '=', 'i')->count());
                $view->with('lat_lng', '29.378586, 47.990341');
                //$view->with('pendingClasses', \DB::table('classes')->where('approved_status', 0)->count());
            }
            if (Auth::guard('vendor')->check()) {
                $userInfo = Auth::guard('vendor')->user();
                $view->with('userInfo', $userInfo);
                $view->with('vendor_profile_size', 'Image size should be Width 500px x Height 340px');
                $view->with('vendor_profile_WH', 'max-width:500px;max-height:340px');
                 $view->with('vendor_estore_size', 'Image size should be Width 770px x Height 420px');
                $view->with('vendor_estore_WH', 'max-width:500px;max-height:340px');
                $view->with('product_size', 'Image size should be Width 500px x Height 500px');
                $view->with('product_WH', 'max-width:500px;max-height:500px');
                $view->with('subscribers_records_count', DB::table('subscribers_package_details')->where('trainer_id', $userInfo->id)->distinct('subscriber_id')->count());
                $view->with('currentURL', URL::current());
                $view->with('pathURL', parse_url(URL::current()));
                $view->with('lat_lng', '29.378586, 47.990341');
                $view->with('configM1', config('global.fitflowVendor') . $store . '/' . config('global.M1'));
                $view->with('configM2', config('global.fitflowVendor') . $store . '/' . config('global.M2'));
                $view->with('configM3', config('global.fitflowVendor') . $store . '/' . config('global.M3'));
                $view->with('configM4', config('global.fitflowVendor') . $store . '/' . config('global.M4'));
                $view->with('memberExpired', DB::table(VendorDetail::getPrefix() . 'members')->whereNull('deleted_at')->whereDate('end_date', '<', Carbon::now())->count());
                //$view->with('rejectedClasses', \DB::table('classes')->where('approved_status', 2)->where('vendor_id', VendorDetail::getID())->count());
            }
            $view->with('appTitle', \App\Models\Admin\Setting::getTitle());
            Config::set('global.appTitle', \App\Models\Admin\Setting::getTitle());


            $view->with('configName', config('global.fitflowVendor') . $store);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
