<?php namespace App\Http\Controllers;
use View;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\District;
use App\Models\Location\Hub;
use App\Models\Covid;
use App\Models\CovidSamples;
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

class MiniLIFController extends Controller {

	public function MiniLif(){
		$facilities = MyHTML::array_merge_maintain_keys([''=>''],Facility::where('id', '>', '0')->pluck('facility', 'facility'));
		$poe = MyHTML::array_merge_maintain_keys([''=>''],Facility::where('facilityLevelID', '=', '14')->pluck('facility','facility'));
		$nationality = MyHTML::array_merge_maintain_keys([''=>''],DB::table('nationalities')->pluck('nationality','nationality'));
		$districts = MyHTML::array_merge_maintain_keys([''=>''],District::where('id', '>', '0')->pluck('district', 'id'));
		$locator_id = MyHTML::array_merge_maintain_keys([''=>''],CovidSamples::whereNull('patient_id')->pluck('specimen_ulin','specimen_ulin'));
		if(\MyHTML::permit(11)){
			return view("covid.mini_lif", compact('facilities','poe','nationality','districts','locator_id'));
		}else{
			return \Redirect::back();
		}
		
	}

	public function getBarcode($epidNo){

		$patient_info = Covid::leftjoin('covid_samples','covid_samples.patient_id','=','covid_patients.id')->where('covid_patients.epidNo','=',$epidNo)->get();
		return $patient_info;
	}

	public function store(Request $request){

		$patient = new Covid;

		\Request::has('id') ? $patient->patientID = $request->id : '';
		$patient->dataEntryDate = $request->data_entry_date;
		$patient->formType = $request->formType;
		$patient->patient_surname = $request->patient_surname;
		$patient->sex = $request->sex;
		$patient->dob = empty($request->dob) ? $request->dob : '';
		$patient->age = $request->age;
		$patient->age_units = $request->age_units;
		$patient->nationality = $request->nationality;
		$patient->passportNo = $request->passportNo;
		$patient->patient_contact = $request->patient_contact;
		$patient->patient_village = $request->patient_village;
		$patient->patient_parish = $request->patient_parish;
		$patient->patient_subcounty = $request->patient_subcounty;
		$patient->patient_district = $request->patient_district;
		$patient->patient_NOK = $request->patient_NOK;
		$patient->nok_contact = $request->nok_contact;
		$patient->epidNo = empty($request->epidNo) ? $request->ulin : $request->epidNo;
		$patient->caseID = empty($request->caseID) ? $request->caseID : $request->ulin;
		$patient->truckNo = $request->truckNo;
		$patient->truckDestination = $request->truckDestination;
		$patient->truckEntryDate = $request->truckEntryDate;
		$patient->tempReading = $request->tempReading;
		$patient->pointOfEntry = $request->pointOfEntry;
		$patient->health_facility = $request->health_facility;
		$patient->request_date = $request->request_date;
		$patient->ulin = $request->ulin;
		$patient->symptomatic_onset_date = $request->symptomatic_onset_date;
		$patient->typeOfSite = $request->typeOfSite;
		$patient->createdby = Auth::user()->id;
		$patient->ref_lab = Auth::user()->ref_lab;
		$patient->who_being_tested = $request->who_being_tested;
		$patient->receipt_number = $request->receipt_number;

		if($request->who_being_tested == "Traveler" || \Request::has('receipt_number'))
		{
			$patient->receipt_number = $request->receipt_number;
			$this->validate($request,['receipt_number' => 'required'],['receipt_number.required' => 'Please enter the bank receipt number.']);
		}

		$request->origin == "UGANDAN" ? $patient->nationality = "UGANDAN" : $patient->nationality = $request->foreign_nationality;

		if(\Request::has("patient_district"))
		{
			$this->validate($request,['patient_district' => 'required'],['patient_district.required' => 'Patient district is required']);
			$patient->patient_district 	= $request->patient_district;
			$patient->origin = 1;
		}

		elseif(\Request::has('foreign_nationality'))
		{
			$patient->foreignDistrict = $request->foreignDistrict;
			$patient->patient_district 	= 2127;
			$patient->nationality = $request->foreign_nationality;
			$patient->origin = 0;
		}
		else {
			$patient->patient_district 	= 2127;
		}

		$patient->where_sample_collected_from = $request->where_sample_collected_from;
		$patient->serial_number = $request->serial_number;

		if(\Request::has('facility'))
		{
			$this->validate($request,['facility' => 'required'],['facility.required' => 'Specify/select facility where sample was collected from.']);
			$get_facility = $patient->nameWhere_sample_collected_from = $request->facility;

			$get_facility_district = Facility::where('facility','=',$get_facility)->get(['districtID']);
			$patient->facility_district = $get_facility_district[0]['attributes']['districtID'];



		}
		elseif(\Request::has('poe'))
		{
			$this->validate($request,['poe' => 'required'],	['poe.required' => 'Specify/select POE.']);
			$get_poe = $patient->nameWhere_sample_collected_from = $request->poe;

			$get_poe_district = Facility::where('facility','=',$get_poe)->get(['districtID']);
			$patient->facility_district = $get_poe_district[0]['attributes']['districtID'];

			$sd = District::where('id','=',$get_poe_district[0]['attributes']['districtID'])->get(['district']);
			$patient->swabing_district = $sd[0]['attributes']['district'];

		}
		elseif(\Request::has('quarantine'))
		{
			$patient->nameWhere_sample_collected_from = $request->quarantine;
			$this->validate($request,['quarantine' => 'required'],['quarantine.required' => 'Specify the Quarantine area/name where sample was collected from.',]);

			$this->validate($request,['q_swabing_district' => 'required'],['q_swabing_district.required' => 'Specify the district where quarantine is located',]);
			$sd = District::where('id','=',$request->q_swabing_district)->get(['district'])->toArray();
			$patient->swabing_district = $sd[0]['district'];

		}

		elseif(\Request::has('other')) {
			$patient->nameWhere_sample_collected_from = $request->other;
			$this->validate($request,['other' => 'required'],['other.required' => 'Specify the area/name where sample was collected from.',]);

			$this->validate($request,['swabing_district' => 'required'],['swabing_district.required' => 'Specify the district where sample was collected from',]);
			$sd = District::where('id','=',$request->swabing_district)->get(['district'])->toArray();
			$patient->swabing_district = $sd[0]['district'];;

		}
		else {
			//do nothing
		}

		$this->validate($request,[
		'specimen_type' => 'required',
		'patient_surname' => 'required',
		'where_sample_collected_from' => 'required',
		//'ulin' => 'required|unique:covid_patients',
		'who_being_tested' => 'required',
		'origin' => 'required',
		'data_entry_date' => 'required|date',
		'specimen_collection_date' => 'required|date',
		],
		[
		 'who_being_tested.required' => 'Please select reason for testing',
		'ulin.required' => ' Locator ID is required. Check if it has not yet been assigned.',
		//'ulin.unique' => ' Locator ID is already assigned to a sample.',
		'patient_surname.required' => ' Please enter the patient full names',
		'where_sample_collected_from.required' => 'Select the site of sample collection.',
		'specimen_type.required' => 'Select atleast 1 sample type',
		'specimen_collection_date.required' => ' Please select specimen collection date',
		'specimen_collection_date.date' => 'Invalid Date format',
		'data_entry_date.required' => ' Please select date of receipt at CPHL',
		'data_entry_date.date' => 'Invalid Date Format',
		'origin.required' => 'Please choose a nationality or if not provided, select left blank.',
		]);


		$data = $patient->toArray();
		// dd($data);

		if (\Request::has('id')){

			Covid::where('id',$data['patientID'])
			->update([
			"dataEntryDate" => $data['dataEntryDate'],
			"formType" => $data['formType'],
			"patient_surname" => $data['patient_surname'],
			"sex" => $data['sex'],
			"dob" => $data['dob'],
			"age" => $data['age'],
			"age_units" => $data['age_units'],
			"nationality" => $request['nationality'],
			"passportNo" => $data['passportNo'],
			"patient_contact" => $data['patient_contact'],
			"patient_village" => $data['patient_village'],
			"patient_parish" => $data['patient_parish'],
			"patient_subcounty" =>  $data['patient_subcounty'],
			"patient_district" => $data['patient_district'],
			"patient_NOK" => $data['patient_NOK'],
			"nok_contact" => $data['nok_contact'],

			"health_facility" => $data['health_facility'],
			"request_date" => $data['request_date'],
			"ulin" => $data['ulin'],
			"where_sample_collected_from" => $data['where_sample_collected_from'],
			"serial_number" => $data['serial_number'],
			"nameWhere_sample_collected_from" => $data['nameWhere_sample_collected_from'],
			"facility_district" => $data['facility_district'],
			"swabing_district" => $data['swabing_district'],
			"createdby" => $data['createdby'],
			"ref_lab" => $data['ref_lab']
			]);

		}
		else {
			$patient->save();
		}

		//now save the sample details
		$samples = [
		'patient_id' => array_key_exists('patientID', $data) ?  $data['patientID'] : $patient->id,
		'serial_number' => $request->serial_number,
		'specimen_type' => $request->specimen_type,
		'specimen_collection_date' => $request->specimen_collection_date,
		'specimen_ulin' => $request->ulin,
		'createdby' => Auth::user()->id,
		'dataEntryDate' => Carbon::today(),
		];

		CovidSamples::updateOrCreate(['specimen_ulin' => $request->ulin],$samples);

		return \Redirect::back()->with('msge','Successfully saved form with Barcode '.$patient->epidNo. ' & Locator ID '.$patient->ulin.' enter next form');

	}
}
