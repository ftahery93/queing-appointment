<?php

namespace App\Http\Controllers\Vendor\Module2;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Excel;
use Carbon\Carbon;
use App\Models\Admin\ImportedFiles;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use Illuminate\Support\Arr;
use App\Helpers\VendorDetail;
use PHPExcel_Cell;
use DateTime;

class UploadScheduleController extends Controller {

    protected $guard = 'vendor';

    public function __construct() {
        $this->middleware($this->guard);
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M2');
        $this->middleware('vendorPermission:uploadSchedule');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        // dd( Session::get('excel_key'));
        //Get all Import table
        $ImportExportData = DB::table('importdata_tables')
                ->select('table_name', 'created_at', 'id', 'fields')
                ->where('status', 1)
                ->where('table_type', 2)
                ->where('vendor_id', VendorDetail::getID())
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($ImportExportData)
                            ->editColumn('created_at', function ($ImportExportData) {
                                $newYear = new Carbon($ImportExportData->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($ImportExportData) {
                                $str = '';
                                if (!is_null($ImportExportData->fields)) {
                                    $str .= '<a href="' . url($this->configName . '/uploadSchedule') . '/' . $ImportExportData->id . '" class="btn btn-primary tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Upload Schedule" data-original-title="Upload Schedule">'
                                            . '<i class="fa fa-cloud-upload"></i></a> ';
                                }
                                $str .= '<a href="' . url($this->configName . '/uploadSchedule/excel') . '/' . $ImportExportData->id . '" class="btn btn-success tooltip-primary btn-small" data-toggle="tooltip" data-placement="top"
                                         title="Export Data" data-original-title="Export Excel Data"><i class="fa fa-cloud-download"></i></a> ' .
                                        '<a data-val="' . $ImportExportData->id . '" class="btn btn-gold tooltip-primary btn-small importedlist" data-toggle="modal" data-target="#myModal" '
                                        . 'title="Imported List" data-original-title="Imported List"><i class="fa fa-list"></i></a>'
                                        . ' <a data-val="' . $ImportExportData->id . '" class="btn btn-orange tooltip-primary btn-small import_datafields" data-toggle="modal" data-target="#upload_schedule"'
                                        . 'title="Insert Excel Field Index" data-original-title="Insert Excel Field Index"><i class="fa fa-asterisk"></i></a>'
                                        . ' <a data-val="' . $ImportExportData->id . '" class="btn btn-info tooltip-primary btn-small imported_index" data-toggle="modal" data-target="#myModal2"'
                                        . 'title="Excel Field Index" data-original-title="Excel Field Index"><i class="fa fa-file"></i></a>';

                                return $str;
                            })
                            ->editColumn('table_name', function ($ImportExportData) {
                                return ucfirst($ImportExportData->table_name);
                            })
                            ->make();
        }
        return view('fitflowVendor.module2.uploadSchedule.index');
        //return view('admin.importdata.create', compact('import_tables'));
    }

    public function importdata(Request $request, $id) {
        $id = $request->id;
        //Get all User Role
        $import_tables = DB::table('importdata_tables')
                ->select('table_name', 'id', 'image')
                ->where('status', 1)
                ->where('id', $id)
                ->where('vendor_id', VendorDetail::getID())
                ->first();

        return view('fitflowVendor.module2.uploadSchedule.create')
                        ->with('import_tables', $import_tables);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //Import Data using Excelsheet
    public function store(Request $request) {

        //Ajax request
        if (request()->ajax()) {
            Session::forget('excel_key');
            // validate
            $validator = Validator::make($request->all(), [
                        'table_name' => 'required',
                        'import_file' => 'required|max:50000|mimes:xls,xlsx,csv,txt',
            ]);


            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => config('global.importError')]);
            } else {

                if ($request->hasFile('import_file')) {

                    $import_file = $request->file('import_file');
                    $filename = $request->table_name . '_' . time() . '.' . $import_file->getClientOriginalExtension();
                    $destinationPath = public_path('importexceldata_tables/');
                    $import_file->move($destinationPath, $filename);
                    $path = public_path('importexceldata_tables/' . $filename);
                    $table_name = $request->table_name;
                    $imported_id = $request->imported_table_id;

                    //Get fields
                    $import_tables = DB::table('importdata_tables')
                            ->select('fields')
                            ->where('id', $request->imported_table_id)
                            ->first();

                    $collection = collect(json_decode($import_tables->fields, true));
                    $collection = json_encode($collection, true);
                    $collection = array_flip(json_decode($collection, true));
                    $collection = array_change_key_case($collection, CASE_UPPER);

                    //Insert data into imported_excel_data_tables
                    $input['imported_table_id'] = $imported_id;
                    $input['imported_file'] = $filename;
                    $file_id = ImportedFiles::create($input)->id;


                    Excel::filter('chunk')->selectSheetsByIndex(0)->load($path)->chunk(500, function($results) use($table_name, $file_id, $filename, $collection) {
                        $headerRow = $results->first()->keys()->toArray();
                        // dd($results->count());
                        if (!empty($results) && $results->count()) {
                            $v = [];
                            $array = array();
                            foreach ($results->toArray() as $key => $value) {

                                if (!empty($value)) {

                                    foreach ($headerRow as $hk => $hv) {
                                        $datetime = new DateTime();
                                        $colString = PHPExcel_Cell::stringFromColumnIndex($hk);

                                        //Check Index exist in collection                                    
                                        if (Arr::exists($collection, $colString)) {

                                            if (Arr::exists($value, $hv)) {
                                                $v[$collection[$colString]] = $value[$hv];
                                            }

                                            if (!Arr::exists($value, $hv)) {
                                                $v[$collection[$colString]] = null;
                                            }
                                        }
                                    }
                                    $v['key'] = $key;
                                }

                                $insert[] = $v;
                            }
                            //Insert Chunk data

                            if (!empty($insert)) {
                                $json = json_encode($insert);
                                $inp['json_data'] = $json;
                                $inp['vendor_id'] = VendorDetail::getID();
                                $inp['imported_file_id'] = $file_id;
                                $inp['num_records'] = count($insert);
                                $inp['created_at'] = Carbon::now();
                                $inp['updated_at'] = Carbon::now();
                                DB::table('temp')->insert($inp);
                            }
                        }
                    }, true);


                    $temp = DB::table('temp')
                            ->select('json_data')
                            ->where('imported_file_id', $file_id)
                            ->get();


                    foreach ($temp as $items) {

                        foreach (json_decode($items->json_data) as $data) {

                            $datetime = new DateTime();
                            if (!is_null($data->start_time)) {
                                $data->start_time = trim($data->start_time);
                                $newDate = $datetime->createFromFormat('h:m:A', $data->start_time);
                                $table_input['start'] = $newDate->format('H:i:s');
                            }
                            if (!is_null($data->schedule_date)) {
                                $table_input['schedule_date'] = $data->schedule_date;
                            }
                            if (!is_null($data->class_name) && !is_null($data->branch_name)) {
                                $data->class_name = trim($data->class_name);
                                //Get Class name
                                $classMasterID = DB::table('class_master')->select('id')->where('name_en', $data->class_name)->where('vendor_id', VendorDetail::getID())->first();

                                //Get Branch name
                                $branchID = DB::table('vendor_branches')->select('id')->where('name_en', $data->branch_name)->where('vendor_id', VendorDetail::getID())->first();

                                $classID = DB::table('classes')->select('id', 'hours', 'available_seats', 'fitflow_seats')
                                                ->where('branch_id', $branchID->id)
                                                ->where('class_master_id', $classMasterID->id)
                                                ->where('vendor_id', VendorDetail::getID())
                                                ->where('approved_status', 1)->first();

                                $count = sizeof($classID);
                                if ($count != 0) {
                                    $table_input['class_id'] = $classID->id;
                                    $table_input['gym_seats'] = $classID->available_seats;
                                    $table_input['fitflow_seats'] = $classID->fitflow_seats;

                                    //check start time exist
                                    if (Arr::exists($table_input, 'start')) {
                                        $classID->hours = trim($classID->hours);
                                        $addHours = new Carbon($table_input['start']);
                                        $addHours->addMinutes($classID->hours);
                                        $table_input['end'] = $addHours->format('H:i:s');
                                    }
                                    $table_input['created_at'] = Carbon::now();
                                    $table_input['updated_at'] = Carbon::now();
                                    $table_input['vendor_id'] = VendorDetail::getID();
                                    DB::table('class_schedules')->insert($table_input);
                                } else {
                                    Session::push('excel_key', $data->key + 2);
                                    Session::flash('error', 'Excel Row Key' . json_encode(Session::get('excel_key')) . 'cannot be imported');
                                    //Session::forget('excel_key');
                                }
                            } else {
                                Session::push('excel_key', $data->key + 2);
                                Session::flash('error', 'Excel Row Key' . json_encode(Session::get('excel_key')) . 'cannot be imported');
                                //Session::forget('excel_key');
                            }
                        }
                    }

                    Session::flash('message', config('global.importRecord'));
                    return response()->json(['success' => 1]);
                }
            }
        }
    }

    //Fields update for table
    public function updateFields(Request $request) {

        //Ajax request
        if (request()->ajax()) {
            // validate
            $validator = Validator::make($request->all(), [
                        'branch_name' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'class_name' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'start_time' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'schedule_date' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
            ]);

            // validation failed
            if ($validator->fails()) {
                return response()->json(['error' => config('global.fieldError')]);
            } else {

                $input = $request->except('_token', 'table_id');
                $collection = collect($input);
                $fields = $collection->toJson();

                DB::table('importdata_tables')->where('id', $request->table_id)
                        ->update([
                            'fields' => $fields
                ]);

                return response()->json(['response' => config('global.addedRecords')]);
            }
        }
    }

    //Export Data using Excelsheet
    public function exportdata($id) {

        // Initialize the array which will be passed into the Excel
        // generator.
        $tableArray = [];


        // Execute the query used to retrieve the data.
        //Check and execute query according to table id  
        $table = 'class_schedules';

        $table_datas = DB::table($table)
                ->join('classes', 'classes.id', '=', 'class_schedules.class_id')
                ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                ->select('vendor_branches.name_en', 'class_master.name_en', 'class_schedules.schedule_date', 'class_schedules.start', 'class_schedules.end')
                ->where('classes.vendor_id', VendorDetail::getID())
                ->get()
                ->toArray();


        // Define the Excel spreadsheet headers
        $tableArray[] = ['branch name', 'class name', 'schedule', 'start', 'end'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($table_datas as $table_data) {
            if (Arr::exists($table_data, 'start')) {

                if (!is_null($table_data->start)) {
                    $sdate = new Carbon($table_data->start);
                    $table_data->start = $sdate->format('h:m:A');
                }
            }
            if (Arr::exists($table_data, 'end')) {
                if (!is_null($table_data->end)) {
                    $edate = new Carbon($table_data->end);
                    $table_data->end = $sdate->format('h:m:A');
                }
            }

            $tableArray[] = (array) $table_data;
        }


        // Generate and return the spreadsheet
        Excel::create(ucfirst(config('global.M2')) . ' Class Schedule', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle(ucfirst(config('global.M2')) . ' Class Schedule');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription(ucfirst(config('global.M2')) . ' Class Schedule');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/uploadSchedule');
    }

    //Imported Data List
    public function importedlist(Request $request, $id) {
        $id = $request->id;
        //Ajax request
        if (request()->ajax()) {
            $importedfiles = ImportedFiles::where('imported_table_id', '=', $id)
                    ->select('imported_files.*', 'importdata_tables.table_name')
                    ->join('importdata_tables', 'importdata_tables.id', '=', 'imported_files.imported_table_id')
                    ->orderby('created_at', 'DESC')
                    ->get();
            $returnHTML = view('fitflowVendor.module2.uploadSchedule.importedlist')->with('importedfiles', $importedfiles)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }
    }

    //Excel Index
    public function excelindex(Request $request, $id) {
        $id = $request->id;
        //Get all User Role
        $excelIndexes = DB::table('importdata_tables')
                ->select('fields')
                ->where('status', 1)
                ->where('id', $id)
                ->where('vendor_id', VendorDetail::getID())
                ->first();

        $collection = collect(json_decode($excelIndexes->fields, true));
        $collection = json_encode($collection, true);
        $collection = array_flip(json_decode($collection, true));
        $collection = array_change_key_case($collection, CASE_UPPER);

        $returnHTML = view('fitflowVendor.module2.uploadSchedule.excelindex')->with('excelIndexes', $collection)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    public function addRecords($mobile, $table, $array, $key) {
        $key_error = array();
        //check mobile number
        $count = DB::table($table)->where('mobile', $mobile)->count();
        if (!is_null($mobile) && $count == 0) {
            
        } else {
            Session::push('excel_key', $key + 2);
            Session::flash('error', 'Excel Row Key' . json_encode(Session::get('excel_key')) . 'cannot be imported');
            //Session::forget('excel_key');
        }
    }

}
