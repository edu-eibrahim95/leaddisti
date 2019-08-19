<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix'=>'admin'], function () {
    Voyager::routes();
    Route::post('partners/upload', 'CSVController@upload')->name('upload_csv');
    Route::post('queue/work', 'QueueController@processQueue')->name('process_queue');
    Route::get('reports', 'ReportsController@index')->middleware('admin.user');
    Route::get('lead/{lead_id}/report', 'ReportsController@lead_report')->middleware('admin.user')->name('lead_report');
    Route::post('lead/{lead_id}/report/email', 'ReportsController@lead_report_email')->middleware('admin.user')->name('lead_report_email');
});
Route::get('/', function () {
    return redirect('/admin');
});
Route::get('mailable', function () {
    $p = App\Partner::find(128);
    $invoice = App\Lead::find(1);

    return new App\Mail\LeadMailable(128, 4);
});
Route::get('mailable2', function () {
    $p = App\Lead::find(1);
    $invoice = [1328, 1329,1330];

    return new App\Mail\AccountManagerReport(4, $invoice);
});
