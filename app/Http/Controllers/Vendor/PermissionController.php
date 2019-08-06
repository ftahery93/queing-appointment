<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Vendor\Permission;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;

class PermissionController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:permissions');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('permissions-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('permissions-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('permissions-edit');

        $Permission = Permission::
                select('id', 'groupname', 'status', 'created_at')
                ->where('vendor_id', VendorDetail::getID())
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Permission)
                            ->editColumn('created_at', function ($Permission) {
                                $newYear = new Carbon($Permission->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($Permission) {
                                return $Permission->status == 1 ? '<div class="label label-success status" sid="' . $Permission->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Permission->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Permission) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Permission->id . '">';
                            })
                            ->editColumn('action', function ($Permission) {
                                if ($this->EditAccess)
                                    return '<a href="' . url($this->configName . '/permissions') . '/' . $Permission->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.permissions.index')
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
        $this->CreateAccess = Permit::AccessPermission('permissions-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Admin Modules
        $admin_modules = DB::table('vendor_admin_modules')
                ->where('status', 1)
                ->orderBy('sort_order', 'ASC')
                ->get();

        //Get all module
        $module1 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 1))
                ->first();

        $module2 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 2))
                ->first();

        $module3 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 3))
                ->first();

        $module4 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 4))
                ->first();


        return view('fitflowVendor.permissions.create')
                        ->with('admin_modules', $admin_modules)
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // validate
        $validator = Validator::make($request->all(), [
                    'groupname' => 'required'
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect($this->configName . '/permissions/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['groupname', 'status']);
            $collection = collect($request->permissions);
            $input['permissions'] = $collection->toJson();
            $input['vendor_id'] = VendorDetail::getID();

            Permission::create($input);

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Permission - ' . $request->groupname, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/permissions');
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
    public function edit(Request $request, $id) {
        $id = $request->id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('permissions-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Get all Admin Modules
        $admin_modules = DB::table('vendor_admin_modules')
                ->where('status', 1)
                ->orderBy('sort_order', 'ASC')
                ->get();

        $Permission = Permission::find($id);
        //Get permissions json value
        $collection = collect(json_decode($Permission->permissions, true));

        //Get all module
        $module1 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 1))
                ->first();

        $module2 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 2))
                ->first();

        $module3 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 3))
                ->first();

        $module4 = DB::table('modules')
                ->select('name_en')
                ->where(array('status' => 1, 'id' => 4))
                ->first();

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.permissions.edit')
                        ->with('Permission', $Permission)
                        ->with('collection', $collection)
                        ->with('admin_modules', $admin_modules)
                        ->with('module1', $module1)
                        ->with('module2', $module2)
                        ->with('module3', $module3)
                        ->with('module4', $module4);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $id = $request->id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('permissions-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Permission = Permission::findOrFail($id);
            $Permission->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Permission = Permission::findOrFail($id);


        // validate
        $validator = Validator::make($request->all(), [
                    'groupname' => 'required'
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect($this->configName . '/permissions/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['groupname', 'status']);
            $collection = collect($request->permissions);
            $input['permissions'] = $collection->toJson();
            $input['vendor_id'] = VendorDetail::getID();
            $Permission->fill($input)->save();

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Permission - ' . $request->groupname, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/permissions');
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
        $this->DeleteAccess = Permit::AccessPermission('permissions-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Permission = Permission::
                select('groupname')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Permission->pluck('groupname');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Permission - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Permission::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/permissions');
    }

}
