<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use DB;

class Permit {

    public static function AccessPermission($permission_type) {
        if (Auth::guard('vendor')->check()) {
            $permission_id = Auth::guard('vendor')->user()->permission_id;
            $role = Auth::guard('vendor')->user()->user_role_id;
            //Get Permissions
            $user_permission = DB::table('vendor_permissions')
                    ->select('permissions')
                    ->where('id', $permission_id);
        } else {
            $permission_id = Auth::user()->permission_id;
            $role = Auth::user()->user_role_id;
            //Get Permissions
            $user_permission = DB::table('permissions')
                    ->select('permissions')
                    ->where('id', $permission_id);
        }


        if ($role == 1) { //for superadmin
            return true;
        } else {

            //check Permission
            if (str_is('*' . $permission_type . '*', $user_permission->first()->permissions)) {
                return true;
            } else {
                return false;
            }
        }
    }

}
