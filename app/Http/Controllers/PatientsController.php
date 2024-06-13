<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use View;
use App\Models\Covid;
use App\Models\Facility;
use App\Models\Location\Hub;
use App\Models\District;
use App\Models\Result;
use App\Models\CovidSamples;
use App\Models\CovidResult;
use App\Models\Worksheet;
use App\Models\WorksheetSample;
use Auth;
//use \DB;
class PatientsController extends Controller {

	private function getCovidData(){
		$patients = Covid::get();
		$districts = [''=>''] + District::where('id', '>', '0')->lists('district', 'id');
		$facilities= [''=>''] + Facility::where('id', '>', '0')->lists('facility', 'facility');
		$nationality = [''=>''] + DB::table('nationalities')->lists('nationality','nationality');
		$poe = [''=>''] + Facility::where('facilityLevelID', '=', '14')->lists('facility','facility');

		return [
			'poe'=>$poe,
			'patients'=>$patients,
			'districts'=>$districts,
			'facilities'=>$facilities,
			'nationality'=>$nationality,
		];
	}

	public function lifForm(){
		$districts = [''=>''] + District::where('id', '>', '0')->lists('district', 'id');
		$facilities= [''=>''] + Facility::where('id', '>', '0')->lists('facility', 'facility');
		$nationality = [''=>''] + \DB::table('nationalities')->lists('nationality','nationality');
		$poe = [''=>''] + Facility::where('facilityLevelID', '=', '14')->lists('facility','facility');
		$m = [''=>''] + WorksheetSample::where('assigned_sample', '=', '1')->lists('locator_id','locator_id');
		$locator_ids = [''=>''] + CovidSamples::whereNull('patient_id')->lists('specimen_ulin','specimen_ulin');
		
		return view("covid.lifForm", compact('facilities','poe','nationality','districts','m','locator_ids'));
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
