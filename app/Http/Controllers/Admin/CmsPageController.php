<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\CmsPage;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class CmsPageController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:cmsPages');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('cmsPages-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('cmsPages-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('cmsPages-edit');


        $CmsPage = CmsPage::
                select('id', 'name_en', 'status', 'created_at')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($CmsPage)
                            ->editColumn('created_at', function ($CmsPage) {
                                $newYear = new Carbon($CmsPage->created_at);
                                return $newYear->format('d/m/Y');
                            })                            
                            ->editColumn('status', function ($CmsPage) {
                                return $CmsPage->status == 1 ? '<div class="label label-success status" sid="' . $CmsPage->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $CmsPage->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($CmsPage) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $CmsPage->id . '">';
                            })
                            ->editColumn('action', function ($CmsPage) {
                                if ($this->EditAccess)
                                    return '<a href="'.url('admin/cmsPages') .'/' . $CmsPage->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('admin.cmsPages.index')
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
        $this->CreateAccess = Permit::AccessPermission('cmsPages-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        return view('admin.cmsPages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate
        $validator = Validator::make($request->only(['name_en', 'name_ar', 'description_en', 'description_ar']), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required'
        ]);


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/cmsPages/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

            CmsPage::create($input);

            //logActivity
            LogActivity::addToLog('CmsPage - ' . $request->name_en, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/cmsPages');
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
        $this->EditAccess = Permit::AccessPermission('cmsPages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $CmsPage = CmsPage::find($id);

        // show the edit form and pass the nerd
        return View::make('admin.cmsPages.edit')
                        ->with('CmsPage', $CmsPage);
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
        $this->EditAccess = Permit::AccessPermission('cmsPages-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $CmsPage = CmsPage::findOrFail($id);
            $CmsPage->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $CmsPage = CmsPage::findOrFail($id);
        // validate
        $validator = Validator::make($request->only(['name_en', 'name_ar', 'description_en', 'description_ar']), [
                    'name_en' => 'required',
                    'name_ar' => 'required',
                    'description_en' => 'required',
                    'description_ar' => 'required'
        ]);


        // validation failed
        if ($validator->fails()) {
            return redirect('admin/cmsPages/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            $CmsPage->fill($input)->save();

            //logActivity
            LogActivity::addToLog('CmsPage - ' . $request->name_en, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/cmsPages');
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
        $this->DeleteAccess = Permit::AccessPermission('cmsPages-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $CmsPage = CmsPage::
                select('name_en')
                ->whereIn('id', $all_data['ids'])
                ->get();

        $name = $CmsPage->pluck('name_en');
        $groupname = $name->toJson();

        LogActivity::addToLog('CmsPage - ' . $groupname, 'deleted');
   
        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            CmsPage::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/cmsPages');
    }

}
