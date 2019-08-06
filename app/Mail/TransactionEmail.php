<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\Models\Admin\Transaction;
use Illuminate\Support\Facades\URL;

class TransactionEmail extends Mailable {

    use Queueable,
        SerializesModels;

    /**
     * The Eamil instance.
     *
     * @var Order
     */
    public $Transaction;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Transaction $Transaction) {
        $this->Transaction = $Transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        //Get file extension
        $ext = pathinfo($this->Transaction->attachment, PATHINFO_EXTENSION);

        //Get Email Template
        $emailTemplates = DB::table('email_templates')
                ->where(array('status' => 1, 'id' => 2))
                ->first();

        //Get Email Template
        return $this->from(config('mail.from.address'), config('app.name'))
                        ->subject($emailTemplates->subject)
                        ->view('emails.transaction')
                        ->with('content', $emailTemplates->content)
                        ->attach(url('public/transactions_images').'/'.$this->Transaction->attachment);
    }

}
