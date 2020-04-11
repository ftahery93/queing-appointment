<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\Notification;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class TrainerNotificationController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:notifications');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('notifications-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('notifications-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('notifications-edit');

        $Notification = Notification::
                select('id', 'send_to', 'subject', 'notification_date', 'sent_status')
                ->whereNotNull('trainer_id')
                ->WhereNull('vendor_id')
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Notification)
                            ->editColumn('subject', function ($Notification) {
                                return str_limit($Notification->subject, 15);
                            })
                            ->editColumn('send_to', function ($Notification) {
                                  if($Notification->send_to==0)
                                      return 'All Users';                                   
                            })
                            ->editColumn('notification_date', function ($Notification) {
                                $newYear = new Carbon($Notification->notification_date);
                                return $newYear->format('d/m/Y h:i A');
                            })
                            ->editColumn('id', function ($Notification) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Notification->id . '">';
                            })
                            ->editColumn('sent_status', function ($Notification) {
                                return 0;
                            })
                            ->editColumn('action', function ($Notification) {
                                if ($this->EditAccess)
                                    return '<a href="'.url('admin/trainerNotifications') .'/' . $Notification->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('admin.trainerNotifications.index')
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
        $this->CreateAccess = Permit::AccessPermission('notifications-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');
        
        $Trainers = DB::table('trainers')
                ->select('name', 'id')
                ->where('status', 1)
                 ->whereNull('deleted_at')
                ->get();

        return view('admin.trainerNotifications.create')->with('Trainers', $Trainers);
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
                   // 'subject' => 'required',
                   // 'subject_ar' => 'required',
                    'message' => 'required',
                    'message_ar' => 'required',
                    'notification_date' => 'required|date_format:d/m/Y h:i:A',
                    'send_to' => 'required',
                    'trainer_id' => 'required',
                    //'link' => 'sometimes|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/trainerNotifications/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y h:i:A', $request->notification_date);
            $input['notification_date'] = $newDate->format('Y-m-d H:i:s');

            Notification::create($input);

            //LogActivity
            LogActivity::addToLog('Notification - ' . str_limit($request->subject, 15), 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/trainerNotifications');
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
        $this->EditAccess = Permit::AccessPermission('notifications-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Notification = Notification::find($id);
        
        $Trainers = DB::table('trainers')
                ->select('name', 'id')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        //Change Date Format
        $newdate = new Carbon($Notification->notification_date);
        $Notification->notification_date = $newdate->format('d/m/Y h:i A');

        // show the edit form and pass the nerd
        return View::make('admin.trainerNotifications.edit')
                        ->with('Notification', $Notification)->with('Trainers', $Trainers);
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
        $this->EditAccess = Permit::AccessPermission('notifications-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Notification = Notification::findOrFail($id);
            $Notification->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Notification = Notification::findOrFail($id);
        // validate
        $validator = Validator::make($request->all(), [
                    //'subject' => 'required',
                    //'subject_ar' => 'required',
                    'message' => 'required',
                    'message_ar' => 'required',
                    'notification_date' => 'required|date_format:d/m/Y h:i:A',
                    'send_to' => 'required',
                     'trainer_id' => 'required',
                    //'link' => 'sometimes|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/trainerNotifications/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

            //Change Date Format
            
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y h:i:A', $request->notification_date);
            $input['notification_date'] = $newDate->format('Y-m-d H:i:s');
           //dd($input['notification_date']);
            $Notification->fill($input)->save();
 
            //LogActivity
            LogActivity::addToLog('Notification - ' . str_limit($request->subject, 15), 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/trainerNotifications');
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
        $this->DeleteAccess = Permit::AccessPermission('notifications-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //LogActivity
        //fetch title
        $Notification = Notification::
                        select('subject')
                        ->whereIn('id', $all_data['ids'])
                        ->get()-> map(function ($Notification) {
                    $Notification->subject = str_limit($Notification->subject, 15);
                    return $Notification;
                });
        $name = $Notification->pluck('subject');
        $groupname = $name->toJson();

        LogActivity::addToLog('Notification - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Notification::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/trainerNotifications');
    }

}
