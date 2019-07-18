<?php

namespace App\Jobs;

use App\EmailQueue;
use App\Mail\LeadMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class LeadEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email_queue_id;

    /**
     * Create a new job instance.
     *
     * @param EmailQueue $email_queue
     */
    public function __construct($email_queue_id)
    {
        $this->email_queue_id = $email_queue_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email_queue = EmailQueue::find($this->email_queue_id);
        if (! $email_queue){
            return;
        }
        $partner = $email_queue->partner();
        $lead = $email_queue->lead();
        if (! $partner || ! $lead){
            return;
        }
        $mailable = new LeadMailable($partner->id, $lead->id);
        foreach(explode(';', $partner->email) as $email){
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send($mailable);
            }
        }
        $email_queue->status = 1;
        $email_queue->save();
    }
    public function getEmailQueueId(){
        return $this->email_queue_id;
    }
}
