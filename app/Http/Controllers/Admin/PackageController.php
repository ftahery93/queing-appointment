<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\Package;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class PackageController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:packages');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('packages-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('packages-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('packages-edit');


        $Package = Package::
                select('id', 'name_en', 'num_points', 'num_days', 'price', 'status', 'created_at')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Package)
                            ->editColumn('created_at', function ($Package) {
                                $newYear = new Carbon($Package->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('num_points', function ($Package) {
                                return $Package->num_points == 0 ? 'Unlimited' : $Package->num_points;
                            })
                            ->editColumn('status', function ($Package) {
                                return $Package->status == 1 ? '<div class="label label-success status" sid="' . $Package->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Package->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Package) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Package->id . '">';
                            })
                            ->editColumn('action', function ($Package) {
                                if ($this->EditAccess)
                                    return '<a href="'.url('admin/packages') .'/' . $Package->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('admin.packages.index')
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
        $this->CreateAccess = Permit::AccessPermission('packages-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        return view('admin.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate
        Validator::extend('less_than', function($attribute, $value, $parameters) {
            $other = Input::get($parameters[0]);

            return isset($other) and intval($value) < intval($other);
        });
      $messsages = array(
		'expired_notify_duration.less_than' => config('global.lessthanValidate'),
	);
        $validator = Validator::make($request->only(['name_en', 'name_ar', 'num_points', 'price', 'num_days', 'expired_notify_duration']), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'num_points' => 'required|numeric',
                    'num_days' => 'required|numeric',
                    'expired_notify_duration' => 'required|numeric',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
        ],$messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/packages/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

            Package::create($input);

            //logActivity
            LogActivity::addToLog('Package - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/packages');
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
        $this->EditAccess = Permit::AccessPermission('packages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Package = Package::find($id);

        // show the edit form and pass the nerd
        return View::make('admin.packages.edit')
                        ->with('Package', $Package);
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
        $this->EditAccess = Permit::AccessPermission('packages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Package = Package::findOrFail($id);
            $Package->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Package = Package::findOrFail($id);
        // validate
        Validator::extend('less_than', function($attribute, $value, $parameters) {
            $other = Input::get($parameters[0]);

            return isset($other) and intval($value) < intval($other);
        });
        $messsages = array(
		'expired_notify_duration.less_than' => config('global.lessthanValidate'),
	);
        $validator = Validator::make($request->only(['name_en', 'name_ar', 'num_points', 'price', 'num_days', 'expired_notify_duration']), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'num_points' => 'required|numeric',
                    'num_days' => 'required|numeric',
                    'expired_notify_duration' => 'required|numeric',
                    'price' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
        ],$messsages);


        // validation failed
        if ($validator->fails()) {
            return redirect('admin/packages/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            $Package->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Package - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/packages');
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
        $this->DeleteAccess = Permit::AccessPermission('packages-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Package = Package::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Package->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('Package - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Package::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/packages');
    }

}
