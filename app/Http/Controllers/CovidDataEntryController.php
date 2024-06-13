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
use DB;
use Auth;
use App\Closet\MyHTML as MyHTML;

class CovidDataEntryController extends Controller {


	private function getCovidData(){
		$patients = Covid::get();
		$districts = MyHTML::array_merge_maintain_keys([''=>''], District::where('id', '>', '0')->pluck('district', 'id'));
		$facilities= MyHTML::array_merge_maintain_keys([''=>''],Facility::where('id', '>', '0')->pluck('facility', 'facility'));
		$nationality = MyHTML::array_merge_maintain_keys([''=>''],DB::table('nationalities')->pluck('nationality','nationality'));
		$poe = MyHTML::array_merge_maintain_keys([''=>''],Facility::where('facilityLevelID', '=', '14')->pluck('facility','facility'));

		return [
			'poe'=>$poe,
			'patients'=>$patients,
			'districts'=>$districts,
			'facilities'=>$facilities,
			'nationality'=>$nationality,
		];
	}

	public function index(){
		$n = DB::table('covid_patients')->whereDate('covid_patients.created_at','=', Carbon::today())
		->leftJoin('covid_samples', 'covid_patients.id', '=', 'covid_samples.patient_id')
		->get();
		$patients = Covid::get();

		return view("covid.index", compact('patients'));
	}

	public function listSuspects(){
		$covidData=$this->getCovidData();
		$patients = $covidData['patients'];
		dd('This page is nolonger in use');
		return view("covid.suspects", compact('patients'));
	}

	//loads the CIF Form
	public function cifForm(){
		$covidData=$this->getCovidData();
		$facilities = $covidData['facilities'];
		$poe = $covidData['poe'];
		$nationality = $covidData['nationality'];
		$districts = $covidData['districts'];
		return view("covid.cifForm", compact('facilities','poe','nationality','districts'));
	}

	//loads the LIF Form
	public function lifForm(){
		$covidData=$this->getCovidData();
		$facilities = $covidData['facilities'];
		$poe = $covidData['poe'];
		$nationality = $covidData['nationality'];
		$districts = $covidData['districts'];
		$m = MyHTML::array_merge_maintain_keys([''=>''],WorksheetSample::where('assigned_sample', '=', 0)->pluck('locator_id','locator_id'));

		return view("covid.lifForm", compact('facilities','poe','nationality','districts','m'));
	}

	//loads the Point of entry Form
	public function poeForm(){
		$covidData=$this->getCovidData();
		$facilities = $covidData['facilities'];
		$poe = $covidData['poe'];
		$nationality = $covidData['nationality'];
		$districts = $covidData['districts'];
		return view("covid.poeForm", compact('facilities','poe','nationality','districts'));
	}

	public function editForm($id){

		$patient = Covid::findOrFail($id);
		$covidData=$this->getCovidData();

		$f = $patient['attributes']['formType'];
		$pID = $patient['attributes']['id'];
		$sample = CovidSamples::where('patient_id','=',$pID)->get();

		if($f == "cif")

		{
			$facilities = $covidData['facilities'];
			$poe = $covidData['poe'];
			$districts = $covidData['districts'];
			$nationality = $covidData['nationality'];


			return view("covid.cifEdit", compact('facilities','poe','nationality','districts','patient','sample'));
		}

		elseif ($f == "lif") {
			$facilities = $covidData['facilities'];
			$poe = $covidData['poe'];
			$districts = $covidData['districts'];
			$nationality = $covidData['nationality'];
			return view("covid.poeEdit", compact('facilities','poe','districts','patient','sample','nationality'));
		}
		else {
			$facilities = $covidData['facilities'];
			$districts = $covidData['districts'];
			return view("covid.poeEdit", compact('facilities','districts','patient','sample'));
		}

	}

	public function getLabData(){

		$n = Covid::whereDate('covid_patients.created_at','=', Carbon::today())->leftJoin('covid_samples', 'covid_patients.id', '=', 'covid_samples.patient_id')->orderBy('specimen_ulin','DESC')->get();

		return Datatables::of($n)->addColumn('action', function($row) {
			return '<a href="' . route("poeForm", $row->id) . '" class="btn btn-sm btn-link pull-left"><i class="fa fa-edit"></i>Update</a>';
		})->make(true);
	}

	public function getSuspectData(){
		$all_req = \Request::all();
		//\Log::info($all_req);
		$n = Covid::groupBy('covid_patients.epidNo')->leftJoin('covid_results','covid_patients.id','=','covid_results.patient_id')->leftJoin('covid_samples', 'covid_patients.id', '=', 'covid_samples.patient_id')
		->select('covid_patients.id','covid_patients.epidNo','patient_surname','patient_firstname','sex','age','nationality','patient_contact',
		'where_sample_collected_from','covid_patients.request_date','covid_results.test_result','covid_results.test_date','covid_samples.specimen_ulin')->get();

		return Datatables::of($n)->addColumn('action', function($row) {
			\Log::info($row);
			$link = '<a href="' . route("editForm", $row->id) . '" class="btn btn-sm btn-link pull-left"><i class="fa fa-edit"></i>Update</a>';
			if(empty($row->test_result)){
				$link .= '<br><a href="' . route("enter_results", $row->id) . '" class="btn btn-sm btn-link pull-left"><i class="fa fa-edit"></i>Enter Results</a>';
			}
			return $link;
		})->make(true);
	}

	public function store(Request $request){

	$interviewer = new Interviewer;

	$interviewer->interviewer_name = $request->interviewer_name;
	$interviewer->interviewer_phone = $request->interviewer_phone;
	$interviewer->interviewer_facility = $request->interviewer_facility;
	$interviewer->interviewer_email = $request->interviewer_email;
	$interviewer->save();

	$patient = new Covid;

	$patient->dataEntryDate = Carbon::today();
	$patient->formType = $request->formType;
	$patient->patient_surname = $request->patient_surname;
	$patient->patient_firstname = $request->patient_firstname;
	$patient->sex = $request->sex;
	$patient->dob = $request->dob !="" ? $request->dob : "0000-00-00 00:00:00";
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
	$patient->epidNo = $request->epidNo != "" ? $request->epidNO : $request->ulin;
	$patient->caseID = $request->caseID != "" ? $request->caseID : $request->ulin;
	$patient->truckNo = $request->truckNo;
	$patient->truckDestination = $request->truckDestination;
	$patient->truckEntryDate = $request->truckEntryDate;
	$patient->tempReading = $request->tempReading;
	$patient->sampleCollected = $request->sampleCollected;
	$patient->pointOfEntry = $request->pointOfEntry;
	$patient->health_facility = $request->health_facility;
	$patient->request_date = $request->request_date;
	$patient->ulin = $request->ulin;


	//start here
	$request->origin == "UGANDAN" ? $patient->nationality = "UGANDAN" : $patient->nationality = $request->foreign_nationality;

	if(\Request::has("patient_district"))
	{
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
		$get_facility = $patient->nameWhere_sample_collected_from = $request->facility;

		$get_facility_district = Facility::where('facility','=',$get_facility)->get(['districtID']);
		$patient->facility_district = $get_facility_district[0]['attributes']['districtID'];
	}
	elseif(\Request::has('poe'))
	{
		$get_poe = $patient->nameWhere_sample_collected_from = $request->poe;
		$get_poe_district = Facility::where('facility','=',$get_poe)->get(['districtID']);
		$patient->facility_district = $get_poe_district[0]['attributes']['districtID'];
	}
	elseif(\Request::has('quarantine'))
	{
		$patient->nameWhere_sample_collected_from = $request->quarantine;
	}
	else {
		$patient->nameWhere_sample_collected_from = $request->other;
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

	if(\Request::has('symptoms')){
		$a = $request->input('symptoms');
		$dt =[];
		foreach($a as $k){
			$dt[] = $k;
		}
		if(trim($request->otherSymp) != ''){
			$dt[] = $request->otherSymp;
		}
		$sk = implode(",",$dt);
		$patient->symptoms = $sk ;
	}

	if(\Request::has('known_underlying_condition')){
		$a = $request->input('known_underlying_condition');
		$dt =[];
		foreach($a as $k){
			$dt[] = $k;
		}
		if(trim($request->otherUnderlyingCondition) != ''){
			$dt[] = $request->otherUnderlyingCondition;
		}
		$sk = implode(",",$dt);
		$patient->symptoms = $sk ;
	}

	$patient->TravelToChina = $request->TravelToChina;
	$patient->travelDateToChina = $request->travelDateToChina;
	$patient->travelDateToChina = $request->travelDateToChina != "" ? $request->travelDateToChina : "0000-00-00 00:00:00";
	$patient->travelDateFromChina = $request->travelDateFromChina != "" ? $request->travelDateFromChina : "0000-00-00 00:00:00" ;
	$patient->stateVisited = $request->stateVisited;
	$patient->UgArrivalDate = $request->UgArrivalDate != "" ? $request->UgArrivalDate : "0000-00-00 00:00:00";
	$patient->closeContact4 = $request->closeContact4;
	$patient->closeContact5 = $request->closeContact5;
	$patient->healthFacilityHistory = $request->healthFacilityHistory;
	$patient->acuteRespiratory = $request->acuteRespiratory;

	if(\Request::has('additionalSigns')){
		$a = $request->input('additionalSigns');
		$dt =[];
		foreach($a as $k){
			$dt[] = $k;
		}
		if(trim($request->otherAdditionalSign) != ''){
			$dt[] = $request->otherAdditionalSign;
		}
		$sk = implode(",",$dt);
		$patient->additionalSigns = $sk;
	}
	if(\Request::has('diagnosis')){
		$a = $request->input('diagnosis');
		$dt =[];
		foreach($a as $k){
			$dt[] = $k;
		}
		$sk = implode(",",$dt);
		$patient->diagnosis = $sk;
	}
	if(\Request::has('comorbid')){
		$cc = $request->input('comorbid'); $ns = implode(',', $cc);
		$dt =[];
		foreach($cc as $k){
			$dt[] = $k;
		}
		$sk = implode(",",$dt);
		$patient->comorbid = $sk;
	}

	$patient->admitted = $request->admitted;
	$patient->admissionDate = $request->admissionDate;
	$patient->icuAdmitted = $request->icuAdmitted;
	$patient->intubated = $request->intubated;
	$patient->ecmo = $request->ecmo;
	$patient->patientDied = $request->patientDied;
	$patient->otherEtiology = $request->otherEtiology !="0" ? $request->otherEtiology : $request->otherEti;
	$patient->interviewer_name = $request->interviewer_name;
	$patient->interviewer_facility = $request->interviewer_facility;
	$patient->interviewer_phone = $request->interviewer_phone;
	$patient->interviewer_email = $request->interviewer_email;
	$patient->typeOfSite = $request->typeOfSite;
	$patient->createdby = Auth::user()->id;
	$patient->ref_lab = Auth::user()->ref_lab;

	if($request->where_sample_collected_from == 'HEALTH FACILITY') {
		$this->validate($request,['facility' => 'required'],['facility.required' => 'Specify/select facility where sample was collected from.']);
	}

	elseif($request->where_sample_collected_from == 'POE') {
		$this->validate($request,['poe' => 'required'],	['poe.required' => 'Specify/select POE.']);
	}
	elseif($request->where_sample_collected_from == 'QURANTINE') {

		$this->validate($request,['quarantine' => 'required'],['quarantine.required' => 'Specify the Quarantine area/name where sample was collected from.',]);
	}
	elseif($request->formType == 'lif') {

		$this->validate($request,[
		'request_date' => 'required',
		'specimen_type' => 'required',
		'patient_surname' => 'required',
		'patient_surname' => 'required',
		'patient_firstname' => 'required',
		'interviewer_name' => 'required',
		'where_sample_collected_from' => 'required',
		'epidNo' => 'required',
		'nationality' => 'required',
		'quarantine' => 'required',
		],
		[
		'epidNo.required' => ' Please enter the Locator ID.',
		'request_date.required' => ' Plesae enter registration date.',
		'patient_surname.required' => ' Please enter the patient sur-name.',
		'patient_firstname.required' => 'Please enter the patient first name',
		'where_sample_collected_from.required' => 'Select the site of sample collection.',
		'quarantine.required' => 'Specify the Quarantine area/name.',
		'specimen_type.required' => 'Select atleast 1 sample type',
		'interviewer_name.required' => 'Enter interviewer name.',
		'nationality.required' => 'Please choose a nationality or if not provided, select left blank.',

		]);
	}

	else {

		$this->validate($request,[
		'request_date' => 'required',
		'specimen_type' => 'required',
		'specimen_collection_date' => 'required',
		'patient_surname' => 'required',
		'interviewer_name' => 'required',
		'where_sample_collected_from' => 'required',
		'other' => 'required',
		],
		[
		'epidNo.required' => ' Please enter the Batch Number.',
		'request_date.required' => ' Plesae enter registration date.',
		'patient_surname.required' => ' Please enter the patient names.',
		'where_sample_collected_from.required' => 'Select the site of sample collection.',
		'other.required' => 'Specify name of place where sample was collected from.',
		'specimen_type.required' => 'Select atleast 1 sample type',
		'specimen_collection_date.required' => 'Provide the specimen collection date',
		'interviewer_name.required' => 'Enter interviewer name.',

		]);
	}
	$patient->save();

	$w = Covid::get();
	foreach ($w as $key => $v) {
		$pID = $v->id;
		$ep = $v->epidNo;
	}
	$points = new CollectionPoints;
	$points->patient_id = $pID;
	$points->health_facility = $request->health_facility;
	$points->facility_sub_district = $request->facility_sub_district;
	$points->facility_district = $request->facility_district;
	$points->pointOfEntry = $request->pointOfEntry;
	$points->dataEntryDate = Carbon::today();
	$points->save();

	$s = $request->specimen_type;
	$sam = $request->specimen_collection_date;
	if(Auth::user()->ref_lab == 2891)
	{
		$pecimen_ulin = $ep;
	}
	else
	{
		$pecimen_ulin = $request->specimen_ulin;
	}

	$ri = $request->sentToUvri;
	$xy = $request->specimen_collection_date;

	$no_of_specimen = count($s);
	if($no_of_specimen){
		for($i=0; $i < $no_of_specimen; $i++){
			$samples = new CovidSamples;
			$samples->patient_id = $pID;
			if(\Request::has('sample_collection_date'))
			{
				$samples->specimen_collection_date = $request->sample_collection_date;
			}
			else{
				$samples->specimen_collection_date = $sam[$i];
			}
			$samples->specimen_type = $s[$i];

			if(\Request::has('sp_ulin')){
				$samples->specimen_ulin = $request->sp_ulin;
			}else{
				$samples->specimen_ulin = $pecimen_ulin;
			}

			$samples->sample_referred_to = $ri[$i];
			$samples->serial_number = $request->serial_number;
			$samples->testing_lab = Auth::user()->ref_lab;
			$samples->is_accessioned = 1;
			// $samples->dataEntryDate = Carbon::today();
			$samples->createdby = Auth::user()->id;

			$samples->save();
		}
	}

	return redirect()->back()->with('msge','successfully saved,enter next client data');

}

	public function updateForm($id){

		$patient = Covid::findOrFail($id);
		$pID = $patient['attributes']['id'];
		$sample = CovidSamples::where('patient_id','=',$pID)->get();

		$patient->dataEntryDate = Carbon::today();
		$patient->formType = Input::get('formType');

		$patient_firstname = Input::get('patient_firstname');
		$patient->patient_surname = Input::get('patient_surname');

		$x = Input::get('sex');
		$patient->dob = Input::get('dob') !="" ? Input::get('dob')  : "0000-00-00 00:00:00";
		$patient->age = Input::get('age');
		$patient->age_units = Input::get('age_units');
		$patient->nationality = Input::get('nationality');
		$patient->passportNo = Input::get('passportNo');
		$patient->patient_contact = Input::get('patient_contact');
		$patient->patient_village = Input::get('patient_village');
		$patient->patient_parish = Input::get('patient_parish');
		$patient->patient_subcounty = Input::get('patient_subcounty');
		$patient->patient_district = Input::get('patient_district');
		$patient->patient_NOK = Input::get('patient_NOK');
		$patient->nok_contact = Input::get('nok_contact');
		$patient->epidNo = Input::get('epidNo');
		$patient->caseID = Input::get('caseID');
		$patient->truckNo = Input::get('truckNo');
		$patient->truckDestination = Input::get('truckDestination');
		$patient->truckEntryDate = Input::get('truckEntryDate');
		$patient->tempReading = Input::get('tempReading');
		$patient->sampleCollected = Input::get('sampleCollected');
		$patient->pointOfEntry = Input::get('pointOfEntry');
		$patient->health_facility = Input::get('health_facility');
		$patient->request_date = Input::get('request_date');
		$patient->ulin = Input::get('ulin');
		$patient->serial_number = Input::get('serial_number');
		$patient->where_sample_collected_from = Input::get('where_sample_collected_from');
		//$patient->nameWhere_sample_collected_from = Input::get('nameWhere_sample_collected_from');
		$patient->nameWhere_sample_collected_from = Input::get('pointOfEntry');
		$patient->who_being_tested = Input::get('who_being_tested');
		$patient->is_health_care_worker_being_tested = Input::get('is_health_care_worker_being_tested');
		$patient->health_care_worker_facility = Input::get('health_care_worker_facility');
		$patient->reason_for_healthWorker_testing = Input::get('reason_for_healthWorker_testing') != "Other" ? Input::get('reason_for_healthWorker_testing') : Input::get('reason_for_healthWorker_testingOther');
		$patient->isolatedPerson_test_day = Input::get('isolatedPerson_test_day') != "Other" ? Input::get('isolatedPerson_test_day') : Input::get('isolatedPerson_test_dayOther');
		$patient->travel_out_of_ug_b4_onset = Input::get('travel_out_of_ug_b4_onset');
		$patient->destination_b4_onset = Input::get('destination_b4_onset');
		$patient->return_date = Input::get('return_date');
		$patient->patient_symptomatic = Input::get('patient_symptomatic');
		$patient->symptomatic_onset_date = Input::get('symptomatic_onset_date');

		if(Input::get('symptoms')){
			$a = Input::get('symptoms');
			$dt =[];
			foreach($a as $k){
				$dt[] = $k;
			}
			if(trim(Input::get('otherSymp')) != ''){
				$dt[] = Input::get('otherSymp');
			}
			$sk = implode(",",$dt);
			$patient->symptoms = $sk ;
		}

		if(\Request::has('known_underlying_condition')){
			$a = Input::get('known_underlying_condition');

			$dt =[];
			foreach($a as $k){
				$dt[] = $k;
			}
			if(trim(Input::get('otherUnderlyingCondition')) != ''){
				$dt[] = Input::get('otherUnderlyingCondition');
			}

			$sk = implode(",",$dt);
			$patient->symptoms = $sk ;
		}

		$patient->TravelToChina = Input::get('TravelToChina');
		$patient->travelDateToChina = Input::get('travelDateToChina');
		$patient->travelDateToChina = Input::get('travelDateToChina') != "" ? Input::get('travelDateToChina') : "0000-00-00 00:00:00";
		$patient->travelDateFromChina = Input::get('travelDateFromChina') != "" ? Input::get('travelDateFromChina') : "0000-00-00 00:00:00" ;
		$patient->stateVisited = Input::get('stateVisited');
		$patient->UgArrivalDate = Input::get('UgArrivalDate') != "" ? Input::get('UgArrivalDate') : "0000-00-00 00:00:00";
		$patient->closeContact4 = Input::get('closeContact4');
		$patient->closeContact5 = Input::get('closeContact5');
		$patient->healthFacilityHistory = Input::get('healthFacilityHistory');
		$patient->acuteRespiratory = Input::get('acuteRespiratory');

		// $a = Input::get('additionalSigns');
		if(\Request::has('additionalSigns')){
			$dt =[];
			foreach($a as $k){
				$dt[] = $k;
			}
			if(trim($request->otherAdditionalSign) != ''){
				$dt[] = Input::get('otherAdditionalSign');
			}
			$sk = implode(",",$dt);
			$patient->additionalSigns = $sk;
		}

		if(\Request::has('diagnosis')){

			$a = $request->input('diagnosis');
			$dt =[];
			foreach($a as $k){
				$dt[] = $k;
			}
			$sk = implode(",",$dt);
			$patient->diagnosis = $sk;
		}

		if(\Request::has('comorbid')){
			$cc = Input::get('comorbid'); $ns = implode(',', $cc);
			$dt =[];
			foreach($cc as $k){
				$dt[] = $k;

			}
			$sk = implode(",",$dt);
			$patient->comorbid = $sk;
		}

		$patient->admitted = Input::get('admitted');
		$patient->admissionDate = Input::get('admissionDate');
		$patient->icuAdmitted = Input::get('icuAdmitted');
		$patient->intubated = Input::get('intubated');
		$patient->ecmo = Input::get('ecmo');
		$patient->patientDied = Input::get('patientDied');
		// $patient->deathDate = $request->deathDate;
		$patient->otherEtiology = Input::get('otherEtiology') !="0" ? Input::get('otherEtiology') : Input::get('otherEti');
		$patient->interviewer_name = Input::get('interviewer_name');
		$patient->interviewer_facility = Input::get('interviewer_facility');
		$patient->interviewer_phone = Input::get('interviewer_phone');
		$patient->interviewer_email = Input::get('interviewer_email');
		$patient->ref_lab = Auth::user()->ref_lab;

		$patient->update();

		//prepare data to create or update a sample for the patient
		$f = [
		'patient_id' => $patient->id,
		'caseID' => Input::get('caseID'),
		'specimen_ulin' => Input::get('specimen_ulin'),
		'specimen_collection_date' => Input::get('request_date'),
		'sampleCollected' => Input::get('sampleCollected'),
		'test_result' => Input::get('test_result'),
		'test_date' => Input::get('result_date'),
		'result_type' => "Final",
		'test_type' => Input::get('test_type'),
		'tested_by' => Input::get('tested_by'),
		];

		CovidSamples::updateOrCreate(['patient_id' => $pID],$f);

		return redirect('suspected/cases')->with('msge',trans('general.save_success'));
	}

	public function getCovidCsv(){

		$fro = \Request::get('test_date_fro');
		$to = \Request::get('test_date_to');
		$test_performed = 'SARS-CoV-2';
		$approver_sign = 'PimunduGodfrey';
		$approved_by = 'Pimundu Godfrey';
		$ref_lab = Auth::user()->ref_lab;

		$query = "SELECT p.epidNo,p.patient_surname,p.patient_firstname,p.age,p.age_units,p.sex,p.patient_contact,p.nationality,
		p.where_sample_collected_from,p.nameWhere_sample_collected_from,s.specimen_collection_date, s.specimen_type,s.test_type, p.request_date, p.serial_number,p.foreignDistrict,pd.district as patientDistrict, p.dataEntryDate,
		fd.district as facilityDistrict
		FROM covid_patients as p
		LEFT JOIN covid_samples s ON(s.patient_id = p.id)
		LEFT JOIN districts pd ON (pd.id = p.patient_district)
		LEFT JOIN districts fd ON (fd.id = p.facility_district)
		where s.in_lims = 0  AND s.test_type = 'pcr' AND p.created_at between '$fro' and '$to'";
		$patients = \DB::select($query);

		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=Covid_data_$fro"."_$to.csv");
		$output = fopen('php://output', 'w');
		$headers = array(
		'SAMPLE ID/BARCODE',
		'PATIENT NAME',
		'SEX',
		'AGE',
		'NATIONALITY',
		'SAMPLE TYPE',
		'COLLECTION DATE',
		'SWAB FACILITY',
		'SWAB DISTRICT',
		'DATE RECEIVED IN LAB',
		'TESTING LAB ID',
		'TEST RESULT',
		'TEST DATE',
		'TESTED BY',
		'TEST TYPE',
		);

		fputcsv($output, $headers);
		foreach ($patients as $patient) {
			$row=array(
			$patient->epidNo,
			$patient->patient_surname ? $patient->patient_surname.' ' .$patient->patient_firstname : '',
			$patient->sex,
			$patient->age,
			$patient->nationality,
			$patient->specimen_type,
			date("Y-m-d", strtotime($patient->specimen_collection_date)),
			$patient->nameWhere_sample_collected_from,
			$patient->facilityDistrict
			);
			fputcsv($output, $row);
		}
		fclose($output);
	}

	// downloads ALL covid data as CSV

	public function getAllCovidCsv(){
		$fro = \Request::get('test_date_f');
		$to = \Request::get('test_date_t');

		$query = "SELECT s.request_date,p.health_facility,p.pointOfEntry,p.epidNo,p.patient_surname,p.patient_firstname,p.patient_contact,p.symptoms,p.symptomatic_onset_date,p.patient_village,
		p.patient_subcounty,p.TravelToChina,p.UgArrivalDate,p.stateVisited
		FROM covid_patients as p
		LEFT JOIN covid_samples s ON(s.patient_id = p.id) WHERE p.ref_lab = ".Auth::user()->ref_lab;
		$patients = \DB::select($query);

		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=Covid_data_$fro"."_$to.csv");
		$output = fopen('php://output', 'w');
		$headers = array(
		'DateSample collected',
		'Name SentSite',
		'Name SentSiteSP',
		'PatientID',
		'NameFamily',
		'AgeYears',
		'AgeMonths',
		'Sex',
		'Case contact (Telephone)',
		'Symptom (Yes/No)',
		'Date of Onset',
		'Village',
		'Subcounty',
		'Historyoftravel',
		'IfYescountriestraveledto',
		'Date of Arrival',
		'Additionalcommentsfortravel',
		'PCR Results',
		'Date of Testing',
		'Followup PCR Result (10 days)',
		'Date Follow up sample tested',
		'Followup PCR Result(14 Days)',
		'Date Follow up sample tested',
		'Followup PCR Result',
		'Date followup sample tested'
		);

		fputcsv($output, $headers);
		foreach ($patients as $patient) {
			$row=array(

			$patient->request_date,
			$patient->health_facility,
			$patient->pointOfEntry,
			$patient->epidNo,
			$patient->patient_surname.''.$patient->patient_firstname,
			$patient->age,
			$patient->age,
			$patient->sex,
			$patient->patient_contact,
			$patient->symptoms,
			$patient->symptomatic_onset_date,
			$patient->patient_village,
			$patient->patient_subcounty,
			$patient->TravelToChina,
			$patient->stateVisited,
			$patient->UgArrivalDate,
			$patient->stateVisited
			);

			fputcsv($output, $row);
		}
		fclose($output);

	}

	public function sendResults(){
		$results = Covid::where('caseID','!=','')->get(['id','caseID','epidNo','test_type','result_type','test_result']);
		return $results;
	}

	public function getResults(Request $request){
		$client = new Client();
		$api_response = $client->get('https://api.envato.com/v1/discovery/search/search/item?site=themeforest.net&category=wordpress&sort_by=relevance&access_token=TOKEN');
		$response = json_decode($api_response);
	}

	public function saveResults($id)
	{
		$rules = [
		'test_result' => 'required',
		];
		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput(Input::except('password'));
		}
		else {

			$result = Covid::findOrFail($id);
			$result->$test_type = "PCR";
			$result->$result_type = "Final";
			$result->test_result = Input::get('test_result');

			try{
				$result->save();
				return Redirect::route('covid')
				->with('message', trans('messages.success-dispatch'))->with('facilityrequest', $result->id);
			}catch(QueryException $e){
				Log::error($e);
			}
		}
	}
}
