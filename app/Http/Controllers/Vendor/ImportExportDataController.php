<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Excel;
use Carbon\Carbon;
use App\Models\Admin\Users;
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

class ImportExportDataController extends Controller {

    protected $guard = 'vendor';

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:uploadMembers');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress');
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
                ->where('table_type', 1)
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
                                    $str .= '<a href="' . url($this->configName . '/importexportdata') . '/' . $ImportExportData->id . '" class="btn btn-primary tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Import Excel Data" data-original-title="Import Excel Data">'
                                            . '<i class="fa fa-cloud-upload"></i></a> ';
                                }
                                $str .= '<a href="' . url($this->configName . '/importexportdata/excel') . '/' . $ImportExportData->id . '" class="btn btn-success tooltip-primary btn-small" data-toggle="tooltip" data-placement="top"
                                         title="Export Data" data-original-title="Export Excel Data"><i class="fa fa-cloud-download"></i></a> ' .
                                        '<a data-val="' . $ImportExportData->id . '" class="btn btn-gold tooltip-primary btn-small importedlist" data-toggle="modal" data-target="#myModal" '
                                        . 'title="Imported List" data-original-title="Imported List"><i class="fa fa-list"></i></a>'
                                        . ' <a data-val="' . $ImportExportData->id . '" class="btn btn-orange tooltip-primary btn-small import_datafields" data-toggle="modal" data-target="#' . $ImportExportData->table_name . '" '
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
        return view('fitflowVendor.importexportdata.index');
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

        return view('fitflowVendor.importexportdata.create')
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
                        //'import_file' => 'required|max:50000|mimes:xlsx,doc,docx,ppt,pptx,ods,odt,odp,application/csv,application/excel,
                        // application/vnd.ms-excel, application/vnd.msexcel', 
                        //'import_file' => 'required|max:10000|in:doc,csv,xlsx,xls,docx,ppt,odt,ods,odp',
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

                                            //if ($collection[$colString] == 'start_date') {
                                            //$newDate = $datetime->createFromFormat('d/m/Y', $value[$hv]);
                                            // $value[$hv] = $newDate->format('Y-m-d');
                                            //}
                                            // if ($collection[$colString] == 'end_date') {
                                            //  $newDate = $datetime->createFromFormat('d/m/Y', $value[$hv]);
                                            // $value[$hv] = $newDate->format('Y-m-d');
                                            // }
                                            if (Arr::exists($value, $hv)) {
                                                $v[$collection[$colString]] = $value[$hv];
                                            }

                                            if (!Arr::exists($value, $hv)) {
                                                $v[$collection[$colString]] = null;
                                            }
                                        }
                                    }
                                    $v['key'] = $key;
                                    // $v['updated_at'] = Carbon::now();
                                    // $startDate = $datetime->createFromFormat('d/m/Y', $v['start_date']);
                                    //$endDate = $datetime->createFromFormat('d/m/Y', $v['end_date']);
//                                   $v = array_add($v, 'created_at', Carbon::now());
//                                    $v = array_add($v, 'updated_at', Carbon::now());
//                                    $v = array_add($v, 'name', $v['name']);
//                                    $v = array_add($v, 'email', $v['email']);
//                                    $v = array_add($v, 'mobile', $v['mobile']);
//                                    $v = array_add($v, 'package_name', $v['package_name']);
//                                    $v = array_add($v, 'start_date', $v['start_date']);
//                                    $v = array_add($v, 'end_date', $v['end_date']);
                                    //}
                                }
                                //dd($v);
                                // $array = array_push($array,$v);
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


                    //Insert record in members table
                    //Insert record into members table
                    $temp = DB::table('temp')
                            ->select('json_data')
                            ->where('imported_file_id', $file_id)
                            ->get();


                    foreach ($temp as $items) {

                        foreach (json_decode($items->json_data) as $data) {

                            $datetime = new DateTime();
                            if (!is_null($data->start_date)) {
                                $data->start_date = trim($data->start_date);
                                //$newDate = $datetime->createFromFormat('m/d/Y', $data->start_date);
                                $table_input['start_date'] = $data->start_date;
                            }
                            if (!is_null($data->end_date)) {
                                $data->end_date = trim($data->end_date);
                                //$newDate = $datetime->createFromFormat('m/d/Y', $data->end_date);
                                $table_input['end_date'] = $data->end_date;
                            }
                            if (!is_null($data->package_name)) {
                                $data->package_name = trim($data->package_name);
                                $packageID = DB::table('vendor_packages')->select('id')->where('name_en', $data->package_name)->where('vendor_id', VendorDetail::getID())->first();

                                $table_input['package_id'] = null;
                                $table_input['package_name'] = null;
                                if (!is_null($packageID)) {
                                    $table_input['package_id'] = $packageID->id;
                                    $table_input['package_name'] = $data->package_name;
                                }
                            }
                            if (!is_null($data->gender)) {
                                $data->gender = trim($data->gender);
                                $data->gender = ucfirst($data->gender);
                                $gender = str_is('M*', $data->gender) ? 'Male' : $data->gender;
                                $gender = str_is('F*', $gender) ? 'Female' : $gender;
                                $gender = str_is('K*', $gender) ? 'Kid' : $gender;
                                $genderID = DB::table('gender_types')->select('id')->where('name_en', $gender)->first();
                                $table_input['gender_id'] = null;
                                if (!is_null($genderID)) {
                                    $table_input['gender_id'] = $genderID->id;
                                }
                            }

                            $cash = 0;
                            if (array_has($data, 'cash')) {
                                $data->cash = trim($data->cash);
                                $cash = $data->cash;
                            }

                            $knet = 0;
                            if (array_has($data, 'knet')) {
                                $data->knet = trim($data->knet);
                                $knet = $data->knet;
                            }
                            $table_input['created_at'] = Carbon::now();
                            $table_input['updated_at'] = Carbon::now();
                            $table_input['name'] = trim($data->name);
                            $table_input['email'] = trim($data->email);
                            $table_input['mobile'] = trim($data->mobile);

                            //$this->insertIgnore($input,'v2_members');
                            //DB::table('v2_members')->insert($input);
                            $table = VendorDetail::getPrefix() . 'members';
                            $this->uniqueMobile($data->mobile, $table, $table_input, $data->key, $cash, $knet);
                        }
                    }

                    Session::flash('message', config('global.importRecord'));
                    return response()->json(['success' => 1]);
                    //return redirect($this->configName . '/members');
                }





//                $data = Excel::load($path, function($reader) {
//                            $reader->ignoreEmpty();
//                        })->get();
//                if (!empty($data) && $data->count()) {
//
//                    foreach ($data->toArray() as $key => $value) {
//
//                        if (!empty($value)) {
//
//                            foreach ($value as $v) {
//                                $v = array_add($v, 'created_at', Carbon::now());
//                                $v = array_add($v, 'updated_at', Carbon::now());
//                                $insert[] = $v;
//                            }
//                        }
//                    }
//
//                    if (!empty($insert)) {
//
//                        DB::table($request->table_name)->insert($insert);
//
//                        //Insert data into imported_excel_data_tables
//                        $input['imported_table_id'] = $request->imported_table_id;
//                        $input['imported_file'] = $filename;
//                        ImportedFiles::create($input);
//
//                        Session::flash('message', 'Records successfully Imported!');
//
//                        return redirect('admin/importexportdata');
//                    }
//                }
            }
        }
    }

    //Fields update for table
    public function updateFields(Request $request) {

        //Ajax request
        if (request()->ajax()) {
            // validate
            $validator = Validator::make($request->all(), [
                        'name' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'email' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'gender' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'mobile' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'package_name' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'start_date' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'end_date' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'cash' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
                        'knet' => 'required|regex:/^[\pL\s\-]+$/u|max:3',
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
        $table = VendorDetail::getPrefix() . 'members';

        $table_datas = DB::table($table)
                ->select('name', 'email', 'mobile', 'package_name', 'start_date', 'end_date')
                ->where('status', 1)
                ->get()
                ->toArray();

        // Define the Excel spreadsheet headers
        $tableArray[] = ['name', 'email', 'mobile', 'package_name', 'start_date', 'end_date'];

        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($table_datas as $table_data) {
            if (Arr::exists($table_data, 'start_date')) {

                if (!is_null($table_data->start_date)) {
                    $sdate = new Carbon($table_data->start_date);
                    $table_data->start_date = $sdate->format('d/m/Y');
                }
            }
            if (Arr::exists($table_data, 'end_date')) {
                if (!is_null($table_data->end_date)) {
                    $edate = new Carbon($table_data->end_date);
                    $table_data->end_date = $edate->format('d/m/Y');
                }
            }

            $tableArray[] = (array) $table_data;
        }


        // Generate and return the spreadsheet
        Excel::create('Members', function($excel) use($tableArray) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Members');
            $excel->setCreator('Creativity')->setCompany(config('global.appTitle'));
            $excel->setDescription('Members');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect($this->configName . '/importexportdata');
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
            $returnHTML = view('fitflowVendor.importexportdata.importedlist')->with('importedfiles', $importedfiles)->render();
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

        $returnHTML = view('fitflowVendor.importexportdata.excelindex')->with('excelIndexes', $collection)->render();
        return response()->json(array('success' => true, 'html' => $returnHTML));
    }

    //for dublicate entry ignore
//    public  function insertIgnore($array, $table){ 
//                 DB::insert('INSERT IGNORE INTO '.$table.' ('.implode(',',array_keys($array)).
//        ') values (?'.str_repeat(',?',count($array) - 1).')',array_values($array));    
//   }

    public function uniqueMobile($mobile, $table, $array, $key, $cash, $knet) {
        $key_error = array();
        //check mobile number
        $count = DB::table($table)->where('mobile', $mobile)->count();
        if (!is_null($mobile) && $count == 0) {
            $packageID = DB::table('vendor_packages')->where('id', $array['package_id'])->where('vendor_id', VendorDetail::getID())->first();

            $array['price'] = $packageID->price;
            $array['cash'] = $cash;
            $array['knet'] = $knet;
            //Set notification date
            $notify = new Carbon($array['end_date']);
            $notify->subDays($packageID->expired_notify_duration);
            $array['notification_date'] = $notify->format('Y-m-d');
            $array['package_name_ar'] = $packageID->name_ar;

            $lastID = DB::table($table)->insertGetId($array);

            $sale_setting = VendorDetail::getSalesCountDate();

            // check package start date is greater than or equal to vendor sales count date if true then insert into subscriber package table.
            if ($array['start_date'] >= $sale_setting) {
                //Subscription package            
                $package_array['member_id'] = $lastID;
                $package_array['module_id'] = 1;
                $package_array['vendor_id'] = VendorDetail::getID();
                $package_array['package_id'] = $array['package_id'];
                $package_array['start_date'] = $array['start_date'];
                $package_array['end_date'] = $array['end_date'];
                $package_array['name_en'] = $packageID->name_en;
                $package_array['name_ar'] = $packageID->name_ar;
                $package_array['description_en'] = $packageID->description_en;
                $package_array['description_ar'] = $packageID->description_ar;
                $package_array['area_name_en'] = VendorDetail::getArea(1,$array['package_id']);
                $package_array['area_name_ar'] = VendorDetail::getArea(2,$array['package_id']);
                $package_array['num_days'] = $packageID->num_days;
                $package_array['price'] = $packageID->price;
                $package_array['profit'] = VendorDetail::getProfitCommission($packageID->price, 0);
                $package_array['commission'] = VendorDetail::getProfitCommission($packageID->price, 1);
                $package_array['cash'] = $cash;
                $package_array['knet'] = $knet;
                $exp = new Carbon($array['end_date']);
                $exp->subDays($packageID->expired_notify_duration);
                $package_array['notification_date'] = $exp->format('Y-m-d');
                $package_array['created_at'] = Carbon::now();
                $package_array['updated_at'] = Carbon::now();

                $subscription_table = VendorDetail::getPrefix() . 'subscribers_package_details';
                $admin_subscription_table = 'subscribers_package_details';
                DB::table($admin_subscription_table)->insert($package_array);
                $subscriberedLastID = DB::table($subscription_table)->insertGetId($package_array);

                $dt = Carbon::now();
                $invoice_array['created_at'] = Carbon::now();
                $invoice_array['updated_at'] = Carbon::now();
                $invoice_array['member_id'] = $lastID;
                $invoice_array['receipt_num'] = $dt->year . $dt->month . $dt->day . $dt->hour . $lastID;
                $invoice_array['subscribed_package_id'] = $subscriberedLastID;
                $invoice_array['collected_by'] = VendorDetail::getID();
                $invoice_array['cash'] = $cash;
                $invoice_array['knet'] = $knet;
                $invoice_array['price'] = $packageID->price;
                $invoice_array['package_id'] = $array['package_id'];
                $invoice_array['start_date'] = $array['start_date'];
                $invoice_array['end_date'] = $array['end_date'];
                $invoice_array['package_name'] = $packageID->name_en;

                //Invoice Table
                $invoice_table = VendorDetail::getPrefix() . 'member_invoices';
                DB::table($invoice_table)->insert($invoice_array);
            }
        } else {
            Session::push('excel_key', $key + 2);
            Session::flash('error', 'Excel Row Key' . json_encode(Session::get('excel_key')) . 'cannot be imported');
            //Session::forget('excel_key');
        }
    }

}
