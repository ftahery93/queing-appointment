<?php

namespace App\Http\Controllers\Vendor;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\VendorBranch;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;
use App\Helpers\VendorDetail;
use App\Models\Admin\VendorImage;
use App\Helpers\Common;

class VendorBranchController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;
    protected $guard = 'vendor';
    protected $configName;

    public function __construct() {
        $this->middleware($this->guard);
        $this->middleware('vendorPermission:vendorBranches');
        $this->configName = config('global.fitflowVendor') . config('global.storeAddress');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

//Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('vendorBranches-create');

//Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendorBranches-delete');

//Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('vendorBranches-edit');


        $VendorBranch = VendorBranch::
                join('vendors', 'vendors.id', '=', 'vendor_branches.vendor_id')
                ->join('areas', 'areas.id', '=', 'vendor_branches.area')
                ->select('vendor_branches.id', DB::raw('(CASE WHEN vendor_branches.is_main_branch = 1 THEN CONCAT(vendor_branches.name_en, " ", "(Main Branch)")  ELSE vendor_branches.name_en END) AS name_en'), 'vendor_branches.contact', 'areas.name_en AS area', 'vendor_branches.status', 'vendor_branches.created_at')
                ->where('vendors.id', VendorDetail::getID())
                ->whereNull('vendors.deleted_at')
                ->get();


//Ajax request
        if (request()->ajax()) {

            return Datatables::of($VendorBranch)
                            ->editColumn('created_at', function ($VendorBranch) {
                                $newYear = new Carbon($VendorBranch->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($VendorBranch) {
                                return $VendorBranch->status == 1 ? '<div class="label label-success status" sid="' . $VendorBranch->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $VendorBranch->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($VendorBranch) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $VendorBranch->id . '">';
                            })
                            ->editColumn('action', function ($VendorBranch) {
                                if ($this->EditAccess)
                                    return '<a href="' . url($this->configName . '/vendorBranches') . '/' . $VendorBranch->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>'
                                            . ' <a href="' . url($this->configName . '/vendorBranches') . '/' . $VendorBranch->id . '/uploadImages" class="btn btn-red tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Upload Images" data-original-title="Upload Images"><i class="entypo-picture"></i></a>';
                            })
                            ->make();
        }

        return view('fitflowVendor.vendorBranches.index')
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
        $this->CreateAccess = Permit::AccessPermission('vendorBranches-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');


//Get all Gender Types
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

//Get all Areas
        $areas = DB::table('areas')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all amenities
        $amenities = DB::table('amenities')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();


        return view('fitflowVendor.vendorBranches.create')
                        ->with('gender_types', $gender_types)
                        ->with('areas', $areas)
                        ->with('amenities', $amenities)
                        ->with('vendor_id', VendorDetail::getID());
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
                    'vendor_id' => 'required',
                    'gender_type' => 'required',
                    'area' => 'required',
                    'contact' => 'required|numeric',
                    'shifting_hours' => 'sometimes|required|array|min:1',
                    'latitude' => 'required',
                    'longitude' => 'required',
        ]);


// validation failed
        if ($validator->fails()) {

            return redirect($this->configName . '/vendorBranches/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name_en', 'name_ar', 'vendor_id', 'status', 'area', 'amenities',
                'latitude', 'longitude', 'contact', 'address']);
            //Shifiting Hours
            $request->shifting_hours = array_filter($request->shifting_hours);
            if (!empty($request->shifting_hours)) {
                $collection = collect($request->shifting_hours);
                $input['shifting_hours'] = $collection->toJson();
            } else {
                $input['shifting_hours'] = '';
            }
            //Gender Type Hours
            $request->gender_type = array_filter($request->gender_type);
            if (!empty($request->gender_type)) {
                $collection = collect($request->gender_type);
                $input['gender_type'] = $collection->toJson();
            } else {
                $input['gender_type'] = '';
            }

            //Amenities Type Hours
            $request->amenities = array_filter($request->amenities);
            if (!empty($request->amenities)) {
                $collection = collect($request->amenities);
                $input['amenities'] = $collection->toJson();
            } else {
                $input['amenities'] = '';
            }

            //Check main branch condition
            if ($request->has('main_branch_id') && $request->main_branch_id == 1) {
                VendorBranch::where('vendor_id', VendorDetail::getID())->update(array('is_main_branch' => null));
                $input['is_main_branch'] = 1;
            }

            $id = VendorBranch::create($input)->id;

            //Vendor Name
            $vendorName = DB::table('vendors')->select('name', 'main_branch_id')->where('id', VendorDetail::getID())->first();


            //Check main branch condition
            if ($request->has('main_branch_id') && $request->main_branch_id == 1) {
                DB::table('vendors')->where('id', VendorDetail::getID())->update(array('main_branch_id' => $id));
            } else {
                if (is_null($vendorName->main_branch_id) || $vendorName->main_branch_id == $id) {
                    $branchID = VendorBranch::select('id')->where('vendor_id', VendorDetail::getID())->orderby('id', 'ASC')->first();
                    DB::table('vendors')->where('id', VendorDetail::getID())->update(array('main_branch_id' => $branchID->id));
                    VendorBranch::where('vendor_id', VendorDetail::getID())->update(array('is_main_branch' => null));
                    VendorBranch::where('id', $branchID->id)->update(array('is_main_branch' => 1));
                }
            }


            //logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorBranch - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect($this->configName . '/vendorBranches');
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
        $this->EditAccess = Permit::AccessPermission('vendorBranches-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

//Get all Gender Types
        $gender_types = DB::table('gender_types')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

//Get all Areas
        $areas = DB::table('areas')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        //Get all amenities
        $amenities = DB::table('amenities')
                ->select('id', 'name_en')
                ->where('status', 1)
                ->get();

        $VendorBranch = VendorBranch::find($id);
//Get contact json value
        $collection = collect(json_decode($VendorBranch->gender_type, true));
        $amenityCollection = collect(json_decode($VendorBranch->amenities, true));
        $shifting_hours = collect(json_decode($VendorBranch->shifting_hours, true));
        //Get Default Branch
        $branchID = VendorBranch::select('id')->where('vendor_id', $VendorBranch->vendor_id)->orderby('id', 'ASC')->first();

// show the edit form and pass the nerd
        return View::make('fitflowVendor.vendorBranches.edit')
                        ->with('VendorBranch', $VendorBranch)
                        ->with('vendor_id', VendorDetail::getID())
                        ->with('amenityCollection', $amenityCollection)
                        ->with('collection', $collection)
                        ->with('gender_types', $gender_types)
                        ->with('shifting_hours', $shifting_hours)
                        ->with('areas', $areas)
                        ->with('amenities', $amenities)
                        ->with('branchID', $branchID);
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
        $this->EditAccess = Permit::AccessPermission('vendorBranches-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

//Ajax request
        if (request()->ajax()) {
            $VendorBranch = VendorBranch::findOrFail($id);
            $VendorBranch->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $VendorBranch = VendorBranch::findOrFail($id);
// validate
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'vendor_id' => 'required',
                    'gender_type' => 'required',
                    'area' => 'required',
                    'contact' => 'required|numeric',
                    'shifting_hours' => 'sometimes|required|array|min:1',
                    'latitude' => 'required',
                    'longitude' => 'required',
        ]);


// validation failed
        if ($validator->fails()) {
            return redirect($this->configName . '/vendorBranches/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name_en', 'name_ar', 'vendor_id', 'status', 'area', 'amenities', 'latitude', 'longitude', 'contact', 'address']);
            //Shifiting Hours           
            $request->shifting_hours = array_filter($request->shifting_hours);
            if (!empty($request->shifting_hours)) {
                $collection = collect($request->shifting_hours);
                $input['shifting_hours'] = $collection->toJson();
            } else {
                $input['shifting_hours'] = '';
            }

            //Gender Type Hours
            $request->gender_type = array_filter($request->gender_type);
            if (!empty($request->gender_type)) {
                $collection = collect($request->gender_type);
                $input['gender_type'] = $collection->toJson();
            } else {
                $input['gender_type'] = '';
            }

            //Amenities Type Hours
            $request->amenities = array_filter($request->amenities);
            if (!empty($request->amenities)) {
                $collection = collect($request->amenities);
                $input['amenities'] = $collection->toJson();
            } else {
                $input['amenities'] = '';
            }

            //Check main branch condition
            if ($request->has('main_branch_id') && $request->main_branch_id == 1) {
                VendorBranch::where('vendor_id', VendorDetail::getID())->update(array('is_main_branch' => null));
                $input['is_main_branch'] = 1;
            }


            //Vendor Name
            $vendorName = DB::table('vendors')->select('name', 'main_branch_id')->where('id', VendorDetail::getID())->first();


            //Check main branch condition
            if ($request->has('main_branch_id') && $request->main_branch_id == 1) {
                DB::table('vendors')->where('id', VendorDetail::getID())->update(array('main_branch_id' => $id));
            } else {
                if (is_null($vendorName->main_branch_id) || $vendorName->main_branch_id == $id) {
                    $branchID = VendorBranch::select('id')->where('vendor_id', VendorDetail::getID())->orderby('id', 'ASC')->first();
                    DB::table('vendors')->where('id', VendorDetail::getID())->update(array('main_branch_id' => $branchID->id));
                    VendorBranch::where('vendor_id', VendorDetail::getID())->update(array('is_main_branch' => null));
                    VendorBranch::where('id', $branchID->id)->update(array('is_main_branch' => 1));
                }
            }

            $VendorBranch->fill($input)->save();

//logActivity
            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorBranch - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect($this->configName . '/vendorBranches');
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
        $this->DeleteAccess = Permit::AccessPermission('vendorBranches-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

//logActivity
//fetch title
        $VendorBranch = VendorBranch::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $VendorBranch->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorBranch - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            VendorBranch::destroy($id);
        }
// redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect($this->configName . '/vendorBranches');
    }
    
     //Multiple Images
    public function uploadImages(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('vendorBranches-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        $branchID = $request->branch_id;

        $VendorBranch = VendorBranch::
                select('vendor_branches.vendor_id', 'vendor_branches.name_en')
                ->where('vendor_branches.id', $branchID)
                ->first();

        //Get All Images
        $vendorImages = VendorImage::where('vendor_branch_id', $branchID)->get();


        return view('fitflowVendor.vendorBranches.uploadImages')
                        ->with('branchID', $branchID)
                        ->with('VendorBranch', $VendorBranch)
                        ->with('vendorImages', $vendorImages);
    }

    //Multiple Images
    public function images(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('vendorBranches-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        $branchID = $request->branch_id;

        if ($request->hasFile('file')) {
            $validator = Validator::make($request->only(['file']), [
                        'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:800'
            ]);
            // validation failed
            if ($validator->fails()) {
                return response()->json(array('error' => config('global.errorImage')));
            } else {

                $image = $request->file('file');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('vendors_images/');
                $image->move($destinationPath, $filename);

                //Create Thumbnails
                $thumbnail_image_path = public_path('vendors_images/thumbnails/' . $filename);
                $source_image_path = public_path('vendors_images/' . $filename);
                $reduceSize = false;
                $reduceSizePercentage = 1;
                $thumbnailMaxWidth = 100;
                $thumbnailMaxHeight = 100;
                $maintainAspectRatio = true;
                $bgColor = config('global.thumbnailColor');
                $quality = 100;
               Common::generateThumbnails($source_image_path, $thumbnail_image_path, $reduceSize, $reduceSizePercentage, $thumbnailMaxWidth, $thumbnailMaxHeight, $maintainAspectRatio, $bgColor);

                $input['image'] = $filename;
                $input['vendor_branch_id'] = $branchID;

                $id = VendorImage::create($input)->id;
               
                //fetch title
                $VendorBranch = VendorBranch::
                        join('vendors', 'vendors.id', '=', 'vendor_branches.vendor_id')
                        ->select('vendor_branches.vendor_id', 'vendor_branches.name_en', 'vendors.name AS vendor')
                        ->where('vendor_branches.id', $branchID)
                        ->first();

                $groupname = $VendorBranch->name_en;
                $vendorName = $VendorBranch->vendor;

                LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorBranch - ' . $groupname . ' Image', 'uploaded');
            }
        }

        return response()->json(array('id' => $id));
    }

    //Delete image
    public function deleteImage(Request $request) {
        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('vendorBranches-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $id = $request->id;

        //Ajax request
        if (request()->ajax()) {

            //Delete  image 
            $VendorImage = VendorImage::
                    select('image', 'vendor_branch_id')->where('id', $id)->first();
            // dd($VendorImage);

            $destinationPath = public_path('vendors_images/');
            $thumbnailPath = public_path('vendors_images/thumbnails/');
            if (!empty($VendorImage)) {
                if (file_exists($destinationPath . $VendorImage->image) && $VendorImage->image != '') {
                    @unlink($destinationPath . $VendorImage->image);
                     @unlink($thumbnailPath . $VendorImage->image);
                }
            }

            VendorImage::destroy($id);

            //fetch title
            $VendorBranch = VendorBranch::
                    join('vendors', 'vendors.id', '=', 'vendor_branches.vendor_id')
                    ->select('vendor_branches.vendor_id', 'vendor_branches.name_en', 'vendors.name AS vendor')
                    ->where('vendor_branches.id', $VendorImage->vendor_branch_id)
                    ->first();

            $groupname = $VendorBranch->name_en;
            $vendorName = $VendorBranch->vendor;

            LogActivity::addToLog('[Vendor ' . VendorDetail::getName() . '] VendorBranch - ' . $groupname . ' Image', 'deleted');

            $images = VendorImage::get();

            //$returnHTML = view('admin.vendors.images')->with('images', $images)->render();

            return response()->json(array('response' => config('global.deletedRecords'), 'id' => $id));
        }
    }

}