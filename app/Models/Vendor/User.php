<?php

namespace App\Models\Vendor;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use DB;

class User extends Authenticatable {

    use Notifiable;
    
    protected $table = 'vendor_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'email', 'password', 'original_password', 'civilid', 'mobile', 'permission_id', 'status', 'user_role_id', 'vendor_id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    
    //Check role based page access
    public function hasRolePermission($permission) {
        $role = Auth::guard('vendor')->user()->user_role_id;
        $permission_id = Auth::guard('vendor')->user()->permission_id;

        //Get Permissions
        $user_permission = DB::table('vendor_permissions')
                ->select('permissions')
                ->where('id', $permission_id);

                $count=DB::table('vendor_permissions')->where('id', $permission_id)->count();
        
        if ($role == 1) { //for superadmin
            return true;
        } else {
            //check Permission   
            if($count!=0)         
            if (str_contains($user_permission->first()->permissions, $permission)) {
               return true;
            } else {
                return false;
            }
        }
    }
    
    //Check Superadmin
    public function isSuperAdmin(){
        $user = User::find(Auth::guard('vendor')->user()->id);

            foreach ($user->roles as $r) {
                if($r->id == 1){
                    return true;
                }
                return false;
            }
    }

}
