<?php

namespace App\Http\Controllers\Vendor\Module4;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Vendor\Category;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;

class CategoryController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:categories');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress') . '/' . config('global.M4');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
       
        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('categories-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('categories-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('categories-edit');

        $Category = Category::
                leftjoin('categories As c', 'c.id', '=', 'categories.parent_id')
                ->select('categories.id', 'categories.name_en', DB::raw('(CASE WHEN c.name_en != "" THEN c.name_en ELSE "Main Category" END) AS parent_category'), 'categories.status', 'categories.created_at')
                //->orderBy('created_at','ASC')
                ->get();



        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Category)
                            ->editColumn('created_at', function ($Category) {
                                $newYear = new Carbon($Category->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($Category) {
                                return $Category->status == 1 ? '<div class="label label-success status" sid="' . $Category->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Category->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Category) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Category->id . '">';
                            })
                            ->editColumn('action', function ($Category) {
                                if ($this->EditAccess)
                                    return '<a href="' . url($this->configName . '/categories') . '/' . $Category->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a> ';
                            })
                            ->make();
        }

        return view('fitflowVendor.module4.categories.index')
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
        $this->CreateAccess = Permit::AccessPermission('categories-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        $subcate = new Category;
        try {
            $allSubCategories = $subcate->getCategories();
        } catch (Exception $e) {
            //no parent category found
        }


        return view('fitflowVendor.module4.categories.create', compact('allSubCategories'));
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
                    'sort_order' => 'required|numeric',
                    'parent_id' => 'required',
        ]);

        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/categories/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();
            $input['vendor_id'] = VendorDetail::getID();


            Category::create($input);

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Category - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/categories');
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
        $this->EditAccess = Permit::AccessPermission('categories-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Category = Category::find($id);

        $subcate = new Category;
        try {
            $allSubCategories = $subcate->getCategories();
        } catch (Exception $e) {
            //no parent category found
        }

        // show the edit form and pass the nerd
        return View::make('fitflowVendor.module4.categories.edit', compact('allSubCategories'))
                        ->with('Category', $Category);
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
        $this->EditAccess = Permit::AccessPermission('categories-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Category = Category::findOrFail($id);
            $Category->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Category = Category::findOrFail($id);

        // validate    
        $validator = Validator::make($request->only(['name_en', 'name_ar', 'parent_id']), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'parent_id' => 'required',
        ]);


        // validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/categories/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name_en', 'name_ar', 'parent_id']);
            $input['vendor_id'] = VendorDetail::getID();
            $Category->fill($input)->save();

            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Category - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/categories');
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
        $this->DeleteAccess = Permit::AccessPermission('categories-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Category = Category::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Category->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] Category - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Category::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/categories');
    }

}
