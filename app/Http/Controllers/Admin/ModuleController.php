<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\Module;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class ModuleController extends Controller {

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

        $Module = Module::
                select('id', 'name_en', 'created_at')
                //->orderBy('created_at','ASC')
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Module)
                            ->editColumn('created_at', function ($Module) {
                                $newYear = new Carbon($Module->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('id', function ($Module) {
                                if ($this->EditAccess)
                                    return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Module->id . '">';
                            })
                            ->editColumn('action', function ($Module) {
                                return '<a href="' . url('admin/modules') . '/' . $Module->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('admin.masters.modules.index')
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

        return view('admin.masters.modules.create');
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
                    'description_en' => 'required',
                    'description_ar' => 'required',
                    'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:100'
        ]);

        // validation failed
        if ($validator->fails()) {

            return redirect('admin/modules/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name_en', 'name_ar', 'description_en', 'description_ar']);
            //Icon Image 
            if ($request->hasFile('icon')) {
                $icon = $request->file('icon');
                $filename = time() . '.' . $icon->getClientOriginalExtension();
                $destinationPath = public_path('modules_icons/');
                $icon->move($destinationPath, $filename);
                $input['icon'] = $filename;
            }
            $input['slug'] = str_slug($request->name_en, '-');
            Module::create($input);

            //logActivity
            LogActivity::addToLog('Module - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/modules');
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

        $Module = Module::find($id);

        // show the edit form and pass the nerd
        return View::make('admin.masters.modules.edit')
                        ->with('Module', $Module);
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
            $Module = Module::findOrFail($id);
            $Module->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Module = Module::findOrFail($id);
        // validate
        $validator = Validator::make($request->all(), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required',
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

            return redirect('admin/modules/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->only(['name_en', 'name_ar', 'description_en', 'description_ar']);

            //If Uploaded Image removed           
            if ($request->uploaded_image_removed != 0 && !$request->hasFile('icon')) {
                //Remove previous images
                $destinationPath = public_path('modules_icons/');
                if (file_exists($destinationPath . $Module->icon) && $Module->icon != '') {
                    unlink($destinationPath . $Module->icon);
                }
                $input['icon'] = '';
            } else {
                //Icon Image 
                if ($request->hasFile('icon')) {
                    $icon = $request->file('icon');
                    $filename = time() . '.' . $icon->getClientOriginalExtension();
                    $destinationPath = public_path('modules_icons/');
                    $icon->move($destinationPath, $filename);
                    //Remove previous images
                    if (file_exists($destinationPath . $Module->icon) && $Module->icon != '') {
                        unlink($destinationPath . $Module->icon);
                    }
                    $input['icon'] = $filename;
                }
            }


            $input['slug'] = str_slug($request->name_en, '-');

            $Module->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Module - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/modules');
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

        //logActivity
        //fetch title
        $Module = Module::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $Module->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('Module - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Module::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/modules');
    }

}
