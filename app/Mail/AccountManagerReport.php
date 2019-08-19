<?php

namespace App\Mail;

use App\Lead;
use App\Partner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountManagerReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $lead_id;
    public $partner_ids;
    public $lead;
    public $partners;
    /**
     * Create a new message instance.
     *
     * @param $lead_id
     * @param $partner_ids
     */
    public function __construct($lead_id, $partner_ids)
    {
        $this->lead_id = $lead_id;
        $this->partner_ids = $partner_ids;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->lead = Lead::find($this->lead_id);
        $this->partners = Partner::whereIn('id', $this->partner_ids)->get();
        return $this->view('mail.account_manager_report')->with(['lead'=>$this->lead, 'partners'=>$this->partners]);
    }
}
