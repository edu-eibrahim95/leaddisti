<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use  App\Imports\PartnerImport;
use Illuminate\Support\Facades\Session;

class CSVController extends Controller
{
    public function upload(Request $request){
        try {
            foreach($request->file('files') as $file){
                $path = $file->store('public/temp');
                Excel::import(new PartnerImport, $path);
            }
        }
        catch (\Throwable $e){
            Session::flash('import_error', 'There Was an error importing your file please make sure you are uploading a valid file'.$e->getMessage());
            return redirect()->back();
        }
        Session::flash('import_success', 'Imported Successfully');

        return redirect(route('voyager.partners.index'));

    }
}
