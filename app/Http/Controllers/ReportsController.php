<?php

namespace App\Http\Controllers;

use App\AccountManager;
use App\EmailGroup;
use App\EmailQueue;
use App\Lead;
use App\Mail\AccountManagerReport;
use App\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class ReportsController extends VoyagerBaseController
{
    public function index(Request $request){

        return view('reports.index');
    }
    public function lead_report($lead_id){
        $this->authorize('browse', app('App\Lead'));
        $lead = Lead::findOrFail($lead_id);
        $email_queues = EmailQueue::where(['lead_id'=>$lead_id])->pluck('partner_id');
        $real_overall = EmailQueue::where(['lead_id'=>$lead_id])->count();
        $account_managers = Partner::whereIn('id', $email_queues)->whereNotNull('account_manager')->select('account_manager')->distinct('account_manager')->pluck('account_manager');
//        $email_groups = EmailGroup::all();
        $result = [];
        $overall = 0;
        foreach ($account_managers as $account_manager){
            $partners = Partner::whereIn('id', $email_queues)->where(['account_manager'=>$account_manager])->get();
            $am = AccountManager::where(['name'=>$account_manager])->first();
            $emails = "";
            if ($am){
                $emails = $am->emails;
            }
            array_push($result, collect(['name'=>$account_manager, 'data'=>$partners, 'count'=>count($partners), 'emails'=>$emails]));
            $overall += count($partners);
        }
//        return json_encode($result);
        return view('reports.lead_report', compact('result', 'lead', 'overall', 'real_overall'));
    }
    public function lead_report_email($lead_id, Request $request){
        if (! $request->filled('account_manager')){
            return redirect()
                ->route("lead_report", ['lead_id'=>$lead_id])
                ->with([
                    'message'    => "Some error occurred 1",
                    'alert-type' => 'error',
                ]);
        }
        $account_manager = $request->input('account_manager');
        if ($account_manager == 'all'){
            $lead = Lead::findOrFail($lead_id);
            $email_queues = EmailQueue::where(['lead_id'=>$lead_id])->pluck('partner_id');
            $account_managers = Partner::whereIn('id', $email_queues)->whereNotNull('account_manager')->select('account_manager')->distinct('account_manager')->pluck('account_manager');

            foreach ($account_managers as $account_manager_name){
                $partners = Partner::whereIn('id', $email_queues)->where(['account_manager'=>$account_manager_name])->get();
                $am = AccountManager::where(['name'=>$account_manager_name])->first();
                if($am) {
                    $mailable = new AccountManagerReport($lead->id, $partners);
                    foreach (explode(';', $am->emails) as $email) {
                        if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                            Mail::to(trim($email))->send($mailable);
                        }
                    }
                }
            }
            return redirect()
                ->route("lead_report", ['lead_id'=>$lead_id])
                ->with([
                    'message'    => "Emails sent successfully",
                    'alert-type' => 'success',
                ]);
        }
        else {
            $am = AccountManager::where(['name'=>$account_manager])->first();
            if(! $am ){
                return redirect()
                    ->route("lead_report", ['lead_id'=>$lead_id])
                    ->with([
                        'message'    => "Account Manager ". $account_manager . "doesn't have an account manager record",
                        'alert-type' => 'error',
                    ]);
            }
            $lead = Lead::findOrFail($lead_id);
            $email_queues = EmailQueue::where(['lead_id'=>$lead->id])->pluck('partner_id');
            $partners = Partner::whereIn('id', $email_queues)->where(['account_manager'=>$account_manager])->pluck('id');
            $mailable = new AccountManagerReport($lead->id, $partners);
            foreach(explode(';', $am->emails) as $email){
                if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    Mail::to(trim($email))->send($mailable);
                }
            }
            return redirect()
                ->route("lead_report", ['lead_id'=>$lead_id])
                ->with([
                    'message'    => "Emails sent successfully",
                    'alert-type' => 'success',
                ]);
        }
    }
}
