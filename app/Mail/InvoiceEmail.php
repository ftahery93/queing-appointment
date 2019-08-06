<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use Illuminate\Support\Facades\URL;
use App\Models\Vendor\Member;

class InvoiceEmail extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * The Eamil instance.
     *
     * @var Order
     */
    public $Invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Member $Invoice) {
        $this->Invoice = $Invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
   
        //Get Email Template
        $emailTemplates = DB::table('email_templates')
                ->where(array('status' => 1, 'id' => 3))
                ->first();

        //Get Email Template
        return $this->from(config('mail.from.address'), config('app.name'))
                        ->subject($emailTemplates->subject)
                        ->view('emails.invoice')
                        ->with('content', $emailTemplates->content)
                        ->with('Invoice', $this->Invoice);
    }

}
