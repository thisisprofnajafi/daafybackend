<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name;	public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token,$name)
    {
        $this->token = $token;
        $this->name = $name;	$this->url = env('FRONT_URL').'/account/verify?token='.$this->token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


        return $this->view('verification')->with(['url'=>$this->url , 'name'=>$this->name]);
    }
}
