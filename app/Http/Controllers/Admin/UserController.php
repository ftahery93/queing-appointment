<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\User;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class UserController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:users');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check View Access Permission
//        $this->ViewAccess = Permit::AccessPermission('users-view');
//        if (!$this->ViewAccess)
//            return redirect('errors/401');
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('users-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('users-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('users-edit');


        $User = User::
                select('users.id', 'users.username', 'users.email', 'permissions.groupname', 'users.status', 'users.created_at')
                ->join('permissions', 'permissions.id', '=', 'users.permission_id')
                ->whereNotIn('users.id', [1])
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($User)
                            ->editColumn('created_at', function ($User) {
                                $newYear = new Carbon($User->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($User) {
                                return $User->status == 1 ? '<div class="label label-success status" sid="' . $User->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $User->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($User) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $User->id . '">';
                            })
                            ->editColumn('action', function ($User) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/users') . '/' . $User->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->editColumn('permission', function ($User) {
                                return $User->groupname;
                            })
                            ->make();
        }

        return view('admin.users.index')
                        ->with('CreateAccess', $this->CreateAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('EditAccess', $this->EditAccess);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('users-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all User Role
        $userroles = DB::table('user_role')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNotIn('id', [1])
                ->get();

        //Get all Permissions
        $permissions = DB::table('permissions')
                ->select('id', 'groupname')
                ->where('status', 1)
                ->get();
        return view('admin.users.create', compact('permissions'))->with('userroles', $userroles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {


        // validate
        $validator = Validator::make($request->only(['name', 'username', 'email', 'password', 'password_confirmation', 'permission_id', 'user_role_id', 'mobile']), [
                    'name' => 'required',
                    'username' => 'required|alpha_dash|unique:users',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6|confirmed',
                    'permission_id' => 'required',
                    'user_role_id' => 'required',
                    'mobile' => 'sometimes|numeric|digits:8',
        ]);



        // validation failed
        if ($validator->fails()) {

            return redirect('admin/users/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name', 'username', 'email', 'permission_id', 'mobile', 'status', 'user_role_id']);
            $input = $request->except(['password_confirmation']);
            $input['original_password'] = $request->password;
            $input['password'] = bcrypt($request->password);

            User::create($input);

            //logActivity
            LogActivity::addToLog('User - ' . $request->username, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/users');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('users-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Get all User Role
        $userroles = DB::table('user_role')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNotIn('id', [1])
                ->get();

        //Get all Permissions
        $permissions = DB::table('permissions')
                ->select('id', 'groupname')
                ->where('status', 1)
                ->get();

        $User = User::find($id);

        // show the edit form and pass the nerd
        return View::make('admin.users.edit')
                        ->with('User', $User)
                        ->with('permissions', $permissions)
                        ->with('userroles', $userroles);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('users-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $User = User::findOrFail($id);
            $User->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $User = User::findOrFail($id);
        // validate
        $validator = Validator::make($request->only(['name', 'username', 'email', 'permission_id', 'user_role_id','mobile', 'password', 'password_confirmation']), [
                    'name' => 'required',
                    'username' => 'required|alpha_dash|unique:users,username,' . $id,
                    'email' => 'required|unique:users,email,' . $id,
                    'permission_id' => 'required',
                    'user_role_id' => 'required',
                    'mobile' => 'sometimes|numeric|digits:8',
                    'password' => 'sometimes|min:6|confirmed'
        ]);

       
        // validation failed
        if ($validator->fails()) {
            return redirect('admin/users/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name', 'username', 'email', 'password', 'permission_id', 'mobile', 'status', 'user_role_id']);
            $input = $request->except(['password_confirmation']);

            if ($request->has('password')) {
                $input['password'] = bcrypt($request->password);
                $input['original_password'] = $request->password;
            } else {
                $input = $request->except(['password']);
            }

            $User->fill($input)->save();

            //logActivity
            LogActivity::addToLog('User - ' . $request->username, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/users');
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroyMany(Request $request) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('users-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $User = User::
                select('username')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $User->pluck('username');
        $groupname = $name->toJson();

        LogActivity::addToLog('User - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            User::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/users');
    }

}
