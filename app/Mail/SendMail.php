<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $description;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $description
     */
    public function __construct($subject, $description)
    {
        $this->subject = $subject;
        $this->description = $description;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->markdown('emails.sendmail')
            ->with('description', $this->description);
    }
}
