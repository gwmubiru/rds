<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\CovidResultsController as CovidResultsController;
use App\Http\Controllers\EbolaController as EbolaController;
use App\Http\Controllers\SessionsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('sessions/create');
});

Auth::routes();

Route::get('/airport', ['as'=>'airport', 'uses' => 'App\Http\Controllers\ManageResultsController@airportResult']);
Route::get('/marquee', ['as'=>'marquee', 'uses' => 'App\Http\Controllers\ManageResultsController@airportMarquee']);

//send email results to clients
Route::match(['GET', 'POST'], '/outbreaks/result/{id?}', ['uses'=>'App\Http\Controllers\CovidResultsController@result']);
Route::match(['GET', 'POST'], '/email_result', ['uses'=>'App\Http\Controllers\CovidResultsController@emailResult']);

#Send Covid alerts through email
Route::get('/email_sent', 'App\Http\Controllers\ManageResultsController@sendEmail');

#validate QrCode
Route::get('/validator/{epidNo}', 'App\Http\Controllers\ManageResultsController@validateQrCode');

#validate QrCode
Route::get('/evd/validator/{id}', 'App\Http\Controllers\EbolaController@validateQrCode');

#upload District Administrative Units
Route::post("/upload_districtFiles", array( "as"   => "upload.districtFiles",   "uses" => "App\Http\Controllers\HatController@uploadDistrictFile"));

//validate app credentials
Route::match(array('GET','POST'),'/authenticate', 'App\Http\Controllers\Admin\UserController@appValidator');

#New

//otp routes
Route::post('/auth/otp', ['as' => 'otp', 'uses' => 'App\Http\Controllers\SessionsController@verifyOTP']);
Route::group(['middleware'=> 'cors'], function(){
    Route::match(array('GET', 'POST'), '/system_sync', ['as' => 'system_sync', 'uses' => 'ResultSynchronizationController@processSyncRequest']);
});

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::get('/ulist', [App\Http\Controllers\UserController::class, 'list'])->name('ulist');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::match(['GET', 'POST'],'/outbreaks/list', ['permission'=>52,'as' => 'outbreakslist', 'uses' => 'CovidResultsController@index']);
    /*
    * Start The patient capture routes
    */
    Route::match(['GET', 'POST'],'/cases/list', ['permission'=>11,'as' => 'case_list', 'uses' => 'App\Http\Controllers\CaseManagementController@index']);
    Route::get('/cases/list_data', ['permission'=>11,'as' => 'case_list_data', 'uses' => 'App\Http\Controllers\CaseManagementController@list_data']);

    Route::match(['GET', 'POST'],'/mass_patient_info', ['permission'=>53,'as' => 'mass_patient_info', 'uses' => 'App\Http\Controllers\CaseManagementController@massUpdatePatientInfo']);

    Route::match(['GET', 'POST'],'/mass_assign_results', ['permission'=>53,'as' => 'mass_assign_results', 'uses' => 'App\Http\Controllers\CaseManagementController@massAssignResults']);

    Route::match(['GET', 'POST'],'/mass_assign_lab_numbers_results', ['permission'=>53,'as' => 'mass_assign_lab_numbers_results', 'uses' => 'App\Http\Controllers\CaseManagementController@massAssignLabNumbersResults']);

    Route::match(['GET', 'POST'],'/cases/edit/{id?}', ['permission'=>11,'as' => 'case_edit', 'uses' => 'App\Http\Controllers\CaseManagementController@editPatient']);
    Route::match(['GET', 'POST'],'/cases/update/{id?}', ['permission'=>53,'as' => 'case_update', 'uses' => 'App\Http\Controllers\CaseManagementController@update']);
    Route::match(array('GET', 'POST'), '/cases/release_retain_manual_entries', ['permission'=>53,'as' => 'release_retain_manual_entries', 'uses' => 'App\Http\Controllers\CaseManagementController@release_retain_manual_entries']);

    // covid routes
    Route::get('/covid', ['permission' => 11,'uses'=>'App\Http\Controllers\CovidDataEntryController@index']);
    Route::get('/lif/covid/form', ['permission'=>11,'as' => 'lifForm', 'uses' => 'App\Http\Controllers\DataEntryController@lifForm']);

    /*
    * End the patient capture routes
    */

    //for authentications -- start here
    //STart Manual upload of results
    Route::match(array('GET', 'POST'), '/outbreakrlts', ['permission'=>21,'as' => 'outbreakrlts', 'uses' => 'App\Http\Controllers\ManageResultsController@store_out']);
    Route::match(array('GET', 'POST'), '/update_result', ['permission'=>22,'as' => 'update_result', 'uses' => 'App\Http\Controllers\ManageResultsController@update_result']);
    Route::match(array('GET', 'POST'), '/outbreakrlts/approve_retain', ['permission'=>21,'as' => 'approve_retain', 'uses' => 'App\Http\Controllers\ManageResultsController@approve_retain']); // direct
    Route::match(['GET', 'POST'],'/outbreakrlts/list', ['permission'=>21,'as' => 'outbreakrltslist', 'uses' => 'App\Http\Controllers\ManageResultsController@index']);
    Route::get('/outbreakrlts/list_data', ['permission'=>21,'as' => 'results_list_data', 'uses' => 'App\Http\Controllers\ManageResultsController@list_data']);
    Route::match(['GET', 'POST'], '/outbreakrlts/result/{id?}', ['uses'=>'App\Http\Controllers\ManageResultsController@result']);
    //End manual upload of results

    //Start Auto upload of results
    Route::match(array('GET', 'POST'), '/outbreaks', ['permission'=>21,'as' => 'outbreaks', 'uses' => 'CovidResultsController@store_out']);
    Route::match(array('GET', 'POST'), '/update_outbreak_result', ['permission'=>22,'as' => 'update_outbreak_result', 'uses' => 'App\Http\Controllers\CovidResultsController@update_outbreak_result']);


    Route::match(array('GET', 'POST'), '/outbreaks/release_retain', ['permission'=>22,'as' => 'release_retain', 'uses' => 'App\Http\Controllers\CovidResultsController@release_retain']); // direct
    Route::match(['GET', 'POST'],'/outbreaks/list', ['permission'=>52,'as' => 'outbreakslist', 'uses' => 'App\Http\Controllers\CovidResultsController@index']);
    Route::match(['GET', 'POST'],'/outbreaks/export_to_csv', ['permission'=>21,'as' => 'export_to_csv', 'uses' => 'App\Http\Controllers\CovidResultsController@exportToCsv']);

    Route::match(['GET', 'POST'],'/outbreaks/export_data', ['as' => 'export_data', 'uses' => 'App\Http\Controllers\CovidResultsController@exportData']);

    Route::get('/outbreaks/list_data', ['permission'=>52,'as' => 'list_data', 'uses' => 'App\Http\Controllers\CovidResultsController@list_data']);
    //Route::match(['GET', 'POST'], '/outbreaks/result/{id?}', ['uses'=>'App\Http\Controllers\CovidResultsController@result']);
    //update results - district
    Route::match(['GET', 'POST'],'/mass_update_results', ['permission'=>21,'as' => 'mass_update_results', 'uses' => 'App\Http\Controllers\CovidResultsController@massUpdateResults']);

   
    ##
    ##
    ##  ADMIN ROUTES====================================================================
    ##
    ##

    # EID Admin Home
    Route::get('/admin', ['uses'=>'App\Http\Controllers\Admin\AdminHomeController@index']);
    Route::get('/admin/home', ['uses'=>'App\Http\Controllers\Admin\AdminHomeController@index']);

    #routes for appendices
    Route::get('appendices/index/{cat_id}',['permission'=>45,'uses'=>'App\Http\Controllers\Admin\AppendixController@index']);
    Route::post('appendices/store/{cat_id}',['permission'=>45,'uses'=>'App\Http\Controllers\Admin\AppendixController@store']);
    Route::get('appendices/edit/{cat_id}/{edit_id}',['permission'=>45,'uses'=>'App\Http\Controllers\Admin\AppendixController@edit']);
    Route::post('appendices/update/{cat_id}/{edit_id}',['permission'=>45,'uses'=>'App\Http\Controllers\Admin\AppendixController@update']);
    Route::get('appendices/deactivate/{cat_id}/{edit_id}/{status}',['permission'=>45,'uses'=>'App\Http\Controllers\Admin\AppendixController@deactivate']);

    # EID IP Management
    Route::get('ips/index',['permission'=>46,'uses'=>'App\Http\Controllers\Admin\IPController@index']);
    Route::get('ips/create',['permission'=>46,'uses'=>'App\Http\Controllers\Admin\IPController@create']);
    Route::post('ips/store',['permission'=>46,'uses'=>'App\Http\Controllers\Admin\IPController@store']);
    Route::get('ips/show/{id}',['permission'=>46,'uses'=>'App\Http\Controllers\Admin\IPController@show']);
    Route::get('ips/edit/{id}',['permission'=>46,'uses'=>'App\Http\Controllers\Admin\IPController@edit']);
    Route::post('ips/update/{id}',['permission'=>46,'uses'=>'App\Http\Controllers\Admin\IPController@update']);


    # EID Facility Management
    Route::get('facilities/index',['permission'=>47,'uses'=>'App\Http\Controllers\Admin\FacilityController@index']);
    Route::get('facilities/create',['permission'=>47,'uses'=>'App\Http\Controllers\Admin\FacilityController@create']);
    Route::post('facilities/store',['permission'=>47,'uses'=>'App\Http\Controllers\Admin\FacilityController@store']);
    Route::get('facilities/show/{id}',['permission'=>47,'uses'=>'App\Http\Controllers\Admin\FacilityController@show']);
    Route::get('facilities/edit/{id}',['permission'=>47,'uses'=>'App\Http\Controllers\Admin\FacilityController@edit']);
    Route::post('facilities/update/{id}',['permission'=>47,'uses'=>'App\Http\Controllers\Admin\FacilityController@update']);
    Route::get('facilities/live_search/{q}',['permission'=>47,'uses'=>'App\Http\Controllers\Admin\FacilityController@live_search']);


    Route::get('user_pwd_change',['uses'=>'Admin\UserController@user_pwd_change']);
    Route::post('post_user_pwd_change',['uses'=>'Admin\UserController@post_user_pwd_change']);

    

    # EID Location Management
    Route::get('locations/home',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\AdminHomeController@LocationsHome']);

    Route::get('locations/regions',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\RegionController@index']);
    Route::post('locations/regions/store',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\RegionController@store']);
    Route::get('locations/regions/edit/{edit_id}',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\RegionController@edit']);
    Route::post('locations/regions/update/{edit_id}',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\RegionController@update']);



    # EID Hubs Management
    Route::get('locations/hubs', ['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\HubController@index']);
    Route::post('locations/hubs/store', ['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\HubController@store']);
    Route::get('locations/hubs/edit/{edit_id}', ['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\HubController@edit']);
    Route::post('locations/hubs/update/{edit_id}',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\HubController@update']);



    # EID Districts Management
    Route::get('locations/districts',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\DistrictController@index']);
    Route::post('locations/districts/store',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\DistrictController@store']);
    Route::get('locations/districts/edit/{edit_id}',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\DistrictController@edit']);
    Route::post('locations/districts/update/{edit_id}',['permission'=>48,'uses'=>'App\Http\Controllers\Admin\Location\DistrictController@update']);

    Route::get('/results', ['permission'=>52, 'uses' => 'App\Http\Controllers\Admin\FacilityController@results']);
    


    Route::get('/poe/covid/form', ['permission'=>15,'as' => 'poeForm', 'uses' => 'App\Http\Controllers\CovidDataEntryController@poeForm']);
    Route::get('/edit/{id}', ['permission'=>1,'as' => 'editForm', 'uses' => 'App\Http\Controllers\CovidDataEntryController@editForm']);
    Route::post('covid/update/{id}',['permission'=>11, 'as' => 'covid.update', 'uses '=> 'App\Http\Controllers\CovidDataEntryController@updateForm']);

    Route::get('/suspected/cases', ['permission' => 11, 'uses' => 'App\Http\Controllers\CovidDataEntryController@listSuspects']);

    Route::post('save/covid/form',['permission'=>11,'uses'=>'App\Http\Controllers\CovidDataEntryController@store']);
    Route::get('/covid/patient/data', ['permission'=>11,'as' => 'covid_data', 'uses' => 'App\Http\Controllers\CovidDataEntryController@inq']);
    Route::get('/getCovidData', ['as'=>'getCovidData', 'uses' => 'App\Http\Controllers\CovidDataEntryController@getLabData']);
    Route::get('/getSuspectData', ['as'=>'getSuspectData', 'uses' => 'App\Http\Controllers\CovidDataEntryController@getSuspectData']);

    Route::get("/audit_download/", array(   "as"   => "audit.download", "uses" => "App\Http\Controllers\AuditTrailController@getAuditCsv"));
    Route::get("/covid_download/", array(   "as"   => "covid.download.csv", "uses" => "App\Http\Controllers\CovidDataEntryController@getCovidCsv"));
    Route::get("/all_covid_download/", array(   "as"   => "covid.download", "uses" => "App\Http\Controllers\CovidDataEntryController@getAllCovidCsv"));

     // HAT routes
        Route::get("/hat", array("as"   => "hat",        "uses" => "App\Http\Controllers\HatController@index"));
    Route::get("/generate_hatcsv", array(   "as"   => "generate_hatcsv",    "uses" => "App\Http\Controllers\HatController@HatCsv"));

    Route::get('/newLogisticsData', ['as'=>'newLogisticsData', 'uses' => 'App\Http\Controllers\LogisticsController@newLogisticsData']);
    Route::get('/Logistics', ['as'=>'Logistics', 'uses' => 'App\Http\Controllers\LogisticsController@index']);
    Route::post('/saveLogisticsReport', ['as'=>'saveLogisticsReport', 'uses' => 'App\Http\Controllers\LogisticsController@store']);
    Route::post('importLogisiticsExcel', 'App\Http\Controllers\LogisticsController@importExcel');
    Route::get("/Logisitiscsv", array(  "as"   => "Logisiticscsv.download", "uses" => "App\Http\Controllers\LogisticsController@getCsv"));

    //audit_trail routes
    Route::get('/audit{type?}', ['as'=>'audit', 'uses' => 'App\Http\Controllers\AuditTrailController@index']);
    Route::get('/audit/get_data', ['permission'=>11,'as' => 'audit_trail', 'uses' => 'App\Http\Controllers\AuditTrailController@list_data']);
    // Route::get("/covid_download/", array(    "as"   => "covid.download", "uses" => "AuditTrailController@getAuditCsv"));

    // Ebola
    Route::get('/cif/evd/',['permission' => 11,'as' => 'cifEvd','uses'=>'App\Http\Controllers\EbolaController@cifEvd']);
    Route::post('/upload_evd_csv', 'App\Http\Controllers\EbolaController@uploadEvdCSV');
    Route::get('/evd', ['as'=>'evd', 'uses' => 'App\Http\Controllers\EbolaController@index']);
    Route::get('/evd/pending_printing', ['as'=>'pending_printing', 'uses' => 'App\Http\Controllers\EbolaController@pendingPrinting']);
    Route::get('/print_evd_result', ['as'=>'print_evd_result', 'uses' => 'App\Http\Controllers\EbolaController@resultPdf']);
    Route::match(['GET', 'POST'],'/evd/export_data', ['as' => 'export_evd_data', 'uses' => 'App\Http\Controllers\EbolaController@exportData']);
    
    #To download data in excel
    Route::get('excel', function(){
        $ex=Excel::create(session('excel_file_name'), function($excel) {
            $excel->sheet('Sheet1', function($sheet) {
                $sheet->fromArray(session('excel_data'));
            });
        });
        return $ex->download('xls');
    });

    Route::get('csv', function(){
        $ex=Excel::create(session('csv_file_name'), function($excel) {
            $excel->sheet('Sheet1', function($sheet) {
                $sheet->fromArray(session('csv_data'));
            });
        });
        return $ex->download('csv');
    });

});
use App\Livewire\Counter;
Route::get('/counter', Counter::class);

