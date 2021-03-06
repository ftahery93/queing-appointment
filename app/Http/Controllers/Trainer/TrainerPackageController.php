<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Trainer\TrainerPackage;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\LogActivity;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TrainerDetail;

class TrainerPackageController extends Controller {

    public function __construct() {
        $this->middleware('trainer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $TrainerPackage = TrainerPackage::
                select('id', 'name_en', 'latitude', 'longitude', 'working_hours', 'status', 'created_at')
                 ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($TrainerPackage)
                            ->editColumn('created_at', function ($TrainerPackage) {
                                $newYear = new Carbon($TrainerPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })                           
                            ->editColumn('status', function ($TrainerPackage) {
                                return $TrainerPackage->status == 1 ? '<div class="label label-success status" sid="' . $TrainerPackage->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $TrainerPackage->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($TrainerPackage) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $TrainerPackage->id . '">';
                            })
                            ->editColumn('action', function ($TrainerPackage) {
                                return '<a href="'.url('trainer/branches') .'/' . $TrainerPackage->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a> '
                                .'<a href="'.url('trainer/branches') .'/' . $TrainerPackage->id . '/view" class="btn btn-warning tooltip-warning btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Records"><i class="entypo-eye"></i></a>';
                            })
                            ->make();
        }

        return view('trainer.branches.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('trainer.branches.create');
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
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'working_hours' => 'required',
                    'address' => 'required',
        ],$messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect('trainer/branches/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;          
          


            TrainerPackage::create($input);

            //logActivity
            LogActivity::addToLog('TrainerPackage - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('trainer/branches');
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

        $TrainerPackage = TrainerPackage::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.branches.edit')
                        ->with('TrainerPackage', $TrainerPackage);
    }

    public function view($id) {

        $TrainerPackage = TrainerPackage::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.branches.view')
                        ->with('TrainerPackage', $TrainerPackage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //Ajax request
        if (request()->ajax()) {
            $TrainerPackage = TrainerPackage::findOrFail($id);
            $TrainerPackage->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $TrainerPackage = TrainerPackage::findOrFail($id);
        // validate
        Validator::extend('less_than', function($attribute, $value, $parameters) {
            $other = Input::get($parameters[0]);

            return isset($other) and intval($value) < intval($other);
        });
        $messsages = array(
		'expired_notify_duration.less_than' => config('global.lessthanValidate'),
	);
       
        $validator = Validator::make($request->all(), [
            'name_en' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'working_hours' => 'required',
            'address' => 'required',
        ],$messsages);
        

        // validation failed
        if ($validator->fails()) {
            return redirect('trainer/branches/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;
            
           
           
            $TrainerPackage->fill($input)->save();

            //logActivity
            LogActivity::addToLog('TrainerPackage - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('trainer/branches');
        }
    }

    /**
     * Remove the Multiple resource from storage.
     *
     * @param  int  $ids
     * @return \Illuminate\Http\Response
     */
    public function destroyMany(Request $request) {

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $TrainerPackage = TrainerPackage::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $TrainerPackage->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('TrainerPackage - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
          
            TrainerPackage::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('trainer/branches');
    }

}
