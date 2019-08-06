<?php

namespace App\Http\Controllers\Vendor\Module4;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\Option;
use App\Models\Vendor\ProductOptionValue;
use App\Models\Vendor\OptionValue;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use Image;

class OptionController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:options');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M4');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('options-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('options-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('options-edit');

        $Option = Option::
                select('options.id', 'options.name_en', 'options.sort_order')
                ->where('vendor_id', VendorDetail::getID())
                ->get();



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Option)
                            ->editColumn('id', function ($Option) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Option->id . '">';
                            })
                            ->editColumn('action', function ($Option) {
                                if ($this->EditAccess)
                                    return '<a href="' . url($this->configName . '/options') . '/' . $Option->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a> ';
                            })
                            ->make();
        }

        return view('fitflowVendor.module4.options.index')
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
        $this->CreateAccess = Permit::AccessPermission('options-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');


        return view('fitflowVendor.module4.options.create');
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
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'option_value_name_en.*' => 'required',
                    'option_value_name_ar.*' => 'required',
        ]);

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/options/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only('name_en', 'name_ar', 'sort_order');
            $input['vendor_id'] = VendorDetail::getID();
            $input['type'] = 'radio';

            $id = Option::create($input)->id;

            //add options value in option table
            $count = count($request->option_value_name_en);
            for ($i = 0; $i < $count; $i++) {
                $option_value_array['option_id'] = $id;
                $option_value_array['name_en'] = $request->option_value_name_en[$i];
                $option_value_array['name_ar'] = $request->option_value_name_ar[$i];
                $option_value_array['sort_order'] = $request->option_value_sort_order[$i];

                OptionValue::create($option_value_array);
            }


            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Option - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/options');
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
    public function edit(Request $request, $id) {
        $id = $request->id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('options-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Option = Option::find($id);

        $optionValue = OptionValue::where('option_id', $id)->get();
        
        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module4.options.edit')
                        ->with('Option', $Option)
                        ->with('optionValue', $optionValue);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $id = $request->id;
        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('options-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Option = Option::findOrFail($id);

        // validate    
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'option_value_name_en.*' => 'required',
                    'option_value_name_ar.*' => 'required',
        ]);


        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/options/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name_en', 'name_ar', 'sort_order']);
            $input['vendor_id'] = VendorDetail::getID();
            $input['type'] = 'radio';
            $Option->fill($input)->save();

            //add options value in option table
            $count = count($request->option_value_name_en);
            for ($i = 0; $i < $count; $i++) {
                $option_value_array['option_id'] = $id;
                $option_value_array['name_en'] = $request->option_value_name_en[$i];
                $option_value_array['name_ar'] = $request->option_value_name_ar[$i];
                $option_value_array['sort_order'] = $request->option_value_sort_order[$i];

                if (!$request->option_value_id[$i] || $request->option_value_id[$i] == '') {
                    OptionValue::create($option_value_array);
                } else {
                    OptionValue::updateOrCreate(['id' => $request->option_value_id[$i]], $option_value_array);
                }
            }


            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Option - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/options');
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
        $this->DeleteAccess = Permit::AccessPermission('options-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Option = Option::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Option->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Option - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Option::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/options');
    }

    public function destroyOptionValue(Request $request) {
        $id = $request->id;
         if (!ProductOptionValue::where('option_value_id', $id)->exists()) {
           OptionValue::destroy($id);
         }
    }

}
