<?php

namespace App\Http\Controllers\Trainer;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Trainer\RegisteredUser;
use App\Models\Trainer\SubscriberAttendTrainer;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TrainerDetail;

class SubscriberController extends Controller {

    public function __construct() {
        $this->middleware('trainer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //TrainerDetail::setSubscriberPackageStatus();

        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('spd.id', 'spd.subscriber_id', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'areas.name_en As area', 'gender_types.name_en As gender', 'spd.id AS subscriber_package_id'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'))
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                ->where('spd.active_status', 1);
        // ->groupby('sat.subscribed_package_id')
        //->havingRaw('MAX(spd.end_date) >= NOW()')
        // ->havingRaw('count_class  < spd.num_points');
        //if Request having ID
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $SubscribersList->where('spd.subscriber_id', $ID);
        }

        $SubscribersList->groupby('spd.subscriber_id');
        $RegisteredUser = $SubscribersList->get();

        //Get All Subscribers 
        $Subscribers = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->select('spd.subscriber_id', 'registered_users.name', DB::raw('COALESCE(SUM(sat.status),0) as count_class')
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'))
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
                ->where('spd.active_status', 1)
                //->groupby('spd.subscriber_id')
                //->havingRaw('MAX(spd.end_date) >= NOW()')
                //->havingRaw('count_class  < spd.num_points')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('action', function ($RegisteredUser) {
                                if ($RegisteredUser->count_class == $RegisteredUser->num_points) {
                                    return '<a href="' . url('trainer/subscribers') . '/' . $RegisteredUser->id . '/attendanceHistory" class="btn btn-gold tooltip-primary btn-small" data-toggle="tooltip" data-placement="top"  data-original-title="Attendance History" title="Attendance History" style="color:#fff !important;"><i class="fa fa-hourglass-2"></i></a>
                                       <a  class="btn btn-primary tooltip-primary btn-small current_package" data-toggle="modal"  data-original-title="Current Package" title="Current Package"  href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>
                                          <a  class="btn btn-orange tooltip-primary btn-small payment_details" data-toggle="modal"  data-original-title="Payment Details" title="Payment Details"  href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>'
                                            . '<a href="' . url('trainer') . '/' . $RegisteredUser->subscriber_id . '/packageHistory" class="btn btn-info tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>';
                                } else {
                                    return '<a href="' . url('trainer/subscribers') . '/' . $RegisteredUser->id . '/attendanceHistory" class="btn btn-gold tooltip-primary btn-small" data-toggle="tooltip" data-placement="top"  data-original-title="Attendance History" title="Attendance History" style="color:#fff !important;"><i class="fa fa-hourglass-2"></i></a>
                                       <a  class="btn btn-primary tooltip-primary btn-small current_package" data-toggle="modal"  data-original-title="Current Package"  title="Current Package" href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>
                                          <a  class="btn btn-orange tooltip-primary btn-small payment_details" data-toggle="modal"  data-original-title="Payment Details" title="Payment Details"  href="#myModal" data-val="' . $RegisteredUser->id . '"><i class="entypo-clock"></i></a>
                                          <a  class="btn magenta tooltip-primary btn-small add_attendance" data-toggle="modal" data-placement="top"  data-original-title="Add Attendance" title="Add Attendance"  href="#myModal2" style="color:#fff !important;" data-val="' . $RegisteredUser->subscriber_package_id . '"><i class="fa fa-calendar-plus-o"></i></a>'
                                            . ' <a href="' . url('trainer') . '/' . $RegisteredUser->subscriber_id . '/packageHistory" class="btn btn-info tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>';
                                }
                            })
                            ->make();
        }

        return view('trainer.subscribers.index')->with('Subscribers', $Subscribers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function attendanceHistory($id) {

        //Get current package
        $currentPackage = DB::table('trainer_subscribers_package_details AS spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('spd.id', 'spd.name_en', 'spd.price', 'spd.num_points', 'spd.num_days', 'spd.start_date', 'spd.end_date', 'registered_users.name', 'spd.num_booked')
                ->where(array('spd.id' => $id))
                ->first();
        
        //Get all Subscriber History
        $attendanceHistory = DB::table('subscriber_attend_trainers')
                ->select('id', 'date', 'description_en', 'status')
                ->where('subscribed_package_id', $currentPackage->id)
                ->get();

        //Change Start Date Format
        $newdate = new Carbon($currentPackage->start_date);
        $currentPackage->start_date = $newdate->format('d/m/Y');

        //Change End Date Format
        $enddate = new Carbon($currentPackage->end_date);
        $currentPackage->end_date = $enddate->format('d/m/Y');

        //Total Attend
        $totalAttend = $currentPackage->num_booked;

        //Total Remaining        
            $totalRemaining = $currentPackage->num_points - $totalAttend;
        
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($attendanceHistory)
                            ->editColumn('date', function ($attendanceHistory) {
                                $newYear = new Carbon($attendanceHistory->date);
                                return $newYear->format('d/m/Y h:i A');
                            })
                            ->editColumn('status', function ($attendanceHistory) {
                                return $attendanceHistory->status == 1 ? '<button type="button" class="btn btn-success btn-xs">Attended</button>' : '<button type="button" class="btn btn-danger btn-xs">Not Attended</button>';
                            })
                            ->make();
        }

        return view('trainer.subscribers.attendanceHistory')
                        ->with('currentPackage', $currentPackage)
                        ->with('totalAttend', $totalAttend)
                        ->with('totalRemaining', $totalRemaining)
                        ->with('id', $id);
    }

    public function currentPackage($id) {

        //Get current package
        $currentPackage = DB::table('trainer_subscribers_package_details')
                ->select('name_en', 'price', DB::raw('(CASE WHEN num_points = 0 THEN "Unlimited" ELSE num_points  END) AS num_points'), 'start_date', 'end_date')
                ->where(array('id' => $id))
                ->first();

        //Change Start Date Format
        $newdate = new Carbon($currentPackage->start_date);
        $currentPackage->start_date = $newdate->format('d/m/Y');

        //Change End Date Format
        $enddate = new Carbon($currentPackage->end_date);
        $currentPackage->end_date = $enddate->format('d/m/Y');

        $currentPackage->detail_type = 0; //Current Package:0

        $returnHTML = view('trainer.subscribers.currentPackage')->with('currentPackage', $currentPackage)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function paymentDetails($id) {

        //Get current payment details
        $currentPackage = DB::table('trainer_subscribers_package_details As spd')
                ->join('payment_details', 'spd.payment_id', '=', 'payment_details.id')
                ->select('payment_details.reference_id', 'payment_details.amount', 'payment_details.post_date', 'payment_details.result', DB::raw('(CASE WHEN payment_details.card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->where(array('spd.id' => $id))
                ->first();

        //Change Start Date Format
        $newdate = new Carbon($currentPackage->post_date);
        $currentPackage->post_date = $newdate->format('d/m/Y');

        $currentPackage->detail_type = 1; //Payment Details:0

        $returnHTML = view('trainer.subscribers.currentPackage')->with('currentPackage', $currentPackage)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function create(Request $request) {

        //Ajax request
        if (request()->ajax()) {
            // validate
            $validator = Validator::make($request->except(['description_en']), [
                        'subscribed_package_id' => 'required',
                        'date' => 'required',
                        'status' => 'required',
            ]);

            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => config('global.fieldError')]);
            } else {

                $input = $request->all();

                //Change Date Format
                $datetime = new DateTime();
                $newDate = $datetime->createFromFormat('d/m/Y H:m:A', $request->date);
                $input['date'] = $newDate->format('Y-m-d H:i:s');

                //Get current package
                $currentPackage = DB::table('trainer_subscribers_package_details')
                        ->select('num_points')
                        ->where('id', $request->subscribed_package_id)
                        ->first();

                //Total Attend
                $totalAttend = DB::table('subscriber_attend_trainers')
                        ->where('status', '=', 1)
                        ->count();

                if ($request->status != 0) {
                    TrainerDetail::setclassAtttend($request->subscribed_package_id);
                }
                //Send error if account null
                if ($currentPackage->num_points == $totalAttend) {
                    return response()->json(['error' => config('global.attendClassError')]);
                }

                SubscriberAttendTrainer::create($input);

                //logActivity
                //Get Subscriber name
                $username = DB::table('trainer_subscribers_package_details As spd')
                        ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                        ->select('registered_users.name')
                        ->where('spd.id', $request->subscribed_package_id)
                        ->first();

                LogActivity::addToLog('Attendance Subscriber - ' . $username->name, 'created');

                return response()->json(['response' => config('global.addedRecords')]);
            }
        }
    }

    //Archived Classes
    public function archivedSubscribers(Request $request) {
        //TrainerDetail::setSubscriberPackageStatus();
        $SubscribersList = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->leftjoin('areas', 'areas.id', '=', 'registered_users.area_id')
                ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                ->leftjoin('gender_types', 'gender_types.id', '=', 'registered_users.gender_id')
                ->select('spd.id', 'spd.subscriber_id', 'registered_users.name', 'registered_users.email', 'registered_users.mobile', 'areas.name_en As area', 'gender_types.name_en As gender', 'spd.id AS subscriber_package_id'
                        , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points')
                        , DB::raw('COALESCE(SUM(sat.status),0) as count_class'))
                ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
//                 ->whereNotIn('spd.subscriber_id', function($query) {
//                    $query->select(DB::raw('COALESCE(subscriber_id,0) AS id'))
//                    ->from('trainer_subscribers_package_details')
//                    ->where('active_status', '=', 1)
//                    ->orwhere('active_status', '=', 0)
//                    ->groupby('subscriber_id');
//                })
               ->whereNotIn('spd.subscriber_id', function($query) {
                            $query->select(DB::raw('ts.subscriber_id'))
                            ->from('trainer_subscribers_package_details As ts')
                            ->where(function ($query) {
                                $query->where('ts.active_status', '=', 1)
                                ->orwhere('ts.active_status', '=', 0);
                            })
                            ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
                        });
        // ->groupby('sat.subscribed_package_id')
        // ->havingRaw('MAX(spd.end_date) < NOW() or count_class  = spd.num_points');
        //if Request having ID
        if ($request->has('id') && $request->get('id') != 0) {
            $ID = $request->get('id');
            $SubscribersList->where('spd.subscriber_id', $ID);
        }

        $SubscribersList->groupby('spd.subscriber_id');
        //dd($SubscribersList->toSql());
        $RegisteredUser = $SubscribersList->get();

        //Get All Subscribers 
        $Subscribers = DB::table('trainer_subscribers_package_details As spd')
                        ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                        ->leftjoin('subscriber_attend_trainers As sat ', 'sat.subscribed_package_id', '=', 'spd.id')
                        ->select('spd.subscriber_id', 'registered_users.name', DB::raw('COALESCE(SUM(sat.status),0) as count_class')
                                , DB::raw('(CASE WHEN spd.num_points = 0 THEN "Unlimited" ELSE spd.num_points  END) AS num_points'))
                        ->where('spd.trainer_id', Auth::guard('trainer')->user()->id)
//                ->whereNotIn('spd.subscriber_id', function($query) {
//                    $query->select(DB::raw('COALESCE(subscriber_id,0) AS id'))
//                    ->from('trainer_subscribers_package_details')
//                    ->where('active_status', '=', 1)
//                    ->orwhere('active_status', '=', 0)
//                    ->groupby('subscriber_id');
//                })
                        ->whereNotIn('spd.subscriber_id', function($query) {
                            $query->select(DB::raw('ts.subscriber_id'))
                            ->from('trainer_subscribers_package_details As ts')
                            ->where(function ($query) {
                                $query->where('ts.active_status', '=', 1)
                                ->orwhere('ts.active_status', '=', 0);
                            })
                            ->whereColumn('spd.subscriber_id', 'ts.subscriber_id');
                        })
                        ->groupby('spd.subscriber_id')->get();
//                ->groupby('spd.subscriber_id')
//                ->havingRaw('MAX(spd.end_date) < NOW() or count_class  = spd.num_points')
//                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($RegisteredUser)
                            ->editColumn('action', function ($RegisteredUser) {
                                return '<a href="' . url('trainer') . '/' . $RegisteredUser->subscriber_id . '/archivedAttendanceHistory" class="btn btn-gold tooltip-primary btn-small" data-toggle="tooltip" data-placement="top"  data-original-title="Attendance History" title="Attendance History" style="color:#fff !important;"><i class="fa fa-hourglass-2"></i></a>
                                       <a href="' . url('trainer') . '/' . $RegisteredUser->subscriber_id . '/packageHistory" class="btn btn-primary tooltip-primary btn-small package_history" data-toggle="tooltip"  data-original-title="Package History" title="Package History"><i class="entypo-bag"></i></a>';
                            })
                            ->make();
        }

        return view('trainer.subscribers.archivedSubscribers')->with('Subscribers', $Subscribers);
    }

    public function archivedAttendanceHistory($id) {

        //Get Subscriber name
        $username = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.name')
                ->where('spd.subscriber_id', $id)
                ->first();

        //Get all Archived Subscriber History
        $archivedAttendanceHistory = DB::table('trainer_subscribers_package_details AS spd')
                ->rightjoin('subscriber_attend_trainers As sat', 'spd.id', '=', 'sat.subscribed_package_id')
                ->select('spd.name_en', 'spd.num_points', 'spd.start_date', 'spd.end_date', 'sat.date', 'sat.description_en', 'sat.status')
                ->where(array('spd.subscriber_id' => $id))
                ->get();


        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($archivedAttendanceHistory)
                            ->editColumn('date', function ($archivedAttendanceHistory) {
                                $newYear = new Carbon($archivedAttendanceHistory->date);
                                return $newYear->format('d/m/Y h:i A');
                            })
                            ->editColumn('start_date', function ($archivedAttendanceHistory) {
                                $newYear = new Carbon($archivedAttendanceHistory->start_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('end_date', function ($archivedAttendanceHistory) {
                                $newYear = new Carbon($archivedAttendanceHistory->end_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($archivedAttendanceHistory) {
                                return $archivedAttendanceHistory->status == 1 ? '<button type="button" class="btn btn-success btn-xs">Attended</button>' : '<button type="button" class="btn btn-danger btn-xs">Not Attended</button>';
                            })
                            ->editColumn('num_points', function ($archivedAttendanceHistory) {
                                return $archivedAttendanceHistory->num_points == 0 ? 'Unlimited' : $archivedAttendanceHistory->num_points;
                            })
                            ->make();
        }

        return view('trainer.subscribers.archivedAttendanceHistory')
                        ->with('id', $id)
                        ->with('username', $username);
    }

    public function packageHistory($id) {

        //Get Subscriber name
        $username = DB::table('trainer_subscribers_package_details As spd')
                ->join('registered_users', 'spd.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.name')
                ->where('spd.subscriber_id', $id)
                ->first();

        //Sum Amount
        $Amount = DB::table('trainer_subscribers_package_details AS spd')
                ->where(array('spd.subscriber_id' => $id))
                ->whereNotNull('spd.trainer_id')
                ->WhereNull('spd.vendor_id')
                ->sum('spd.price');

        //Get all Archived Subscriber History
        $packageHistory = DB::table('trainer_subscribers_package_details AS spd')
                ->select('spd.name_en', 'spd.price', 'spd.start_date', 'spd.end_date', 'spd.num_points', 'spd.num_days', 'spd.payment_id')
                ->where(array('spd.subscriber_id' => $id))
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($packageHistory)
                            ->editColumn('start_date', function ($packageHistory) {
                                $newYear = new Carbon($packageHistory->start_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('end_date', function ($packageHistory) {
                                $newYear = new Carbon($packageHistory->end_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('num_points', function ($packageHistory) {
                                return $packageHistory->num_points == 0 ? 'Unlimited' : $packageHistory->num_points;
                            })
                            ->editColumn('action', function ($RegisteredUser) {
                                return '<a  class="btn btn-green tooltip-primary btn-small package_details" data-toggle="modal"  data-original-title="Package Details"  href="#myModal" data-val="' . $RegisteredUser->payment_id . '"><i class="fa fa-money"></i></a>';
                            })
                            ->make();
        }

        return view('trainer.subscribers.packageHistory')
                        ->with('id', $id)
                        ->with('username', $username)
                        ->with('Amount', $Amount);
    }

    public function packagePayment($payment_id) {

        //Get package payment details
        $payment = DB::table('payment_details')
                ->select('reference_id', 'amount', 'post_date', 'result', DB::raw('(CASE WHEN card_type = 1 THEN "KNET" ELSE "Credit Card" END) AS card_type'))
                ->where(array('id' => $payment_id))
                ->first();

        //Change Start Date Format
        $newdate = new Carbon($payment->post_date);
        $payment->post_date = $newdate->format('d/m/Y');

        $returnHTML = view('trainer.subscribers.packagePayment')->with('payment', $payment)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

}
