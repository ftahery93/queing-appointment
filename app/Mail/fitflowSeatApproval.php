<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use Illuminate\Support\Facades\URL;
use App\Models\Vendor\Classes;
use App\Helpers\VendorDetail;

class fitflowSeatApproval extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * The Eamil instance.
     *
     * @var Order
     */
    public $Classes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Classes $Classes) {
        $this->Classes = $Classes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
   
        //Get Email Template
        $emailTemplates = DB::table('email_templates')
                ->where(array('status' => 1, 'id' => 4))
                ->first();

        //Get Email Template
        $classes=$this->Classes;
        
       if($classes->changeRequest==1){
        return $this->from(VendorDetail::getEmailID(), VendorDetail::getName())
                        ->subject($emailTemplates->subject)
                        ->view('emails.changeRequestApproval')
                        ->with('content', $emailTemplates->content)
                        ->with('Classes', $this->Classes);
       }else{
         return $this->from(VendorDetail::getEmailID(), VendorDetail::getName())
                        ->subject($emailTemplates->subject)
                        ->view('emails.seatsApproval')
                        ->with('content', $emailTemplates->content)
                        ->with('Classes', $this->Classes);  
       }
    }

}
