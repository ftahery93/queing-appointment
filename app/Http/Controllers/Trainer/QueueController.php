<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Trainer\Queue;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\LogActivity;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TrainerDetail;

class QueueController extends Controller {

    public function __construct() {
        $this->middleware('trainer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {


        $service_id=0;

        $Queue = Queue::
                select('queues.id', 'branches.name_en AS branch_id', 'services.name_en AS service_id', 'queues.starttime')
                ->join('branches', 'branches.id', '=', 'queues.branch_id') 
                ->join('services', 'services.id', '=', 'queues.service_id') 
                ->where('queues.trainer_id', Auth::guard('trainer')->user()->trainer_id);

                if(Auth::guard('trainer')->user()->branch_id!=0){
                    $Queue->where('queues.branch_id', Auth::guard('trainer')->user()->branch_id);
                }
                 
                 if($request->service_id){
                     $service_id =$request->service_id;
                    $Queue->where('queues.service_id', $request->service_id);
                    $Queue->whereRaw('Date(queues.created_at) = CURDATE()');
                 }

                $Queue->get();



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Queue)                                                      
                            ->editColumn('status', function ($Queue) {
                                return $Queue->status == 1 ? '<div class="label label-success status" sid="' . $Queue->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Queue->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Queue) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Queue->id . '">';
                            })
                            // ->editColumn('action', function ($Queue) {
                            //     return '<a href="'.url('trainer/Queues') .'/' . $Queue->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a> '
                            //     .'<a href="'.url('trainer/Queues') .'/' . $Queue->id . '/view" class="btn btn-warning tooltip-warning btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Records"><i class="entypo-eye"></i></a>';
                            // })
                            ->make();
        }


            //Get all User Role
            $services = DB::table('services')
            ->select('id', 'name_en')
            ->where('status', 1)
            ->where('isQueue', 1)
            ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
            ->get();

        return view('trainer.queues.index')->with('service_id',$service_id)->with('services',$services);
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
        ->where('isQueue', 1)
        ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
        ->get();


        return view('trainer.queues.create')->with('branches', $branches)
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
                    'starttime' => 'required',
        ],$messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect('trainer/queues/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;          
          


            Queue::create($input);

            //logActivity
            LogActivity::addToLog('Queue - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('trainer/queues');
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

        $Queue = Queue::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.queues.edit')
                        ->with('Queue', $Queue);
    }

    public function view($id) {

        $Queue = Queue::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.queues.view')
                        ->with('Queue', $Queue);
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
            $Queue = Queue::findOrFail($id);
            $Queue->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Queue = Queue::findOrFail($id);
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
            return redirect('trainer/Queues/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;
            
           
           
            $Queue->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Queue - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('trainer/Queues');
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
        //LogActivity::addToLog('Queue - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
          
            Queue::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('trainer/Queues');
    }
    

}
