<?php namespace App\Http\Controllers;
use View;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\District;
use App\Models\Location\Hub;
use App\Models\Covid;
use App\Models\Interviewer;
use App\Models\CovidSamples;
use App\Models\CollectionPoints;
use App\Models\isHealthCareWorker;
use App\Models\CovidResult;
use App\Models\WorksheetSample;
use Illuminate\Http\Request;
use Session;
use Validator;
use Lang;
use Input;
use Carbon;
use Redirect;
use yajra\Datatables\Facades\Datatables;
use DB;
use Auth;
use App\Closet\MyHTML as MyHTML;

class DataEntryController extends Controller {


	public function lifForm(){
		$facilities = MyHTML::array_merge_maintain_keys([''=>''], Facility::where('id', '>', '0')->pluck('facility', 'facility'));
		$poe =  MyHTML::array_merge_maintain_keys([''=>''],Facility::where('facilityLevelID', '=', '14')->pluck('facility','facility'));
		$nationality =  MyHTML::array_merge_maintain_keys([''=>''], DB::table('nationalities')->pluck('nationality','nationality'));
		$districts =  MyHTML::array_merge_maintain_keys([''=>''],District::where('id', '>', '0')->pluck('district', 'id'));
		//$locator_id = MyHTML::array_merge_maintain_keys([''=>''],WorksheetSample::where('assigned_sample', '=', 0)->pluck('locator_id','locator_id'));
		if(MyHTML::is_ref_lab()){
			dd('Access denied');

		}
		$locator_ids = [];
		$locator_id = '';
		return view("covid.lifForm", compact('facilities','poe','nationality','districts','locator_id','locator_ids'));
	}

	public function cifForm(){
		$facilities =  MyHTML::array_merge_maintain_keys([''=>''],Facility::where('id', '>', '0')->pluck('facility', 'facility'));
		$poe =  MyHTML::array_merge_maintain_keys([''=>''],Facility::where('facilityLevelID', '=', '14')->pluck('facility','facility'));
		$nationality =  MyHTML::array_merge_maintain_keys([''=>''], DB::table('nationalities')->pluck('nationality','nationality'));
		$districts =  MyHTML::array_merge_maintain_keys([''=>''], District::where('id', '>', '0')->pluck('district', 'id'));
		$locator_id =  MyHTML::array_merge_maintain_keys([''=>''], WorksheetSample::where('assigned_sample', '=', 0)->pluck('locator_id','locator_id'));

		return view("covid.cifForm", compact('facilities','poe','nationality','districts','locator_id'));
	}

	public function store(Request $request){
		$patient = new Covid;

		$patient->dataEntryDate = $request->data_entry_date;
		$patient->formType = $request->formType;
		$patient->patient_surname = $request->patient_surname;
		$patient->patient_firstname = $request->patient_firstname;
		$patient->sex = $request->sex;
		$patient->dob = empty($request->dob) ? $request->dob : '';
		$patient->age = $request->age;
		$patient->age_units = $request->age_units;
		$patient->nationality = $request->nationality;
		$patient->passportNo = $request->passportNo;
		$patient->patient_contact = $request->patient_contact;
		//$patient->patient_village = $request->patient_village;
		//$patient->patient_parish = $request->patient_parish;
		//$patient->patient_subcounty = $request->patient_subcounty;
		$patient->patient_district = $request->patient_district;
		$patient->patient_NOK = $request->patient_NOK;
		$patient->nok_contact = $request->nok_contact;
		$patient->epidNo = $request->serial_number;
		$patient->caseID = !empty($request->caseID) ? $request->caseID : $request->ulin;
		$patient->truckNo = $request->truckNo;
		$patient->truckDestination = $request->truckDestination;
		$patient->truckEntryDate = $request->truckEntryDate;
		$patient->tempReading = $request->tempReading;
		$patient->sampleCollected = $request->sampleCollected;
		$patient->pointOfEntry = $request->pointOfEntry;
		$patient->health_facility = $request->health_facility;
		$patient->request_date = $request->request_date;
		$patient->ulin = $request->ulin;
		$patient->eac_pass_id = $request->eac_pass_id;
		$patient->email_address = $request->email_address;
		$patient->branch_id = \Auth::user()->branch_id; 


		$request->origin == "UGANDAN" ? $patient->nationality = "UGANDAN" : $patient->nationality = $request->foreign_nationality;

		if(\Request::has("patient_district"))
		{
			$patient->patient_district 	= $request->patient_district;
			$patient->origin = 1;
		}elseif(\Request::has('foreign_nationality')){
			$patient->foreignDistrict = $request->foreignDistrict;
			$patient->patient_district 	= 2127;
			$patient->nationality = $request->foreign_nationality;
			$patient->origin = 0;
		}else {
			$patient->patient_district 	= 2127;
		}

		$patient->where_sample_collected_from = $request->where_sample_collected_from;
		$patient->serial_number = $request->serial_number;

		if(\Request::has('facility')){
			$get_facility = $patient->nameWhere_sample_collected_from = $request->facility;
			$this->validate($request,['facility' => 'required'],['facility.required' => 'Specify/select facility where sample was collected from.']);
			$get_facility_district = Facility::where('facility','=',$get_facility)->get(['districtID']);
			$patient->facility_district = $get_facility_district[0]['attributes']['districtID'];

			$sd = District::where('id','=',$get_facility_district[0]['attributes']['districtID'])->get(['district']);
			if($request->facility == ''){
				$patient->swabing_district = \MyHTML::getBranchDistrict(\Auth::user()->branch_id);
			}else{
				$patient->swabing_district = $sd[0]['attributes']['district'];
			}
			
		}elseif(\Request::has('poe')){
			$get_poe = $patient->nameWhere_sample_collected_from = $request->poe;
			$this->validate($request,['poe' => 'required'],	['poe.required' => 'Specify/select POE.']);
			$get_poe_district = Facility::where('facility','=',$get_poe)->get(['districtID']);
			$patient->facility_district = $get_poe_district[0]['attributes']['districtID'];

			$sd = District::where('id','=',$get_poe_district[0]['attributes']['districtID'])->get(['district']);
			$patient->swabing_district = $sd[0]['attributes']['district'];
		}elseif(\Request::has('quarantine')){
			$patient->nameWhere_sample_collected_from = $request->quarantine;
			$this->validate($request,['quarantine' => 'required'],['quarantine.required' => 'Specify the Quarantine area/name where sample was collected from.',]);

			$this->validate($request,['q_swabing_district' => 'required'],['q_swabing_district.required' => 'Specify the district where quarantine is located',]);
			$sd = District::where('id','=',$request->q_swabing_district)->get(['district'])->toArray();
			$patient->swabing_district = $sd[0]['district'];

		}elseif(\Request::has('other')) {
			$patient->nameWhere_sample_collected_from = $request->other;
			$this->validate($request,['other' => 'required'],['other.required' => 'Specify the area/name where sample was collected from.',]);

			$this->validate($request,['swabing_district' => 'required'],['swabing_district.required' => 'Specify the district where sample was collected from',]);
			$sd = District::where('id','=',$request->swabing_district)->get(['district'])->toArray();
			$patient->swabing_district = $sd[0]['district'];;

		}else {
			//do nothing
		}

		$patient->who_being_tested = $request->who_being_tested;
		$patient->is_health_care_worker_being_tested = $request->is_health_care_worker_being_tested;
		$patient->health_care_worker_facility = $request->health_care_worker_facility;
		$patient->reason_for_healthWorker_testing = $request->reason_for_healthWorker_testing != "Other" ? $request->reason_for_healthWorker_testing : $request->reason_for_healthWorker_testingOther;
		$patient->isolatedPerson_test_day = $request->isolatedPerson_test_day != "Other" ? $request->isolatedPerson_test_day : $request->isolatedPerson_test_dayOther;
		$patient->travel_out_of_ug_b4_onset = $request->travel_out_of_ug_b4_onset;
		$patient->destination_b4_onset = $request->destination_b4_onset;
		$patient->return_date = $request->return_date !="" ? $request->return_date : "0000-00-00 00:00:00";
		$patient->patient_symptomatic = $request->patient_symptomatic;
		$patient->symptomatic_onset_date = $request->symptomatic_onset_date;

		$patient->interviewer_name = $request->interviewer_name;
		$patient->interviewer_facility = $request->interviewer_facility;
		$patient->interviewer_phone = $request->interviewer_phone;
		$patient->interviewer_email = $request->interviewer_email;
		$patient->typeOfSite = $request->typeOfSite;
		$patient->createdby = Auth::user()->id;
		$patient->ref_lab = Auth::user()->ref_lab;

		if($request->formType == 'lif') {

			$this->validate($request,[
			'request_date' => 'required',
			'specimen_type' => 'required',
			'patient_surname' => 'required',
			'interviewer_name' => 'required',
			'where_sample_collected_from' => 'required',
			'serial_number' => 'required',
			'origin' => 'required',
			],
			[
			'serial_number.required' => ' Please enter the sample ID.',
			'request_date.required' => ' Plesae enter registration date.',
			'patient_surname.required' => ' Please enter the patient full names',
			'where_sample_collected_from.required' => 'Select the site of sample collection.',
			'specimen_type.required' => 'Select atleast 1 sample type',
			'interviewer_name.required' => 'Enter interviewer name.',
			'origin.required' => 'Please choose a nationality or if not provided, select left blank.',

			]);
		}


		if($request->who_being_tested == "Traveler" || \Request::has('receipt_number'))
		{
			$patient->receipt_number = $request->receipt_number;
			$this->validate($request,['receipt_number' => 'required'],['receipt_number.required' => 'Please enter the bank receipt number.']);
		}
		if(Auth::user()->ref_lab == 55){

			$this->validate($request,[
				'test_result' => 'required',
				'test_method' => 'required',
				'test_date' => 'required',
				'testedBy' => 'required',
			],
				[
					'test_result.required' => 'Please enter the test result..',
					'test_method.required' => 'Please select test method used.',
					'test_date.required' => 'Please enter the test date.',
					'testedBy.required' => 'Please provide name of lab tech who tested sample.',
				]);
		}

		$patient->save();

		//now save the sample details
		//build the sample array
		$samples_data = [
			'patient_id' => $patient->id,
			'specimen_collection_date' => $request->sample_collection_date,
			'specimen_ulin' => $request->serial_number,
			'typeOfSite' => $request->typeOfSite,
			'dataEntryDate' => Carbon::today(),
			'createdby' => Auth::user()->id,
			'testing_lab' => env('LAB_ID'),
			'branch_id'=> \Auth::user()->branch_id,
		];
		$validator = Validator::make($samples_data, [
			'specimen_ulin' => 'required|unique:covid_samples',
			
			//'date_of_collection'=>"required|date|before_or_equal:".$result_data['sample_received_on']
			//."|before_or_equal:".date('Y-m-d H:i:s')."|after_or_equal:".date('1970-01-02 00:00:00'),
			],
			[
			'specimen_ulin.required' => 'The specimen identifier is required ',
			'specimen_ulin.unique' => 'The specimen identifier, '.$samples_data['specimen_ulin'].' is already used',
			
			//'date_of_collection.before_or_equal' => "The collection date cannot be greater than reception date or today's date for row ".$count,
			]
		);
		$error_messages = [];
		if($validator->fails()) {
			$messages = $validator->errors();
			$return_msg = array();
			foreach ($messages->all() as $message) {
				array_push($return_msg, $message);
			}
			// dd($return_msg);
			$error_messages[] = $return_msg;
			return \Redirect::back()->withInput()->withFlashMessage('danger',$error_messages);
		}else{
			$sample = CovidSamples::updateOrCreate(['specimen_ulin' => $samples_data['specimen_ulin']],$samples_data);
			//$update_covid_worksheet = "update worksheet_samples set assigned_sample = 1, sample_id = ". "'".$sample->id."'". "where locator_id = ". "'".$sample->specimen_ulin."'";
			// dd($update_covid_worksheet);
			$sql = \DB::statement($update_covid_worksheet);		
			return \Redirect::back()->with('success','Successfully saved form with patient ID '.$patient->epidNo.' Enter next form');
		}
	}

}
