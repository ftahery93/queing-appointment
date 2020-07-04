<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Trainer\User;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use Illuminate\Support\Facades\Auth;
use Mail;

class UserController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('trainer');
       // $this->middleware('permission:users');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
   
        
        $User = User::
                select('trainers.id', 'trainers.username', 'trainers.email', 'branches.name_en', 'trainers.status', 'trainers.created_at')
                ->join('branches', 'branches.id', '=', 'trainers.branch_id')
                ->where('trainers.trainer_id', Auth::guard('trainer')->user()->trainer_id)
                ->where('trainers.branch_id', '!=',0)
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($User)
                            ->editColumn('created_at', function ($User) {
                                $newYear = new Carbon($User->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($User) {
                                return $User->status == 1 ? '<div class="label label-success status" sid="' . $User->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $User->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($User) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $User->id . '">';
                            })
                            ->editColumn('action', function ($User) {                               
                                    return '<a href="' . url('trainer/users') . '/' . $User->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->editColumn('branch', function ($User) {
                                return $User->name_en;
                            })
                            ->make();
        }

        return view('trainer.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
      
        //Check Create Access Permission      

        //Get all User Role
        $branches = DB::table('branches')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
                ->get();

     
        return view('trainer.users.create')->with('branches', $branches);
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
                    'name' => 'required',
                    'email' => 'required|email|unique:trainers',
                    'branch_id' => 'required',
        ]);



        // validation failed
        if ($validator->fails()) {

            return redirect('trainer/users/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();
            $password = $this->random_strings(8);
            $input['original_password'] = $password;
            $input['password'] = bcrypt($password);
            $input['trainer_id'] =  Auth::guard('trainer')->user()->trainer_id; 
                  

            $id = User::create($input)->id;
            $User = User::findOrFail($id);
            $username =  $request->name.'_'.$id;
            $User->update(['username' => $username]);

            //Send email
            $data['username'] = $username;
            $data['password'] = $password;
          
             //mail from and Name
            $data['MAIL_FROM_ADDRESS'] = env('MAIL_FROM_ADDRESS');
             $data['APP_NAME'] = env('APP_NAME');

             $data['name'] = $input['name'];
            $data['email'] = $input['email'];

            $send = $this->sendmail('emails.registration', $data);


            //logActivity
            LogActivity::addToLog('User - ' . $request->name, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('trainer/users');
        }
    }

     Private function random_strings($length_of_string) 
            { 
            
                // String of all alphanumeric character 
                $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
            
                // Shufle the $str_result and returns substring 
                // of specified length 
                return substr(str_shuffle($str_result),  
                                0, $length_of_string); 
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
     

       //Get all User Role
       $branches = DB::table('branches')
       ->select('id', 'name_en')
       ->where('status', 1)
       ->where('trainer_id', Auth::guard('trainer')->user()->trainer_id)
       ->get();

        $User = User::find($id);

        // show the edit form and pass the nerd
        return View::make('trainer.users.edit')
                        ->with('User', $User)
                        ->with('branches', $branches);
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
            $User = User::findOrFail($id);
            $User->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $User = User::findOrFail($id);
        // validate
        $validator = Validator::make($request->all(), [
                    'branch_id' => 'required',
        ]);

       
        // validation failed
        if ($validator->fails()) {
            return redirect('trainer/users/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();
           
            $User->fill($input)->save();

            //Send email
            // $data['username'] = 'fadsf';
            // $data['password'] = 'fasdf';
          
            //  //mail from and Name
            // $data['MAIL_FROM_ADDRESS'] = env('MAIL_FROM_ADDRESS');
            //  $data['APP_NAME'] = env('APP_NAME');

            //  $data['name'] = $input['name'];
            // $data['email'] = $input['email'];

            // $send = $this->sendmail('emails.registration', $data);

            //logActivity
            LogActivity::addToLog('User - ' . $request->username, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('trainer/users');
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
        $User = User::
                select('username')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $User->pluck('username');
        $groupname = $name->toJson();

        LogActivity::addToLog('User - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            User::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('trainer/users');
    }

    public function sendmail($template, $data)
    {

        Mail::send($template,  $data, function ($message) use ($data) {
            $message->to($data['email'], $data['name'])->subject('Registration');
            $message->from($data['MAIL_FROM_ADDRESS'], $data['APP_NAME']);
        });

       
        if (Mail::failures()) {
            return false;
        } else {
            return true;
        }
    }


}
