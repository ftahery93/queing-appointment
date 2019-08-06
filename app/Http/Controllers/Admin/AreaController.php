<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\Area;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class AreaController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:master');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('master-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('master-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('master-edit');

        $Area = Area::
                select('areas.id', 'governorates.name_en AS gname', 'areas.name_en', 'areas.status', 'areas.created_at')
                ->join('governorates', 'areas.governorate_id', '=', 'governorates.id')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Area)
                            ->editColumn('created_at', function ($Area) {
                                $newYear = new Carbon($Area->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($Area) {
                                return $Area->status == 1 ? '<div class="label label-success status" sid="' . $Area->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Area->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Area) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Area->id . '">';
                            })
                            ->editColumn('action', function ($Area) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/areas') . '/' . $Area->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->editColumn('governorate', function ($Area) {
                                return $Area->gname;
                            })
                            ->make();
        }

        return view('admin.masters.areas.index')
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
        $this->CreateAccess = Permit::AccessPermission('master-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all Goevenorates
        $governorates = DB::table('governorates')
                ->select('id AS gid', 'name_en')
                ->where('status', 1)
                ->get();
        return view('admin.masters.areas.create', compact('governorates'));
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
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'governorate_id' => 'required',
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/areas/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            Area::create($input);

            //logActivity
            LogActivity::addToLog('Area - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/areas');
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
        $this->EditAccess = Permit::AccessPermission('master-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Get all Goevenorates
        $governorates = DB::table('governorates')
                ->select('id AS gid', 'name_en')
                ->where('status', 1)
                ->get();

        $Area = Area::find($id);


        // show the edit form and pass the nerd
        return View::make('admin.masters.areas.edit')
                        ->with('Area', $Area)
                        ->with('governorates', $governorates);
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
        $this->EditAccess = Permit::AccessPermission('master-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Area = Area::findOrFail($id);
            $Area->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Area = Area::findOrFail($id);
        // validate
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'governorate_id' => 'required',
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/areas/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
            $Area->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Area - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/areas');
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
        $this->DeleteAccess = Permit::AccessPermission('master-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Area = Area::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Area->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('Area - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Area::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/areas');
    }

}
