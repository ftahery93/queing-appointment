<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\PaymentMode;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class PaymentModeController extends Controller {

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

        $PaymentMode = PaymentMode::
                select('id', 'name_en', 'status', 'created_at')
                //->orderBy('created_at','ASC')
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($PaymentMode)
                            ->editColumn('created_at', function ($PaymentMode) {
                                $newYear = new Carbon($PaymentMode->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($PaymentMode) {
                                return $PaymentMode->status == 1 ? '<div class="label label-success status" sid="' . $PaymentMode->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $PaymentMode->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($PaymentMode) {
                                if ($this->EditAccess)
                                    return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $PaymentMode->id . '">';
                            })
                            ->editColumn('action', function ($PaymentMode) {
                                return '<a href="'.url('admin/paymentModes') .'/' . $PaymentMode->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('admin.masters.paymentModes.index')
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

        return view('admin.masters.paymentModes.create');
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
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/paymentModes/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            PaymentMode::create($input);

            //logActivity
            LogActivity::addToLog('PaymentMode - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/paymentModes');
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

        $PaymentMode = PaymentMode::find($id);

        // show the edit form and pass the nerd
        return View::make('admin.masters.paymentModes.edit')
                        ->with('PaymentMode', $PaymentMode);
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
            $PaymentMode = PaymentMode::findOrFail($id);
            $PaymentMode->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $PaymentMode = PaymentMode::findOrFail($id);
        // validate
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/paymentModes/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
            $PaymentMode->fill($input)->save();

            //logActivity
            LogActivity::addToLog('PaymentMode - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/paymentModes');
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
        $PaymentMode = PaymentMode::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $PaymentMode->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('PaymentMode - ' . $groupname, 'deleted');
        
        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            PaymentMode::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/paymentModes');
    }

}
