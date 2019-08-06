<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\Amenity;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class AmenityController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:master');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('master-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('master-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('master-edit');

        $Amenity = Amenity::
                select('id', 'name_en', 'icon', 'status', 'created_at')
                //->orderBy('created_at','ASC')
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Amenity)
                            ->editColumn('created_at', function ($Amenity) {
                                $newYear = new Carbon($Amenity->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($Amenity) {
                                return $Amenity->status == 1 ? '<div class="label label-success status" sid="' . $Amenity->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Amenity->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($Amenity) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Amenity->id . '">';
                            })
                            ->editColumn('action', function ($Amenity) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/amenities') . '/' . $Amenity->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->editColumn('icon', function ($Amenity) {
                                return $Amenity->icon != '' ? '<img src="' . url('public/amenities_icons/' . $Amenity->icon) . '" width="50" />' : '';
                            })
                            ->make();
        }

        return view('admin.masters.amenities.index')
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
        $this->CreateAccess = Permit::AccessPermission('master-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        return view('admin.masters.amenities.create');
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
                    'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:100'
        ]);



        // validation failed
        if ($validator->fails()) {

            return redirect('admin/amenities/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name_en', 'name_ar', 'status']);

            //Icon Image 
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                $filename = time() . '.' . $icon->getClientOriginalExtension();
                $destinationPath = public_path('amenities_icons/');
                $icon->move($destinationPath, $filename);
                $input['icon'] = $filename;
            }

            Amenity::create($input);

            //logAmenity
            LogActivity::addToLog('Amenity - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/amenities');
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
        $this->EditAccess = Permit::AccessPermission('master-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Amenity = Amenity::find($id);

        // show the edit form and pass the nerd
        return View::make('admin.masters.amenities.edit')
                        ->with('Amenity', $Amenity);
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
        $this->EditAccess = Permit::AccessPermission('master-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Amenity = Amenity::findOrFail($id);
            $Amenity->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Amenity = Amenity::findOrFail($id);
        // validate
        $validator = Validator::make($request->only(['name_en', 'name_ar']), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
        ]);

        // Image Validate
        //If Uploaded Image removed
        if ($request->uploaded_image_removed != 0) {
            $validator = Validator::make($request->only(['icon']), [
                        'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:100'
            ]);
        }

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/amenities/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->only(['name_en', 'name_ar']);

            //If Uploaded Image removed           
            if ($request->uploaded_image_removed != 0 && !$request->hasFile('icon')) {
                //Remove previous images
                $destinationPath = public_path('amenities_icons/');
                if (file_exists($destinationPath . $Amenity->icon) && $Amenity->icon != '') {
                    unlink($destinationPath . $Amenity->icon);
                }
                $input['icon'] = '';
            } else {
                //Icon Image 
                if ($request->hasFile('icon')) {
                    $icon = $request->file('icon');
                    $filename = time() . '.' . $icon->getClientOriginalExtension();
                    $destinationPath = public_path('amenities_icons/');
                    $icon->move($destinationPath, $filename);
                    //Remove previous images
                    if (file_exists($destinationPath . $Amenity->icon) && $Amenity->icon != '') {
                        unlink($destinationPath . $Amenity->icon);
                    }
                    $input['icon'] = $filename;
                }
            }

            $Amenity->fill($input)->save();

            //logAmenity
            LogActivity::addToLog('Amenity - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/amenities');
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
        $this->DeleteAccess = Permit::AccessPermission('master-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logAmenity
        //fetch title
        $Amenity = Amenity::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Amenity->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('Amenity - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //Delete Icon image 
            $Amenity = Amenity::
                    select('icon')->where('id', $id)->first();

            $destinationPath = public_path('amenities_icons/');

            if (!empty($Amenity)) {
                if (file_exists($destinationPath . $Amenity->icon) && $Amenity->icon != '') {
                    @unlink($destinationPath . $Amenity->icon);
                }
            }
            Amenity::destroy($id);
        }


        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/amenities');
    }

}
