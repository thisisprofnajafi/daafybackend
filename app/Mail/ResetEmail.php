<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $code;
    public $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code,$name)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('reset')->with(['code'=>$this->code,'name'=>$this->name]);
    }
}
