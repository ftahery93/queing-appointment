<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use DB;

class WorkoutAssigned extends Notification {

    use Queueable;

    private $workout;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($workout) {
        $this->workout = $workout;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
     
        if ($this->workout->assign == 'Trainer') {  // Email to Trainer
            //Get Email Template
            $emailTemplates = DB::table('email_templates')
                    ->where(array('status' => 1, 'id' => 1))
                    ->first();

            return (new MailMessage)
                            ->subject($emailTemplates->subject)
                            ->line($emailTemplates->content)
                            ->action('Application Link', url('trainer'));
        } elseif ($this->workout->assign == 'Vendor') {  // Email to Vendor
            //Get Email Template
            $emailTemplates = DB::table('email_templates')
                    ->where(array('status' => 1, 'id' => 1))
                    ->first();

            return (new MailMessage)
                            ->subject($emailTemplates->subject)
                            ->line($emailTemplates->content)
                            ->action('Application Link', url('vendor'));
        }
        
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            'workout' => $this->workout->id
        ];
    }

}
