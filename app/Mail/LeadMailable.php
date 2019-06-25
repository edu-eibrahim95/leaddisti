<?php

namespace App\Mail;

use App\Lead;
use App\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

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
        if (! empty($this->lead->pdf_file) && count(json_decode($this->lead->pdf_file)) > 0){
            return $this->from("sasleads@softwareadvisoryservice.com", "Software Advisory Service")
                ->subject("SAS New Sales Opportunity: ".$this->lead->refernce)
                ->view('mail.lead')->attach(Storage::disk(config('voyager.storage.disk'))->url(json_decode($this->lead->pdf_file)[0]->download_link))
                    ->with(['lead'=>$this->lead, 'partner'=>$this->partner]);
        }
        return $this->from("sasleads@softwareadvisoryservice.com", "Software Advisory Service")
            ->subject("SAS New Sales Opportunity: ".$this->lead->refernce)
            ->view('mail.lead')->with(['lead'=>$this->lead, 'partner'=>$this->partner]);
    }
}
