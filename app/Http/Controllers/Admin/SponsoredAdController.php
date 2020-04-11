<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use DateTime;
use App\Models\Admin\SponsoredAdvertisement;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class SponsoredAdController extends Controller {

    protected $ViewAccess;
    protected $EditAccess;
    protected $CreateAccess;
    protected $DeleteAccess;

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:sponsoredAds');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        //Check Create Access Permission
        $this->CreateAccess = Permit::AccessPermission('sponsoredAds-create');

        //Check Delete Access Permission
        $this->DeleteAccess = Permit::AccessPermission('sponsoredAds-delete');

        //Check Edit Access Permission
        $this->EditAccess = Permit::AccessPermission('sponsoredAds-edit');

        $sponsoredAds = SponsoredAdvertisement::
                select('id', 'image', 'status', 'created_at')
                ->get();
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($sponsoredAds)
                            ->editColumn('created_at', function ($sponsoredAds) {
                                $newYear = new Carbon($sponsoredAds->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('status', function ($sponsoredAds) {
                                return $sponsoredAds->status == 1 ? '<div class="label label-success status" sid="' . $sponsoredAds->id . '" value="0"><i class="entypo-check"></i></div>' : '<div class="label label-secondary status"  sid="' . $sponsoredAds->id . '" value="1"><i class="entypo-cancel"></i></div>';
                            })
                            ->editColumn('id', function ($sponsoredAds) {
                                return '<input tabindex="5" type="checkbox" class="icheck-14 check"   name="ids[]" value="' . $sponsoredAds->id . '">';
                            })
                            ->editColumn('action', function ($sponsoredAds) {
                                if ($this->EditAccess)
                                    return '<a href="' . url('admin/sponsoredAds') . '/' . $sponsoredAds->id . '/edit" class="btn btn-info tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Edit Records" data-original-title="Edit Records"><i class="entypo-pencil"></i></a>';
                            })
                            ->editColumn('image', function ($sponsoredAds) {
                                return $sponsoredAds->image != '' ? '<img src="' . url('public/sponsoredAd_images/' . $sponsoredAds->image) . '" width="50" />' : '';
                            })
                            ->make();
        }

        return view('admin.sponsoredAds.index')
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
        $this->CreateAccess = Permit::AccessPermission('sponsoredAds-create');
        if (!$this->CreateAccess)
            return redirect('errors/401');

        return view('admin.sponsoredAds.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // validate
        $validator = Validator::make($request->only(['start_date', 'end_date', 'image']), [
                    'start_date' => 'required|date_format:d/m/Y',
                    'end_date' => 'required|date_format:d/m/Y|after:start_date',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:800'
        ]);


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/sponsoredAds/create')
                            ->withErrors($validator)->withInput();
        } else {

            $input = $request->all();

            // Image 
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('sponsoredAd_images/');
                $image->move($destinationPath, $filename);
                $input['image'] = $filename;
            }

            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
            $input['start_date'] = $newDate->format('Y-m-d');

            $eDate = $datetime->createFromFormat('d/m/Y', $request->end_date);
            $input['end_date'] = $eDate->format('Y-m-d');

            SponsoredAdvertisement::create($input);

            //logSponsoredAdvertisement
            $period = $request->start_date . '-' . $request->end_date;
            LogActivity::addToLog('SponsoredAdvertisement - ' . $period, 'created');

            Session::flash('message', config('global.addedRecords'));

            return redirect('admin/sponsoredAds');
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
        $this->EditAccess = Permit::AccessPermission('sponsoredAds-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        $sponsoredAds = SponsoredAdvertisement::find($id);

        //Change Date Format
        $newdate = new Carbon($sponsoredAds->start_date);
        $sponsoredAds->start_date = $newdate->format('d/m/Y');

        $enddate = new Carbon($sponsoredAds->end_date);
        $sponsoredAds->end_date = $enddate->format('d/m/Y');

        // show the edit form and pass the nerd
        return View::make('admin.sponsoredAds.edit')
                        ->with('sponsoredAds', $sponsoredAds);
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
        $this->EditAccess = Permit::AccessPermission('sponsoredAds-edit');
        if (!$this->EditAccess)
            return redirect('errors/401');

        //Ajax request
        if (request()->ajax()) {
            $sponsoredAds = SponsoredAdvertisement::findOrFail($id);
            $sponsoredAds->update(['status' => $request->status]);
            return response()->json(['response' => config('global.statusUpdated')]);
        }
        $sponsoredAds = SponsoredAdvertisement::findOrFail($id);
        // validate
        $validator = Validator::make($request->only(['start_date', 'end_date']), [
                    'start_date' => 'required|date_format:d/m/Y',
                    'end_date' => 'required|date_format:d/m/Y|after:start_date',
        ]);

        // Image Validate
        //If Uploaded Image removed
        if ($request->uploaded_image_removed != 0) {
            $validator = Validator::make($request->only(['image']), [
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:800'
            ]);
        }


        // validation failed
        if ($validator->fails()) {

            return redirect('admin/sponsoredAds/' . $id . '/edit')
                            ->withErrors($validator)->withInput();
        } else {
            $input = $request->all();

            //If Uploaded Image removed           
            if ($request->uploaded_image_removed != 0 && !$request->hasFile('image')) {
                //Remove previous images
                $destinationPath = public_path('sponsoredAd_images/');
                if (file_exists($destinationPath . $sponsoredAds->image) && $sponsoredAds->image != '') {
                    unlink($destinationPath . $sponsoredAds->image);
                }
                $input['image'] = '';
            } else {
                //Icon Image 
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('sponsoredAd_images/');
                    $image->move($destinationPath, $filename);
                    //Remove previous images
                    if (file_exists($destinationPath . $sponsoredAds->image) && $sponsoredAds->image != '') {
                        unlink($destinationPath . $sponsoredAds->image);
                    }
                    $input['image'] = $filename;
                }
            }


            //Change Date Format
            $datetime = new DateTime();
            $newDate = $datetime->createFromFormat('d/m/Y', $request->start_date);
            $input['start_date'] = $newDate->format('Y-m-d');

            $eDate = $datetime->createFromFormat('d/m/Y', $request->end_date);
            $input['end_date'] = $eDate->format('Y-m-d');

            $sponsoredAds->fill($input)->save();

            $period = $request->start_date . '-' . $request->end_date;

            //logSponsoredAdvertisement
            LogActivity::addToLog('SponsoredAdvertisement - ' . $period, 'updated');

            Session::flash('message', config('global.updatedRecords'));

            return redirect('admin/sponsoredAds');
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
        $this->DeleteAccess = Permit::AccessPermission('sponsoredAds-delete');
        if (!$this->DeleteAccess)
            return redirect('errors/401');

        $all_data = $request->except('_token', 'table-4_length');

        //logSponsoredAdvertisement
        //fetch title
        $sponsoredAds = SponsoredAdvertisement::
                select(DB::raw('CONCAT(DATE_FORMAT(start_date,"%d/%m/%Y"), "- ", DATE_FORMAT(end_date,"%d/%m/%Y")) AS period'))
                ->whereIn('id', $all_data['ids'])
                ->get();

        $period = $sponsoredAds->pluck('period');
        $groupname = $period->toJson();

        LogActivity::addToLog('SponsoredAdvertisement - ' . $groupname, 'deleted');

        $all_data = array_get($all_data, 'ids');
        foreach ($all_data as $id) {
            //Delete Icon image 
            $sponsoredAds = SponsoredAdvertisement::
                    select('image')->where('id', $id)->first();

            $destinationPath = public_path('sponsoredAd_images/');

            if (!empty($sponsoredAds)) {
                if (file_exists($destinationPath . $sponsoredAds->image) && $sponsoredAds->image != '') {
                    @unlink($destinationPath . $sponsoredAds->image);
                }
            }
            SponsoredAdvertisement::destroy($id);
        }


        // redirect
        Session::flash('message', config('global.deletedRecords'));

        return redirect('admin/sponsoredAds');
    }

}
