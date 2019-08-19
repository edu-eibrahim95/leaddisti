<?php

namespace App\Mail;

use App\Lead;
use App\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class LeadMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $partner_id;
    public $lead_id;
    public $lead;
    public $partner;
    public function __construct($partner,$lead)
    {
        $this->partner_id = $partner;
        $this->lead_id = $lead;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->lead = Lead::find($this->lead_id);
        $this->partner = Partner::find($this->partner_id);
        if (! empty($this->lead->pdf_file) && count(json_decode($this->lead->pdf_file)) > 0){
            return $this->from("sasleads@softwareadvisoryservice.com", "Software Advisory Service")
                ->subject("SAS New Sales Opportunity: ".$this->lead->refernce . ' - '.$this->lead->project)
                ->view('mail.lead')->attach(Storage::disk(config('voyager.storage.disk'))->getDriver()->getAdapter()->getPathPrefix().json_decode($this->lead->pdf_file)[0]->download_link,
                    ['as'=>$this->lead->refernce.'.pdf'])
                    ->with(['lead'=>$this->lead, 'partner'=>$this->partner]);
        }
        return $this->from("sasleads@softwareadvisoryservice.com", "Software Advisory Service")
            ->subject("SAS New Sales Opportunity: ".$this->lead->refernce. ' - '.$this->lead->project)
            ->view('mail.lead')->with(['lead'=>$this->lead, 'partner'=>$this->partner]);
    }
}
