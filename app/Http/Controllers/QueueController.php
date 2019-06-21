<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class QueueController extends Controller
{
    public function processQueue(Request $request){
        $exitCode = Artisan::output('queue:work --stop-when-empty');
        $out = Artisan::output();
        Session::flash('queu_worked', 'queue started successfully');
        return redirect(route('voyager.email-queues.index'));
    }
}
