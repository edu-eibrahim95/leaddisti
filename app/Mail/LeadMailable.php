<?php

namespace App\Mail;

use App\Lead;
use App\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeadMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Partner $partner
     * @param Lead $lead
     */
    public function __construct(Partner $partner, Lead $lead)
    {
        $this->partner = $partner;
        $this->lead = $lead;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("sasleads@softwareadvisoryservice.com", "Software Advisory Service")
            ->subject("SAS New Sales Opportunity: ".$this->lead->refernce)
            ->view('mail.lead')->with(['lead'=>$this->lead, 'partner'=>$this->partner]);
    }
}
