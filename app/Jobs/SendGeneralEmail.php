<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Mail\Mailer;

class SendGeneralEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 10;
    protected $data;
    public function __construct($data = null)
    {
        //
        $this->data=json_decode(json_encode($data));
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        //
        $mailer->send('email.general', ['data'=>$this->data], function ($message) {

            $message->from('noreply@biosg2.com', 'noreply');
            $message->subject($this->data->subject);
            $message->to($this->data->email);

        });
    }
}
