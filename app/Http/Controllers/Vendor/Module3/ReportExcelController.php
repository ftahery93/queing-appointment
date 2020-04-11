<?php

namespace App\Http\Controllers\Vendor\Module3;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Excel;
use Carbon\Carbon;
use PHPExcel_Cell;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\VendorDetail;

class ReportExcelController extends Controller {

    protected $guard = 'vendor';
    protected $configName;
    protected $PrintAccess;

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor');
        //$this->middleware('vendorPermission:M3bookings');
    }

    /**
     * Display a listing of the Favourites.
     */
    public function bookings(Request $request) {

        //Check View Access Permission
        $this->PrintAccess = Permit::AccessPermission('M3bookings-print');
        if (!$this->PrintAccess)
            return redirect('errors/401');

        $this->table = 'subscribers_package_details';
        $this->bookingTable = 'bookings';

        $bookingList = DB::table($this->bookingTable . ' As b')
                ->join('classes', 'classes.id', '=', 'b.class_id')
                ->join('class_schedules', 'class_schedules.id', '=', 'b.schedule_id')
                ->join('registered_users', 'b.subscriber_id', '=', 'registered_users.id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('registered_users.name', DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS class_name'), 'class_schedules.start', 'class_schedules.end', 'class_schedules.schedule_date', 'b.profit', 'b.created_at')
                ->where(array('b.vendor_id' => VendorDetail::getID()));

        if (Session::has('reportModule3ClassBookings')) {
            $val = Session::get('reportModule3ClassBookings');
            if (Session::has('reportModule3ClassBookings.start_date')) {
                $bookingList->whereBetween('class_schedules.schedule_date', [$val['start_date'], $val['end_date']]);
            }
            // if Request having Subscriber id
            if (Session::has('reportModule3ClassBookings.subscriber_id')) {
                $val = Session::get('reportModule3ClassBookings');
                $ID = $val['subscriber_id'];
                $bookingList->where('b.subscriber_id', $ID);
            }

            // if Request having Class id
            if (Session::has('reportModule3ClassBookings.class_id')) {
                $val = Session::get('reportModule3ClassBookings');
                $ID = $val['class_id'];
                $bookingList->where('b.class_id', $ID);
            }

            // if Request having Start Time and End Time
            if (Session::has('reportModule3ClassBookings.start_time')) {
                $val = Session::get('reportModule3ClassBookings');
                $bookingList->whereBetween('class_schedules.start', [$val['start_time'], $val['end_time']]);
            }
        }


        $bookingHistory = $bookingList->get()->toArray();

        $Count = $bookingList->count();
        $TotalProfit = $bookingList->sum('b.profit');

        // Define the Excel spreadsheet headers
        $tableArray[] = ['Subscriber', 'Class Name', 'Start Time', 'End Time', 'Schedule Date', 'Created On'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($bookingHistory as $table_data) {
            if (!is_null($table_data->schedule_date)) {
                $sdate = new Carbon($table_data->schedule_date);
                $table_data->schedule_date = $sdate->format('d/m/Y');
            }
            if (!is_null($table_data->start)) {
                $start = new Carbon($table_data->start);
                $table_data->start = $start->format('h:i:A');
            }
            if (!is_null($table_data->end)) {
                $end = new Carbon($table_data->end);
                $table_data->end = $end->format('h:i:A');
            }
            if (!is_null($table_data->created_at)) {
                $created_at = new Carbon($table_data->created_at);
                $table_data->created_at = $created_at->format('d/m/Y');
            }
            $tableArray[] = (array) $table_data;
        }

        $count = count($tableArray) + 1;

        $tableArray[$count]['Total Profit'] = 'Total Profit (' . config('global.amountCurrency') . '): ' . $TotalProfit;
        $tableArray[$count]['Bookings'] = 'Bookings: ' . $Count;


        // Generate and return the spreadsheet
        Excel::create(ucfirst(config('global.M3')) . ' Booking History', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle(ucfirst(config('global.M3')) . ' Booking History');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription(ucfirst(config('global.M3')) . ' Booking History');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/m3/report/bookings');
    }

}
