<?php

namespace App\Http\Controllers;

use App\EmailQueue;
use App\Job;
use App\Mail\LeadMailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class QueueController extends Controller
{
    public function processQueue(Request $request){
        $jobs = Job::where(['queue'=>'leads'])->get();
        foreach ($jobs as $job) {
            $payload = json_decode($job->payload);
            $email_queue_id = unserialize($payload->data->command)->getEmailQueueId();
            $email_queue = EmailQueue::find($email_queue_id);
            if (! $email_queue){
                Job::find($job->id)->delete();
                continue;
            }
            $partner = $email_queue->partner();
            $lead = $email_queue->lead();
            if (! $partner || ! $lead){
                continue;
            }
            foreach(explode(';', $partner->email) as $email){
                Mail::to($email)->send(new LeadMailable($partner->id, $lead->id));
            }
            $email_queue->status = 1;
            $email_queue->save();
            Job::find($job->id)->delete();
        }

        Session::flash('queu_worked', 'queue started successfully');
        return redirect(route('voyager.email-queues.index'));
    }
}
