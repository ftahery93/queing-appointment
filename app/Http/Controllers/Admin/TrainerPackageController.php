<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Redirect;
use Session;
use DB;
use View;
use Input;
use Carbon\Carbon;
use App\Models\Trainer\TrainerPackage;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Permit;
use App\Helpers\LogActivity;

class TrainerPackageController extends Controller {

   
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:trainerPackages');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
      
        //Ajax request
        if (request()->ajax()) {
          
            if($request->has('id') && $request->get('id')!=0){ 
                 $ID=$request->get('id');                
               $TrainerPackage = TrainerPackage::
                join('trainers', 'trainers.id', '=', 'trainer_packages.trainer_id')
                ->select('trainers.name', 'trainer_packages.name_en', 'trainer_packages.num_points', 'trainer_packages.num_days', 'trainer_packages.price', 'trainer_packages.created_at')
                ->where('trainers.status', 1)
                ->where('trainers.id', $ID)
                ->whereNull('trainers.deleted_at')
                ->get(); 
                
            }
            else{                
                $TrainerPackage = TrainerPackage::
                join('trainers', 'trainers.id', '=', 'trainer_packages.trainer_id')
                ->select('trainers.name', 'trainer_packages.name_en', 'trainer_packages.num_points', 'trainer_packages.num_days', 'trainer_packages.price', 'trainer_packages.created_at')
                ->where('trainers.status', 1)
                ->whereNull('trainers.deleted_at')
                ->get(); 
            }
            
             
            return Datatables::of($TrainerPackage)
                            ->editColumn('created_at', function ($TrainerPackage) {
                                $newYear = new Carbon($TrainerPackage->created_at);
                                return $newYear->format('d/m/Y');
                            })
                            ->editColumn('num_points', function ($TrainerPackage) {
                                return $TrainerPackage->num_points == 0 ? 'Unlimited' : $TrainerPackage->num_points;
                            })->make();
                           
                            // return $datatable
        }

        $Trainers = DB::table('trainers')
                ->select('name', 'id')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        return view('admin.trainerPackages.index')
                        ->with('Trainers', $Trainers);
    }
}
