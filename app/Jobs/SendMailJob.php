<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $subject;
    protected $description;

    public function __construct($user, $subject, $description)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->description = $description;
    }

    public function handle()
    {
        Mail::to($this->user)->send(new SendMail($this->subject, $this->description));
    }
}
