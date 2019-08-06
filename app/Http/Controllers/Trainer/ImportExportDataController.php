<?php

namespace App\Http\Controllers\Admin;

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

class ImportExportDataController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //Get all Import table
        $ImportExportData = DB::table('importdata_tables')
                ->select('table_name', 'created_at', 'id')
                ->where('status', 1)
                ->get();
      
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($ImportExportData)
                            ->editColumn('created_at', function ($ImportExportData) {
                                $newYear = new Carbon($ImportExportData->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('action', function ($ImportExportData) {
                                return '<a href="/admin/importexportdata/' . $ImportExportData->id . '" class="btn btn-primary tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Import Excel Data" data-original-title="Import Excel Data">'
                                        . '<i class="fa fa-cloud-upload"></i></a> '
                                        . '<a href="/admin/importexportdata/excel/' . $ImportExportData->id . '" class="btn btn-success tooltip-primary btn-small" data-toggle="tooltip" data-placement="top"
                                         title="Export Data" data-original-title="Export Excel Data"><i class="fa fa-cloud-download"></i></a> ' .
                                        '<a data-val="' . $ImportExportData->id . '" class="btn btn-gold tooltip-primary btn-small importedlist" data-toggle="modal" data-target="#myModal" '
                                        . 'title="Imported List" data-original-title="Imported List"><i class="fa fa-list"></i></a>';
                            })
                            ->editColumn('table_name', function ($ImportExportData) {
                                return ucfirst($ImportExportData->table_name);
                            })
                            ->make();
        }
        return view('admin.importexportdata.index');
        //return view('admin.importdata.create', compact('import_tables'));
    }

    public function importdata($id) {

        //Get all User Role
        $import_tables = DB::table('importdata_tables')
                ->select('table_name', 'id', 'image')
                ->where('status', 1)
                ->where('id', $id)
                ->first();

        return view('admin.importexportdata.create')
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

        // validate
        $validator = Validator::make($request->all(), [
                    'table_name' => 'required',
                    'import_file' => 'required|max:10000|mimes:xlsx,xls'
        ]);


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/importexportdata/' . $request->imported_table_id)
                            ->withErrors($validator)->withInput();
        } else {

            if ($request->hasFile('import_file')) {

                $import_file = $request->file('import_file');
                $filename = $request->table_name . '_' . time() . '.' . $import_file->getClientOriginalExtension();
                $destinationPath = public_path('importexceldata_tables/');
                $import_file->move($destinationPath, $filename);
                $path = public_path('importexceldata_tables/' . $filename);
                $table_name = $request->table_name;
                $imported_id = $request->imported_table_id;

                Excel::filter('chunk')->load($path)->chunk(10000, function($results) use($table_name, $imported_id, $filename) {
                  
                    if (!empty($results) && $results->count()) {

                        foreach ($results->toArray() as $key => $value) {
                            if (!empty($value)) {

                                foreach ($value as $v) {
                                    $v = array_add($v, 'created_at', Carbon::now());
                                    $v = array_add($v, 'updated_at', Carbon::now());
                                    $insert[] = $v;
                                }
                            }
                        }
                        //Insert Chunk data
                        if (!empty($insert)) {

                            DB::table($table_name)->insert($insert);

                            //Insert data into imported_excel_data_tables
                            $input['imported_table_id'] = $imported_id;
                            $input['imported_file'] = $filename;
                            ImportedFiles::create($input);
                        }
                    }
                },false);
                Session::flash('message', config('global.importRecord'));
                return redirect('admin/importexportdata');


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

    //Export Data using Excelsheet
    public function exportdata($id) {

        //Get all Import table
        $import_tables = DB::table('importdata_tables')
                ->select('table_name', 'id')
                ->where('status', 1)
                ->where('id', $id)
                ->first();



        // Initialize the array which will be passed into the Excel
        // generator.
        $tableArray = [];


        // Execute the query used to retrieve the data.
        //Check and execute query according to table id        
        if ($id == 1) {
            $table_datas = DB::table($import_tables->table_name)
                    ->select('name_en', 'name_ar', 'created_at')
                    ->where('status', 1)
                    ->get()
                    ->toArray();

            // Define the Excel spreadsheet headers
            $tableArray[] = ['name_en', 'name_ar', 'created_at'];
        }
        // Convert each member of the returned collection into an array,
        // and append it to the array.
        foreach ($table_datas as $table_data) {
            $tableArray[] = (array) $table_data;
        }


        // Generate and return the spreadsheet
        Excel::create($import_tables->table_name, function($excel) use($tableArray, $import_tables ) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle($import_tables->table_name);
            $excel->setCreator('Creativity')->setCompany('Fitflow');
            $excel->setDescription($import_tables->table_name);

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($tableArray) {
                $sheet->fromArray($tableArray, null, 'A1', false, false);
            });
        })->download('xlsx');

        return redirect('admin/importexportdata');
    }

    //Imported Data List
    public function importedlist($id) {
        //Ajax request
        if (request()->ajax()) {
            $importedfiles = ImportedFiles::where('imported_table_id', '=', $id)
                    ->select('imported_files.*', 'importdata_tables.table_name')
                    ->join('importdata_tables', 'importdata_tables.id', '=', 'imported_files.imported_table_id')
                    ->orderby('created_at', 'DESC')
                    ->get();
            $returnHTML = view('admin.importexportdata.importedlist')->with('importedfiles', $importedfiles)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        }
    }

}
