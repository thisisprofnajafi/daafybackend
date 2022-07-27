<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactSupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details , $user)
    {
        $this->details = $details;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('contactSupport')->with(['details'=>$this->details , 'user'=>ucfirst($this->user)]);
    }
}
