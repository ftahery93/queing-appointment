<?php

namespace App\Http\Controllers\Vendor\Module3;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;

class ReportController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $ViewAccess;
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor');
        // $this->middleware('vendorPermission:M3bookings');
    }

    /**
     * Display a listing of the Favourites.
     */
    public function bookings(Request $request) {

        //Check View Access Permission
        $this->ViewAccess = Permit::AccessPermission('M3bookings-view');
//        if (!$this->ViewAccess)
//            return redirect('errors/401');
        //Check Print Access Permission
        $this->PrintAccess = Permit::AccessPermission('M3bookings-print');

        $this->table = 'subscribers_package_details';
        $this->bookingTable = 'bookings';

        $bookingList = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('registered_users.name', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end'
                        , 'class_schedules.schedule_date', 'b.profit', 'b.created_at')
                ->where(array('b.vendor_id' => VendorDetail::getID()));


        //if Request having Date Range
        if ($request->has('start_date') && $request->get('start_date') != '' && $request->has('end_date') && $request->get('end_date') != '') {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $session_array['start_date'] = $start_date;
            $session_array['end_date'] = $end_date;
            Session::set('reportModule3ClassBookings', $session_array);
            Session::flash('reportModule3ClassBookings', Session::get('reportModule3ClassBookings'));
            $bookingList->whereBetween('class_schedules.schedule_date', [$start_date, $end_date]);
        }
        // if Request having Subscriber id
        if ($request->has('subscriber_id') && $request->get('subscriber_id') != 0) {
            $ID = $request->get('subscriber_id');
            Session::set('reportModule3ClassBookings', ['subscriber_id' => $ID]);
            Session::flash('reportModule3ClassBookings', Session::get('reportModule3ClassBookings'));
            $bookingList->where('b.subscriber_id', $ID);
        }

        // if Request having Class id
        if ($request->has('class_id') && $request->get('class_id') != 0) {
            $ID = $request->get('class_id');
            Session::set('reportModule3ClassBookings', ['class_id' => $ID]);
            Session::flash('reportModule3ClassBookings', Session::get('reportModule3ClassBookings'));
            $bookingList->where('b.class_master_id', $ID);
        }

        //if Request having Start Time And End Time        
        if ($request->has('start_time') && $request->get('start_time') != '' && $request->has('end_time') && $request->get('end_time') != '') {
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');

            $datetime = new DateTime();

            $newDate = $datetime->createFromFormat('h:m:A', $start_time);
            $start_time = $newDate->format('H:i:s');

            $eDate = $datetime->createFromFormat('h:m:A', $end_time);
            $end_time = $eDate->format('H:i:s');

            //dd($start_time);

            $session_array['start_time'] = $start_time;
            $session_array['end_time'] = $end_time;
            Session::set('reportModule3ClassBookings', $session_array);
            Session::flash('reportModule3ClassBookings', Session::get('reportModule3ClassBookings'));
            $bookingList->whereBetween('class_schedules.start', [$start_time, $end_time]);
        }

        $bookingHistory = $bookingList->get();

        $Count = $bookingHistory->count();

        $TotalProfit = $bookingList->sum('b.profit');

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($bookingHistory)
                            ->editColumn('start', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->start);
                                return $newYear->format('h:i:A');
                            })
                            ->editColumn('end', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->end);
                                return $newYear->format('h:i:A');
                            })
                            ->editColumn('schedule_date', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->schedule_date);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('created_at', function ($bookingHistory) {
                                $newYear = new Carbon($bookingHistory->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->with('Count', $Count)
                            ->with('TotalProfit', $TotalProfit)
                            ->make();
        }

        $Classes = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('class_master.name_en', 'classes.class_master_id As id')
                ->groupby('b.class_master_id')
                ->get();

        $Subscribers = DB::table($this->bookingTable . ' As b')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->select('registered_users.id', 'registered_users.name')
                ->groupby('registered_users.id')
                ->get();

        return view('fitflowVendor.module3.reports.bookings')
                        ->with('PrintAccess', $this->PrintAccess)
                        ->with('Classes', $Classes)
                        ->with('TotalProfit', $TotalProfit)
                        ->with('Count', $Count)
                        ->with('Subscribers', $Subscribers)
                        ->with('ViewAccess', $this->ViewAccess);
        ;
    }

}
