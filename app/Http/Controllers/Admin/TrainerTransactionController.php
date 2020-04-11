<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\Transaction;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Mail\TransactionEmail;
use Mail;

class TrainerTransactionController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:transactions');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Trainer Details
        $trainer_id = $request->trainer_id;
        $trainerName = DB::table('trainers')->select('name')->where('id', $trainer_id)->first();

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('transactions-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('transactions-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('transactions-edit');


        $Transaction = Transaction::
                join('payment_modes', 'payment_modes.id', '=', 'transactions.payment_mode')
                ->select('transactions.id', 'transactions.name', 'transactions.reference_num', 'transactions.amount', 'payment_modes.name_en', 'transactions.transferred_date', 'transactions.id As action')
                ->where('transactions.trainer_id', $trainer_id)
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Transaction)
                            ->editColumn('transferred_date', function ($Transaction) {
                                $newYear = new Carbon($Transaction->transferred_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('id', function ($Transaction) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Transaction->id . '">';
                            })
                            ->editColumn('action', function ($Transaction) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/trainertransactions') . '/' . $Transaction->id . '/edit" class="btn btn-info tooltip-primary btn-small " data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . '<a data-id="' . $Transaction->id . '" class="btn btn-orange tooltip-primary btn-small icon_margin_left transactionEmail" data-toggle="tooltip" data-placement="top" title="Send Email" data-original-title="Send Email"><i class="entypo-mail"></i></a>';
                            })
                            ->make();
        }

        return view('admin.trainertransactions.index')
                        ->with('CreateAccess', $this->CreateAccess)
                        ->with('DeleteAccess', $this->DeleteAccess)
                        ->with('EditAccess', $this->EditAccess)
                        ->with('trainer_id', $trainer_id)
                        ->with('trainerName', $trainerName);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $trainer_id = $request->trainer_id;
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('transactions-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all paymentModes
        $paymentModes = DB::table('payment_modes')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Trainer Name
        $trainerName = DB::table('trainers')->select('name')->where('id', $trainer_id)->first();

        return view('admin.trainertransactions.create')
                        ->with('paymentModes', $paymentModes)
                        ->with('trainer_id', $trainer_id)
                        ->with('trainerName', $trainerName);
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
                    'user_type' => 'required',
                    'reference_num' => 'required|numeric',
                    'payment_mode' => 'required',
                    'transferred_date' => 'required|date_format:d/m/Y',
                    'comment' => 'sometimes',
                    'attachment' => 'required|max:2048',
                    'amount' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
        ]);


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/trainertransactions/' . $request->trainer_id . '/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['user_type', 'comment', 'amount', 'reference_num', 'payment_mode']);

            //Attachment 
            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment');
                $filename = time() . '.' . $attachment->getClientOriginalExtension();
                $destinationPath = public_path('transactions_images/');
                $attachment->move($destinationPath, $filename);
                $input['attachment'] = $filename;
            }

            //Check user type
            $input['trainer_id'] = $request->trainer_id;
            //Get all trainers
            $transaction_details = DB::table('trainers')
                    ->select('acc_name', 'acc_num', 'ibn_num', 'name', 'email', 'mobile')
                    ->where(array('status' => 1, 'id' => $input['trainer_id']))
                    ->whereNull('deleted_at')
                    ->first();


            $input['name'] = $transaction_details->name;
            $input['email'] = $transaction_details->email;
            $input['mobile'] = $transaction_details->mobile;
            $input['acc_name'] = $transaction_details->acc_name;
            $input['acc_num'] = $transaction_details->acc_num;
            $input['ibn_num'] = $transaction_details->ibn_num;

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->transferred_date);
            $input['transferred_date'] = $newDate->format('Y-m-d');

            Transaction::create($input);

            //logActivity
            // Check user Type and user name            
            $user_type = 'Trainer';
            //Get all trainers
            $trainers = DB::table('trainers')
                    ->select('id', 'name')
                    ->where(array('status' => 1, 'id' => $request->trainer_id))
                    ->whereNull('deleted_at')
                    ->first();

            $username = $trainers->name;

            LogActivity::addToLog('Transaction - ' . $user_type . ' ' . $username . ' amount ' . $request->amount . config('global.amountCurrency'), 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/' . $request->trainer_id . '/trainertransactions');
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
        $this->EditAccess = Permit::AccessPermission('transactions-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Get all paymentModes
        $paymentModes = DB::table('payment_modes')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();


        $Transaction = Transaction::find($id);
        $trainer_id = $Transaction->trainer_id;
        //Trainer Name
        $trainerName = DB::table('trainers')->select('name')->where('id', $trainer_id)->first();

        //Change Date Format
        $newdate = new Carbon($Transaction->transferred_date);
        $Transaction->transferred_date = $newdate->format('d/m/Y');

        // show the edit form and pass the nerd
        return View::make('admin.trainertransactions.edit')
                        ->with('Transaction', $Transaction)
                        ->with('paymentModes', $paymentModes)
                        ->with('trainer_id', $trainer_id)
                        ->with('trainerName', $trainerName);
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
        $this->EditAccess = Permit::AccessPermission('transactions-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Transaction = Transaction::findOrFail($id);
        $trainer_id=$Transaction->trainer_id;
        // validate
        $validator = Validator::make($request->all(), [
                    'user_type' => 'required',
                    'reference_num' => 'required|numeric',
                    'payment_mode' => 'required',
                    'transferred_date' => 'required|date_format:d/m/Y',
                    'comment' => 'sometimes',
                    'amount' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
        ]);


        //Attachment Validate
        if ($request->hasFile('attachment')) {
            $validator = Validator::make($request->only(['attachment']), [
                        'attachment' => 'required|max:2048'
            ]);
        }
        // validation failed
        if ($validator->fails()) {
            return redirect('admin/trainertransactions/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['user_type', 'comment', 'amount', 'reference_num', 'payment_mode']);

            //Attachment 
            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment');
                $filename = time() . '.' . $attachment->getClientOriginalExtension();
                $destinationPath = public_path('transactions_images/');
                $attachment->move($destinationPath, $filename);
                //Remove previous images
                if (file_exists($destinationPath . $Transaction->attachment) && $Transaction->attachment != '') {
                    unlink($destinationPath . $Transaction->attachment);
                }
                $input['attachment'] = $filename;
            }


            //Check user type
                $input['trainer_id'] = $trainer_id;
                //Get all trainers
                $transaction_details = DB::table('trainers')
                        ->select('acc_name', 'acc_num', 'ibn_num', 'name', 'email', 'mobile')
                        ->where(array('status' => 1, 'id' =>  $trainer_id))
                        ->whereNull('deleted_at')
                        ->first();
            

            $input['name'] = $transaction_details->name;
            $input['email'] = $transaction_details->email;
            $input['mobile'] = $transaction_details->mobile;
            $input['acc_name'] = $transaction_details->acc_name;
            $input['acc_num'] = $transaction_details->acc_num;
            $input['ibn_num'] = $transaction_details->ibn_num;

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->transferred_date);
            $input['transferred_date'] = $newDate->format('Y-m-d');


            $Transaction->fill($input)->save();

            //logActivity
            // Check user Type and user name
                $user_type = 'Trainer';
                //Get all trainers
                $trainers = DB::table('trainers')
                        ->select('id', 'name')
                        ->where(array('status' => 1, 'id' =>  $trainer_id))
                        ->whereNull('deleted_at')
                        ->first();

                $username = $trainers->name;
           

            LogActivity::addToLog('Transaction - ' . $user_type . ' ' . $username . ' amount ' . $request->amount . config('global.amountCurrency'), 'updated');


            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/' . $trainer_id . '/trainertransactions');
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
        $this->DeleteAccess = Permit::AccessPermission('transactions-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->all('_token', 'table-4_length');
        $all_data = array_get($all_data, 'ids');

        foreach ($all_data as $id) {
            //Delete Attachment 
            $Transaction = Transaction::
                    select('attachment', 'user_type', 'trainer_id', 'trainer_id', 'amount')->where('id', $id)->first();

            $destinationPath = public_path('transactions_images/');

            if (!empty($Transaction)) {
                if (file_exists($destinationPath . $Transaction->attachment) && $Transaction->attachment != '') {
                   // @unlink($destinationPath . $Transaction->attachment);
                }
            }

            //logActivity      
            if (!empty($Transaction)) {
                if ($Transaction->user_type == 1) {
                    $user_type = 'Trainer';
                    //Get all trainers
                    $trainers = DB::table('trainers')
                            ->select('id', 'name')
                            ->where(array('status' => 1, 'id' => $Transaction->trainer_id))
                            ->whereNull('deleted_at')
                            ->first();

                    if (!empty($trainers)) {
                        $username = $trainers->name;
                        LogActivity::addToLog('Transaction - ' . $user_type . ' ' . $username . ' amount ' . $Transaction->amount . config('global.amountCurrency'), 'deleted');
                    }
                } if ($Transaction->user_type == 2) {
                    $user_type = 'Trainer';
                    //Get all trainers
                    $trainers = DB::table('trainers')
                            ->select('id', 'name')
                            ->where(array('status' => 1, 'id' => $Transaction->trainer_id))
                            ->whereNull('deleted_at')
                            ->first();

                    if (!empty($trainers)) {
                        $username = $trainers->name;

                        LogActivity::addToLog('Transaction - ' . $user_type . ' ' . $username . ' amount ' . $Transaction->amount . config('global.amountCurrency'), 'deleted');
                    }
                }
            }

            Transaction::destroy($id);
        }


        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/' . $Transaction->trainer_id . '/trainertransactions');
    }

    /**
     * Notification via Email.
     */
    public function transactionEmail($id) {
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('transactions-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            //fetch Record
            $Transaction = Transaction::
                    select('user_type', 'id', 'trainer_id', 'trainer_id', 'attachment')
                    ->where(array('id' => $id))
                    ->first();
            //check record exist
            $count = Transaction::where(array('id' => $id))->count();

            if ($count != 0) {

                //Check user type and send notification
                if ($Transaction->user_type == 1) {
                    $user_type = 'Trainer';
                    //Get all trainers
                    $trainers = DB::table('trainers')
                            ->select('id', 'email')
                            ->where(array('status' => 1, 'id' => $Transaction->trainer_id))
                            ->whereNull('deleted_at')
                            ->first();

                    if (!empty($trainers)) {
                        $Transaction->email = $trainers->email;
                    }
                } if ($Transaction->user_type == 2) {
                    $user_type = 'Trainer';
                    //Get all trainers
                    $trainers = DB::table('trainers')
                            ->select('id', 'email')
                            ->where(array('status' => 1, 'id' => $Transaction->trainer_id))
                            ->whereNull('deleted_at')
                            ->first();

                    if (!empty($trainers)) {
                        $Transaction->email = $trainers->email;
                    }
                }
              
                //Email with attachment 
                Mail::to($Transaction->email)->send(new TransactionEmail($Transaction));

                return response()->json(['response' => config('global.sentEmail')]);
            } else {

                return response()->json(['response' => config('global.unsentEmail')]);
            }
        }
    }

}
