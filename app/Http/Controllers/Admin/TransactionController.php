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

class TransactionController extends Controller {

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

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('transactions-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('transactions-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('transactions-edit');


        $Transaction = Transaction::
                select('transactions.id', 'transactions.name', 'transactions.user_type', 'transactions.amount AS total_amount', 'transactions.amount', 'transactions.amount As due_amount', 'transactions.transferred_date', 'transactions.id As action')
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Transaction)
                            ->editColumn('user_type', function ($Transaction) {
                                return $Transaction->user_type == 1 ? 'vendor' : 'trainer';
                            })
                            ->editColumn('transferred_date', function ($Transaction) {
                                $newYear = new Carbon($Transaction->transferred_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('id', function ($Transaction) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Transaction->id . '">';
                            })
                            ->editColumn('total_amount', function ($Transaction) {
                                return '0';
                            })
                            ->editColumn('due_amount', function ($Transaction) {
                                return '0';
                            })
                            ->editColumn('action', function ($Transaction) {
                                if ($this->EditAccess)
                                    return '<a href="'.url('admin/transactions') .'/' . $Transaction->id . '/edit" class="btn btn-info tooltip-primary btn-small " data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . '<a data-id="' . $Transaction->id . '" class="btn btn-orange tooltip-primary btn-small icon_margin_left transactionEmail" data-toggle="tooltip" data-placement="top" title="Transaction Email" data-original-title="Transaction Email"><i class="entypo-mail"></i></a>';
                            })
                            ->make();
        }

        return view('admin.transactions.index')
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
        $this->CreateAccess = Permit::AccessPermission('transactions-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        //Get all vendors
        $vendors = DB::table('vendors')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        //Get all trainers
        $trainers = DB::table('trainers')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();


        return view('admin.transactions.create')
                        ->with('vendors', $vendors)
                        ->with('trainers', $trainers);
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
                    'user_id' => 'required',
                    'transferred_date' => 'required|date_format:d/m/Y',
                    'comment' => 'sometimes',
                    'attachment' => 'required|max:2048',
                    'amount' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/')
        ]);


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/transactions/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['user_type', 'comment', 'amount']);

            //Attachment 
            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment');
                $filename = time() . '.' . $attachment->getClientOriginalExtension();
                $destinationPath = public_path('transactions_images/');
                $attachment->move($destinationPath, $filename);
                $input['attachment'] = $filename;
            }

            //Check user type
            $userVendor = $request->user_id;
            if ($request->user_type == 1) {              
                $input['vendor_id'] = array_get($userVendor, 'vendor');
                  //Get all vendors
                $transaction_details = DB::table('vendors')
                        ->select('acc_name', 'acc_num', 'ibn_num', 'name', 'email', 'mobile')
                        ->where(array('status'=>1,'id'=>$input['vendor_id']))
                        ->whereNull('deleted_at')
                        ->first();

            } else {                
                $input['trainer_id'] = array_get($userVendor, 'trainer');
                //Get all trainers
                $transaction_details = DB::table('trainers')
                        ->select('acc_name', 'acc_num', 'ibn_num', 'name', 'email', 'mobile')
                        ->where(array('status'=>1,'id'=>$input['trainer_id']))
                        ->whereNull('deleted_at')
                        ->first();
            }
          
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
            if ($request->user_type == 1) {
                $user_type = 'Vendor';
                //Get all vendors
                $vendors = DB::table('vendors')
                        ->select('id', 'name')
                        ->where(array('status' => 1, 'id' => array_get($userVendor, 'vendor')))
                        ->whereNull('deleted_at')
                        ->first();

                $username = $vendors->name;
            } else {
                $user_type = 'Trainer';
                //Get all trainers
                $trainers = DB::table('trainers')
                        ->select('id', 'name')
                        ->where(array('status' => 1, 'id' => array_get($userVendor, 'trainer')))
                        ->whereNull('deleted_at')
                        ->first();

                $username = $trainers->name;
            }

            LogActivity::addToLog('Transaction - ' . $user_type . ' ' . $username . ' amount ' . $request->amount . config('global.amountCurrency'), 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/transactions');
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

        //Get all vendors
        $vendors = DB::table('vendors')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        //Get all trainers
        $trainers = DB::table('trainers')
                ->select('id', 'name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        $Transaction = Transaction::find($id);

        //Change Date Format
        $newdate = new Carbon($Transaction->transferred_date);
        $Transaction->transferred_date = $newdate->format('d/m/Y');

        // show the edit form and pass the nerd
        return View::make('admin.transactions.edit')
                        ->with('Transaction', $Transaction)
                        ->with('vendors', $vendors)
                        ->with('trainers', $trainers);
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
        // validate
        $validator = Validator::make($request->all(), [
                    'user_type' => 'required',
                    'user_id' => 'required',
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
            return redirect('admin/transactions/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['user_type', 'comment', 'amount']);

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
            $userVendor = $request->user_id;
            if ($request->user_type == 1) {              
                $input['vendor_id'] = array_get($userVendor, 'vendor');
                  //Get all vendors
                $transaction_details = DB::table('vendors')
                        ->select('acc_name', 'acc_num', 'ibn_num', 'name', 'email', 'mobile')
                        ->where(array('status'=>1,'id'=>$input['vendor_id']))
                        ->whereNull('deleted_at')
                        ->first();

            } else {                
                $input['trainer_id'] = array_get($userVendor, 'trainer');
                //Get all trainers
                $transaction_details = DB::table('trainers')
                        ->select('acc_name', 'acc_num', 'ibn_num', 'name', 'email', 'mobile')
                        ->where(array('status'=>1,'id'=>$input['trainer_id']))
                        ->whereNull('deleted_at')
                        ->first();
            }

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
            if ($request->user_type == 1) {
                $user_type = 'Vendor';
                //Get all vendors
                $vendors = DB::table('vendors')
                        ->select('id', 'name')
                        ->where(array('status' => 1, 'id' => array_get($userVendor, 'vendor')))
                        ->whereNull('deleted_at')
                        ->first();

                $username = $vendors->name;
            } else {
                $user_type = 'Trainer';
                //Get all trainers
                $trainers = DB::table('trainers')
                        ->select('id', 'name')
                        ->where(array('status' => 1, 'id' => array_get($userVendor, 'trainer')))
                        ->whereNull('deleted_at')
                        ->first();

                $username = $trainers->name;
            }

            LogActivity::addToLog('Transaction - ' . $user_type . ' ' . $username . ' amount ' . $request->amount . config('global.amountCurrency'), 'updated');


            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/transactions');
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
                    select('attachment', 'user_type', 'vendor_id', 'trainer_id', 'amount')->where('id', $id)->first();

            $destinationPath = public_path('transactions_images/');

            if (!empty($Transaction)) {
                if (file_exists($destinationPath . $Transaction->attachment) && $Transaction->attachment != '') {
                    @unlink($destinationPath . $Transaction->attachment);
                }
            }

            //logActivity      
            if (!empty($Transaction)) {
                if ($Transaction->user_type == 1) {
                    $user_type = 'Vendor';
                    //Get all vendors
                    $vendors = DB::table('vendors')
                            ->select('id', 'name')
                            ->where(array('status' => 1, 'id' => $Transaction->vendor_id))
                            ->whereNull('deleted_at')
                            ->first();

                    if (!empty($vendors)) {
                        $username = $vendors->name;
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

        return redirect('admin/transactions');
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
                    select('user_type', 'id', 'vendor_id', 'trainer_id', 'attachment')
                    ->where(array('id' => $id))
                    ->first();
            //check record exist
            $count = Transaction::where(array('id' => $id))->count();

            if ($count != 0) {

                //Check user type and send notification
                if ($Transaction->user_type == 1) {
                    $user_type = 'Vendor';
                    //Get all vendors
                    $vendors = DB::table('vendors')
                            ->select('id', 'email')
                            ->where(array('status' => 1, 'id' => $Transaction->vendor_id))
                            ->whereNull('deleted_at')
                            ->first();

                    if (!empty($vendors)) {
                        $Transaction->email = $vendors->email;
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
