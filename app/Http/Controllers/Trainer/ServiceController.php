<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Trainer\Service;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\LogActivity;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TrainerDetail;

class ServiceController extends Controller {

    public function __construct() {
        $this->middleware('trainer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $Service = Service::
                select('id', 'name_en', 'isQueue', 'isAppointment')
                 ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id);

                 if(Auth::guard('trainer')->user()->branch_id!=0)
                 $Service->where('branch_id', Auth::guard('trainer')->user()->branch_id);

                $Service->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Service)
                            ->editColumn('created_at', function ($Service) {
                                $newYear = new Carbon($Service->created_at);
                                return $newYear->format('d/m/Y');
                            })                           
                            ->editColumn('isQueue', function ($Service) {
                                return $Service->isQueue == 1 ? '<div class="label label-success queue" sid="' . $Service->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary queue"  sid="' . $Service->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('isAppointment', function ($Service) {
                                return $Service->isAppointment == 1 ? '<div class="label label-success appointment" sid="' . $Service->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary appointment"  sid="' . $Service->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($TrainerPackage) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $TrainerPackage->id . '">';
                            })
                            ->editColumn('action', function ($Service) {
                                return '<a href="'.url('trainer/services') .'/' . $Service->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a> '
                                .'<a href="'.url('trainer/queues') .'/' . $Service->id . '/view" class="btn btn-warning tooltip-warning btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Today Queue Records"><i class="entypo-eye"></i></a>';
                            })
                            ->make();
        }

        return view('trainer.services.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('trainer.services.create');
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
        ],$messsages);


        // validation failed
        if ($validator->fails()) {

            return redirect('trainer/services/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;          
          


            Service::create($input);

            //logActivity
            LogActivity::addToLog('Service - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('trainer/services');
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

        $Service = Service::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.services.edit')
                        ->with('Service', $Service);
    }

    public function view($id) {

        $Service = Service::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.services.view')
                        ->with('Service', $Service);
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
            $Service = Service::findOrFail($id);
            if($request->type=='1')
            $Service->update(['isQueue' => $request->status]);


            if($request->type=='2')
            $Service->update(['isAppointment' => $request->status]);

            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Service = Service::findOrFail($id);
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
        ],$messsages);
        

        // validation failed
        if ($validator->fails()) {
            return redirect('trainer/services/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
            $input['trainer_id']=Auth::guard('trainer')->user()->trainer_id;
            
           
           
            $Service->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Service - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('trainer/services');
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
        $Service = Service::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Service->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('Service - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
          
            Service::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('trainer/services');
    }

}
