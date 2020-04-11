<?php

namespace App\Http\Controllers\Admin;

use Artisan;
use Log;
use Storage;
use File;
use Validator;
use Redirect;
use Session;
use DB;
use View;
use Carbon\Carbon;
use App\Models\Admin\Backup;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Html\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BackupController extends Controller {
    
//     public function fileInfo($filePath)
//    {
//        $file = array();
//        $file['basename'] = $filePath['basename'];
//        $file['extension'] = $filePath['extension'];
//        $file['size'] = filesize($filePath['dirname'] . '/' . $filePath['basename']);
//
//        return $file;
//    }
    public function fileBasename($filePath)
    {
       return $file['basename'] = $filePath['basename'];
    } 
     public function fileName($filePath)
    {
       return $file['filename'] = $filePath['filename'];
    } 
    public function fileSize($filePath)
    {
        return filesize($filePath['dirname'] . '/' . $filePath['basename']);
    }
   
    public function index() { 
        
        $Backups = Backup::
                select('id', 'file_name', 'file_size', 'created_at')
                ->orderBy('id', 'desc')
                ->get();
        
        //Ajax request
        if (request()->ajax()) {

            return Datatables::of($Backups)
                             ->editColumn('created_at', function ($Backups) {
                                $newYear = new Carbon($Backups->created_at);
                                return $newYear->format('d/m/Y H:i:s');
                            })
                            ->editColumn('action', function ($Backups) {
                                return '<a href="'.url('admin/backup/download') .'/' . $Backups->file_name.'" class="btn btn-success tooltip-primary btn-small" data-toggle="tooltip" data-placement="top" title="Download Backup" data-original-title="Download Backup"><i class="fa fa-cloud-download"></i> Download</a>'
                                        . ' <a data-id="' . $Backups->id.'" class="btn btn-danger tooltip-primary btn-small delete"  data-toggle="tooltip" data-placement="top" title="Delete Backup" data-original-title="Delete Backup"><i class="fa fa-trash"></i> Delete</a>';
                            })
                            ->make();
        }

        return view("admin.backup.backups");
    }

    public function create() {
        try {
            // start the backup process
            Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();
           
            // log the results
            Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n" . $output);
            //Insert into backuplist
            $disk = Storage::disk(config('laravel-backup.backup.destination.disks')[0]);
            $allFiles = Storage::disk(config('laravel-backup.backup.destination.disks')[0])->allFiles();
            foreach ($allFiles as $key => $file) { 
            $input['file_name'] = $this->fileBasename(pathinfo(storage_path() . '/laravel-backups/' . $file)); 
            $input['file_size'] = $this->fileSize(pathinfo(storage_path() . '/laravel-backups/' . $file));             
            Backup::firstOrCreate($input);
            }
            // return the results as a response to the ajax call
           Session::flash('message', config('global.Backup'));
            return redirect()->back();
        } catch (Exception $e) {
            Flash::error($e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Downloads a backup zip file.
     *
     * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
     */
   public function download($file_name) {      
   	$title = str_replace('/', '-', url('/'));
       $title = str_replace(':', '-', $title);

       $pathToFile=storage_path('laravel-backups/'.$title.'/'.$file_name);
         return response()->download($pathToFile);
        //$file = config('laravel-backup.backup.name') . '/' . $file_name;
        //$disk = Storage::disk(config('laravel-backup.backup.destination.disks')[0]);
//         $file=pathinfo(storage_path() . '/laravel-backups/' . $file_name);
//        $basename=$this->fileName(pathinfo(storage_path() . '/laravel-backups/' . $file_name));
//        
//        if ($disk->exists($file)) {
//            $fs = Storage::disk(config('laravel-backup.backup.destination.disks')[0])->getDriver();
//           
//            $stream = $fs->readStream($file);
//            return \Response::stream(function () use ($stream) {
//                        fpassthru($stream);
//                    }, 200, [
//                        "Content-Type" => $fs->getMimetype($file),
//                        "Content-Length" => $fs->getSize($file),
//                        "Content-disposition" => "attachment; filename=\"" .$basename . "\"",
//            ]);
//        } else {
//            abort(404, "The backup file doesn't exist.");
//        }
   }

    /**
     * Deletes a backup file.
     */
    public function delete($id) {
        //Ajax request
        if (request()->ajax()) {
          $Backup = Backup::findOrFail($id);
            $title = str_replace('/', '-', url('/'));
       $title = str_replace(':', '-', $title);
        $destinationPath = storage_path('laravel-backups/'.$title.'/');
        if (file_exists($destinationPath . $Backup->file_name)) {
                    @unlink($destinationPath . $Backup->file_name);
                }
                 Backup::destroy($id);
                 return response()->json(['response' => config('global.deleteBackup')]);
                 
        }
//        $disk = Storage::disk(config('laravel-backup.backup.destination.disks')[0]);
//        if ($disk->exists(config('laravel-backup.backup.name') . '/' . $file_name)) {
//            $disk->delete(config('laravel-backup.backup.name') . '/' . $file_name);
//            return redirect()->back();
//        } else {
//            abort(404, "The backup file doesn't exist.");
//        }
    }

}
