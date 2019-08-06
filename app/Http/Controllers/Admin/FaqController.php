<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Admin\Faq;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class FaqController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:faq');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('faq-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('faq-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('faq-edit');


        $Faq = Faq::
                select('id', 'question_en', 'answer_en', 'status', 'created_at')
                ->get();

        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Faq)
                            ->editColumn('created_at', function ($Faq) {
                                $newYear = new Carbon($Faq->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($Faq) {
                                return $Faq->status == 1 ? '<div class="label label-success status" sid="' . $Faq->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $Faq->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('question_en', function ($Faq) {
                                return Str::limit($Faq->question_en, 100);
                            })
                            ->editColumn('answer_en', function ($Faq) {
                                return Str::limit($Faq->answer_en, 100);
                            })
                            ->editColumn('id', function ($Faq) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $Faq->id . '">';
                            })
                            ->editColumn('action', function ($Faq) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/faq') . '/' . $Faq->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->make();
        }

        return view('admin.faq.index')
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
        $this->CreateAccess = Permit::AccessPermission('faq-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        return view('admin.faq.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate
        $validator = Validator::make($request->only(['question_en', 'question_ar', 'answer_en', 'answer_ar']), [
                    'question_en' => 'required',
                    'question_ar' => 'required',
                    'answer_en' => 'required',
                    'answer_ar' => 'required'
        ]);


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/faq/create')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

            Faq::create($input);

            //logActivity
            LogActivity::addToLog('Faq - ' . Str::limit($request->question_en, 10), 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/faq');
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
        $this->EditAccess = Permit::AccessPermission('faq-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $Faq = Faq::find($id);

        // show the edit form and pass the nerd
        return View::make('admin.faq.edit')
                        ->with('Faq', $Faq);
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
        $this->EditAccess = Permit::AccessPermission('faq-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $Faq = Faq::findOrFail($id);
            $Faq->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $Faq = Faq::findOrFail($id);
        // validate
        $validator = Validator::make($request->only(['question_en', 'question_ar', 'answer_en', 'answer_ar']), [
                    'question_en' => 'required',
                    'question_ar' => 'required',
                    'answer_en' => 'required',
                    'answer_ar' => 'required'
        ]);


        // validation failed
        if ($validator->fails()) {
            return redirect('admin/faq/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            $Faq->fill($input)->save();

            //logActivity
            LogActivity::addToLog('Faq - ' . Str::limit($Faq->question_en, 10), 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/faq');
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
        $this->DeleteAccess = Permit::AccessPermission('faq-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logActivity
        //fetch title
        $Faq = Faq::
                select(DB::raw('substr(question_en, 1, 10) as question_en'))
                ->whereIn('id', $all_data['ids'])
                ->get();
       
    
        $name = $Faq->pluck('question_en');
     
        $groupname = $name->toJson();

        LogActivity::addToLog('Faq - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            Faq::destroy($id);
        }

        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/faq');
    }

}
