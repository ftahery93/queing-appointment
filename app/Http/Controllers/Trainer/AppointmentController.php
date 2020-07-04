<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Trainer\Appointment;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\LogActivity;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TrainerDetail;

class AppointmentController extends Controller {

    public function __construct() {
        $this->middleware('trainer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $Appointment = Appointment::
                select('appointments.id', 'branches.name_en AS branch_id', 'services.name_en AS service_id', 'appointments.slot1', 
                'appointments.slot2','appointments.time_interval','appointments.num_persons')
                ->join('branches', 'branches.id', '=', 'appointments.branch_id') 
                ->join('services', 'services.id', '=', 'appointments.service_id') 
                ->where('appointments.trainer_id', Auth::guard('trainer')->user()->trainer_id);
              
                if(Auth::guard('trainer')->user()->branch_id!=0)
                 $Appointment->where('appointments.branch_id', Auth::guard('trainer')->user()->branch_id);

                $Appointment->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Appointment)                                                      
                            ->editColumn('status', function ($Appointment) {
                                return $Appointment->status == 1 ? '<div class="label label-success status" sid="' . $Appointment->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Appointment->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Appointment) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Appointment->id . '">';
                            })
                            // ->editColumn('action', function ($Appointment) {
                            //     return '<a href="'.url('trainer/appointments') .'/' . $Appointment->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a> '
                            //     .'<a href="'.url('trainer/appointments') .'/' . $Appointment->id . '/view" class="btn btn-warning tooltip-warning btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Records"><i class="entypo-eye"></i></a>';
                            // })
                            ->make();
        }

        return view('trainer.appointments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if(Auth::guard('trainer')->user()->branch_id!=0){
            $branches = DB::table('branches')
          ->select('id', 'name_en')
          ->where('status', 1)
          ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
          ->where('id', Auth::guard('trainer')->user()->branch_id)
          ->get();
          }
          else{
             //Get all User Role
          $branches = DB::table('branches')
          ->select('id', 'name_en')
          ->where('status', 1)
          ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
          ->get();
          }         




        //Get all User Role
        $services = DB::table('services')
        ->select('id', 'name_en')
        ->where('status', 1)
        ->where('isAppointment', 1)
        ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
        ->get();


        return view('trainer.appointments.create')->with('branches', $branches)
        ->with('services', $services);
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
                    'branch_id' => 'required',
                    'service_id' => 'required',
                    'slot1' => 'required',
                    'slot2' => 'required',
                    'num_persons' => 'required',
                    'time_interval' => 'required',
        ],$messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect('trainer/appointments/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;          
          


            Appointment::create($input);

            //logActivity
            LogActivity::addToLog('Appointment - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('trainer/appointments');
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

        $Appointment = Appointment::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.appointments.edit')
                        ->with('Appointment', $Appointment);
    }

    public function view($id) {

        $Appointment = Appointment::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.appointments.view')
                        ->with('Appointment', $Appointment);
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
            $Appointment = Appointment::findOrFail($id);
            $Appointment->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Appointment = Appointment::findOrFail($id);
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
            return redirect('trainer/appointments/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;
            
           
           
            $Appointment->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Appointment - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('trainer/appointments');
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
        //LogActivity::addToLog('Appointment - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
          
            Appointment::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('trainer/appointments');
    }

}
