<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\Permission;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class PermissionController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:permissions');
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
                //->orderBy('created_at','ASC')
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
                                    return '<a href="'.url('admin/permissions') .'/' . $Permission->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('admin.permissions.index')
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
        $admin_modules = DB::table('admin_modules')
                ->where('status', 1)
                ->orderBy('sort_order', 'ASC')
                ->get();


        return view('admin.permissions.create')
                        ->with('admin_modules', $admin_modules);
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

            return redirect('admin/permissions/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['groupname', 'status']);
            $collection = collect($request->permissions);
            $input['permissions'] = $collection->toJson();

            Permission::create($input);

            //logActivity
            LogActivity::addToLog('Permission - ' . $request->groupname, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/permissions');
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
        $this->EditAccess = Permit::AccessPermission('permissions-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Get all Admin Modules
        $admin_modules = DB::table('admin_modules')
                ->where('status', 1)
                ->orderBy('sort_order', 'ASC')
                ->get();

        $Permission = Permission::find($id);
        //Get permissions json value
        $collection = collect(json_decode($Permission->permissions, true));
        // show the edit form and pass the nerd
        return View::make('admin.permissions.edit')
                        ->with('Permission', $Permission)
                        ->with('collection', $collection)
                        ->with('admin_modules', $admin_modules);
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

            return redirect('admin/permissions/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['groupname', 'status']);
            $collection = collect($request->permissions);
            $input['permissions'] = $collection->toJson();
            $Permission->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Permission - ' . $request->groupname, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/permissions');
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

        LogActivity::addToLog('Permission - ' . $groupname, 'deleted');

       $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Permission::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/permissions');
    }

}
