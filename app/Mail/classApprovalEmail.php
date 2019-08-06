<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use Illuminate\Support\Facades\URL;
use App\Models\Admin\Vendor;
use App\Helpers\VendorDetail;

class classApprovalEmail extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * The Eamil instance.
     *
     * @var Order
     */
    public $vendorClasses;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Vendor $vendorClasses) {
        $this->vendorClasses = $vendorClasses;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        $vendorClasses = $this->vendorClasses;

        //Get classes
        $arrayJson = json_decode($vendorClasses->jsonID);

        $Classes = DB::table('classes')
                        ->join('class_master', 'classes.class_master_id', '=', 'class_master.id')
                        ->join('vendor_branches', 'classes.branch_id', '=', 'vendor_branches.id')
                        ->select(DB::raw('CONCAT(class_master.name_en, " ","-", vendor_branches.name_en) AS name_en'))
                        ->whereIn('classes.id', $arrayJson)->get();


        //Get Email Template
        $emailTemplates = DB::table('email_templates')
                ->where(array('status' => 1, 'id' => 4))
                ->first();

        return $this->from(config('mail.from.address'))
                        ->subject($emailTemplates->subject)
                        ->view('emails.classApproval')
                        ->with('content', $emailTemplates->content)
                        ->with('Classes', $Classes);
    }

}
