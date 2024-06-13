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
use App\Models\EbolaResult;
use App\Models\EvdPrintingLog;
use App\Models\DiseaseSymptoms;
use Illuminate\Http\Request;
use Session;
use Validator;
use Lang;
use Input;
use Carbon;
use Redirect;
use DB;
use Crypt;
use \Illuminate\Contracts\Encryption\DecryptException;
use Auth;
use App\Closet\MyHTML as MyHTML;


class EbolaController extends Controller {


	private function getCovidData(){
		$patients = Covid::get();
		$districts = MyHTML::array_merge_maintain_keys([''=>''],District::where('id', '>', '0')->pluck('district', 'id'));
		$facilities= MyHTML::array_merge_maintain_keys([''=>''], Facility::where('id', '>', '0')->pluck('facility', 'facility'));
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

		$and_cond ="";
		$district_cond = "";
		if(MyHTML::is_eoc() && \Auth::user()->id != '1451'){
			$and_cond .= " AND  result = 'Negative'";
		}
		if(MyHTML::is_district_user() || MyHTML::is_facility_dlfp_user() || MyHTML::is_rdt_site_user() || \Auth::user()->id == 3654 || \Auth::user()->id == 5026){
			$and_cond .= " AND  interviewer_district LIKE '%".trim(MyHTML::getUserDistrict())."%'";

			$site_details = MyHTML::DistrictOfCollection();
			$and_cond .= " OR (interviewer_district LIKE '%".trim($site_details['district_name'])."%' OR interviewer_district LIKE '%".trim($site_details['key_word'])."%' )";
		}

		if (MyHTML::isSpecialUser() || \Auth::user()->type == 1 || \Auth::user()->id == '4321') {
			$and_cond .= " AND is_classified = 0";
		}

		if(MyHTML::is_ec_user()){
			$and_cond .= " AND (patient_id  LIKE '%ELEC-COM%' OR case_id  LIKE '%electro-com%' OR interviewer_district  LIKE '%electro-com%' OR interviewer_district  LIKE '%ELEC-COM%' OR interviewer_district LIKE '%ELECTRAL COMMISSION%' OR interviewer_district LIKE '%electro-com%' OR interviewer_district LIKE '%ELEC-COM%')";
		}
		if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor()){;
			$site_details = MyHTML::getUserSiteOfCollection();
			$and_cond .= " AND (interviewer_facility LIKE '%".trim($site_details['facility_name'])."%' OR interviewer_facility LIKE '%".trim($site_details['key_word'])."%' OR ref_lab LIKE '%".trim($site_details['facility_name'])."%' OR ref_lab LIKE '%".trim($site_details['key_word'])."%')";
		}
		if(MyHTML::is_general_user()){
			$and_cond = " AND interviewer_district NOT LIKE '%STEC%' AND interviewer_facility NOT LIKE '%STEC%' AND interviewer_facility NOT LIKE '%Cabinet%' ";
		}

		if(MyHTML::is_ref_lab()){
			$site_details = MyHTML::getUserSiteOfCollection();
			//dd($site_details);
			$and_cond .= " AND  (ref_lab = ".\Auth::user()->ref_lab." OR interviewer_facility LIKE '%".trim($site_details['facility_name'])."%' OR interviewer_facility LIKE '%".trim($site_details['key_word'])."%')";
		}
		$is_printed = \Request::get("printed");
		if($is_printed == 0){
			$and_cond .= ' AND is_printed = 0 ';
		}
		if($and_cond != "" || $district_cond !="" || MyHTML::is_incident_commander() || MyHTML::is_case_manager() ||  MyHTML::is_regional_referral_director() || MyHTML::is_eoc()){
		$query_main = "SELECT * FROM ebola_results WHERE id > 0".$and_cond.$district_cond.' ORDER by test_date';

		}
		else {
		dd("You are not allowed to view EVD results");
		}
		$patients = \DB::select($query_main);
		$testing_summary = \DB::select("select interviewer_district, count(id) as total_tests, sum(case when result = 'positive' then 1 else 0 end) as positive_tests from ebola_results group by interviewer_district");

		return view("ebola.index", compact('patients','testing_summary'));
	}

	public function uploadEvdCSV(Request $request)
	{

		$fileName = $_FILES['import_file']['tmp_name'];

		if ($_FILES["import_file"]["size"] > 0) {
			$file = fopen($fileName, "r");
			fgetcsv($file);
			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {

			if(sizeof($column) <110){
				return response()->json("Result Upload declined!!! You are using the old CSV template. Download and use the new template instead.",409);
			}
				$lab_number_exists = EbolaResult::where('lab_number',$column[1])->exists();
				$case_id_exists = EbolaResult::where('case_id',$column[106])->exists();
				$date_now = new \DateTime();
				$file_date_format = 'd-m-Y';
				$file_dateTime_format = 'd-m-Y H:i:s';

				if($lab_number_exists){
					\Log::info("A result with this Lab Number '".$column[1]."' already exists in RDS");
					return response()->json("A result with this Lab Number '".$column[1]."' already exists in RDS. No results on this CSV was uploaded.",409);
				}

				if($case_id_exists){
					\Log::info("A result with this Case ID '".$column[106]."' already exists in RDS");
					return response()->json("A result with this Case ID '".$column[106]."' already exists in RDS. If this is a repeat test for this case, indicate with R1 for first repeat, R2, for second repeat R3 for third repeat.",409);
				}

				if($column[108] == ''){
					\Log::info("Name of the Testing Lab is required.");
					return response()->json("Name of the Testing Lab is required.",409);
				}


				if($column[86] == ''){
					\Log::info("Sample type is required.");
					return response()->json("Sample type is required.",409);
				}

				if($column[97] == ''){
					\Log::info("One of your entries have a missing test result.");
					return response()->json("One of your entries have a missing test result.",409);
				}

				if($column[98] == ''){
					\Log::info("One of your entries have a missing organism.");
					return response()->json("One of your entries have a missing organism.",409);
				}

				if($column[30] == ''){
					\Log::info("Symptoms are required. if there are not provided, Indicate with N/A.");
					return response()->json("Symptoms are required. if there are not provided, Indicate with N/A.",409);
				}
				if($column[93] == ''){
					\Log::info("Please provide the interviewer facility. this is where results will be sent to.");
					return response()->json("Please provide the interviewer facility. this is where results will be sent to.",409);
				}
				if($column[92] == ''){
					\Log::info("Please provide the interviewer district. this is where results will be sent to.");
					return response()->json("Please provide the interviewer district. this is where results will be sent to.",409);
				}

				// validate test dates
				$test_date_format_validator = \DateTime::createFromFormat($file_date_format, $column[99]);
				if($column[99] == ''){
					\Log::info("You have a missing test date in your submission.");
					return response()->json("You have a missing test date in your submission.",409);
				}

				if($column[99] != '' && $test_date_format_validator == false ){
					\Log::info("Wrong test date format: '".$column[99]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong test date format: '".$column[99]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}

				if($column[99] != '' && new \DateTime(date("Y-m-d", strtotime($column[99]))) > $date_now){
					\Log::info("You cannot have a future test date. '".$column[99]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future test date. '".$column[99]."' is greater than todays date. Correct this and try again",409);
				}

				// validate sample collection dates
				$sample_collection_date_format_validator = \DateTime::createFromFormat($file_date_format, $column[87]);
				if($column[87] == ''){
					\Log::info("You have a missing sample collection date in your submission.");
					return response()->json("You have a missing sample collection date in your submission.",409);
				}

				if($column[87] != '' && $test_date_format_validator == false ){
					\Log::info("Wrong sample collection date format: '".$column[87]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong sample collection date format: '".$column[87]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}

				if($column[87] != '' && new \DateTime(date("Y-m-d", strtotime($column[87]))) > $date_now){
					\Log::info("You cannot have a future sample collection date. '".$column[87]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future sample collection date. '".$column[87]."' is greater than todays date. Correct this and try again",409);
				}

				if(new \DateTime(date("Y-m-d", strtotime($column[87]))) > new \DateTime(date("Y-m-d", strtotime($column[99])))){
					\Log::info("The sample collection date'".$column[87]."' is greater than test date:$column[99]. Correct this and try again");
					return response()->json("The sample collection date'".$column[87]."' is greater than test date: $column[99]. Correct this and try again",409);
				}

				// validate case reporting date
				$report_date_validator = \DateTime::createFromFormat($file_date_format, $column[0]);
				if($column[0] != '' && $report_date_validator == false){
					\Log::info("Wrong date of report format: '".$column[0]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong date of report format: '".$column[0]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}

				if($column[0] == ''){
					\Log::info("You have a missing date of case reporting in your submission.");
					return response()->json("You have a missing date of case reporting in your submission.",409);
				}

				if(new \DateTime(date("Y-m-d", strtotime($column[87]))) > new \DateTime(date("Y-m-d", strtotime($column[0]))) || new \DateTime(date("Y-m-d", strtotime($column[99]))) < new \DateTime(date("Y-m-d", strtotime($column[0]))) || new \DateTime(date("Y-m-d", strtotime($column[0]))) > $date_now){
					\Log::info("Check and confirm that the date of case reporting is not earlier than sample collection date or greater than test date or having a future date in your CSV. Correct this and try again");
					return response()->json("Check and confirm that the date of case reporting is not earlier than sample collection date or greater than test date or having a future date  in your CSV. Correct this and try again",409);
				}

				// validate case death date
				$death_date_validator = \DateTime::createFromFormat($file_date_format, $column[11]);
				if($column[11] != '' && $death_date_validator == false){
					\Log::info("Wrong death date format.'".$column[11]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong death date format: '".$column[11]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}

				if($column[11] != '' && new \DateTime(date("Y-m-d", strtotime($column[11]))) > $date_now){
					\Log::info("You cannot have a future patient death date. '".$column[11]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future patient death date. '".$column[11]."' is greater than todays date. Correct this and try again",409);
				}
				//
				// 	// if all date formats are correct, check dates against test date
				// 	if ($test_date_format_validator != false && $report_date_validator != false && $death_date_validator != false ) {
				// 	//if test date is less than sample collection date, case report date,healer visit date, death date
				// 	if($column[99] != '' && date("Y-m-d", strtotime($column[99])) < date("Y-m-d", strtotime($column[87])) || date("Y-m-d", strtotime($column[99])) < date("Y-m-d", strtotime($column[81])) || date("Y-m-d", strtotime($column[99])) < date("Y-m-d", strtotime($column[11])) ){
				// 		\Log::info("The test date'".$column[99]."' cannot be earlier than any other dates. check all columns with dates to confirm that none is greater than .$column[99]. Correct this and try again");
				// 		return response()->json("The test date'".$column[99]."' cannot be earlier than any other dates. check all columns with dates to confirm that none is greater than .$column[99]. Correct this and try again",409);
				// 	}
				// }

				$resided_at_residence_from_validator = \DateTime::createFromFormat($file_date_format,$column[27]);
				if($column[27] != '' && $resided_at_residence_from_validator == false){
					\Log::info("Wrong residence from date format.'".$column[27]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong residence from date format: '".$column[27]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}

				if($column[27] != '' && new \DateTime(date("Y-m-d", strtotime($column[27]))) > $date_now){
					\Log::info("You cannot have a future residence from date. '".$column[27]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future residence from date. '".$column[27]."' is greater than todays date. Correct this and try again",409);
				}

				$resided_at_residence_to_validator = \DateTime::createFromFormat($file_date_format,$column[28]);
				if($column[28] != '' && $resided_at_residence_to_validator == false){
					\Log::info("Wrong residence to date format.'".$column[28]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong residence to date format: '".$column[28]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}

				if($column[28] != '' && new \DateTime(date("Y-m-d", strtotime($column[28]))) > $date_now){
					\Log::info("You cannot have a future residence to date. '".$column[28]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future residence to date. '".$column[28]."' is greater than todays date. Correct this and try again",409);
				}

				$symptom_onset_date_validator = \DateTime::createFromFormat($file_date_format,$column[29]);
				if($column[29] != '' && $symptom_onset_date_validator == false){
					\Log::info("Wrong Symptom on set date format.'".$column[29]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong Symptom on set date format: '".$column[29]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}
				if($column[29] != '' && new \DateTime(date("Y-m-d", strtotime($column[29]))) > $date_now){
					\Log::info("You cannot have a future Symptom on set date. '".$column[29]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future Symptom on set date. '".$column[29]."' is greater than todays date. Correct this and try again",409);
				}

				$hospital_admission_date_validator = \DateTime::createFromFormat($file_date_format,$column[38]);
				if($column[38] != '' && $hospital_admission_date_validator == false){
					\Log::info("Wrong hospital admission date format.'".$column[38]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong hospital admission date format: '".$column[38]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}
				if($column[38] != '' && new \DateTime(date("Y-m-d", strtotime($column[38]))) > $date_now){
					\Log::info("You cannot have a future Hospital admission date. '".$column[38]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future Hospital admission date. '".$column[38]."' is greater than todays date. Correct this and try again",409);
				}

				$date_isolated_validator = \DateTime::createFromFormat($file_date_format,$column[44]);
				if($column[44] != '' && $date_isolated_validator == false){
					\Log::info("Wrong isolation date format.'".$column[44]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong isolation date format: '".$column[44]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}
				if($column[44] != '' && new \DateTime(date("Y-m-d", strtotime($column[44]))) > $date_now){
					\Log::info("You cannot have a future isolation date. '".$column[44]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future isolation date. '".$column[44]."' is greater than todays date. Correct this and try again",409);
				}

				$date_of_exposure_validator = \DateTime::createFromFormat($file_date_format,$column[54]);
				if($column[54] != '' && $date_of_exposure_validator == false){
					\Log::info("Wrong death exposure format.'".$column[54]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong exposure date format: '".$column[54]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}

				if($column[54] != '' && new \DateTime(date("Y-m-d", strtotime($column[54]))) > $date_now){
					\Log::info("You cannot have a future exposure date. '".$column[54]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future exposure date. '".$column[54]."' is greater than todays date. Correct this and try again",409);
				}

				$contact_death_date_validator = \DateTime::createFromFormat($file_date_format,$column[58]);
				if($column[58] != '' && $contact_death_date_validator == false){
					\Log::info("Wrong patient contact death date format.'".$column[58]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong patient contact death date format: '".$column[58]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}
				if($column[58] != '' && new \DateTime(date("Y-m-d", strtotime($column[58]))) > $date_now){
					\Log::info("You cannot have a future contact date of death date . '".$column[58]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future contact date of death date. '".$column[58]."' is greater than todays date. Correct this and try again",409);
				}

				$healer_visit_date_validator = \DateTime::createFromFormat($file_date_format,$column[81]);
				if($column[81] != '' && $healer_visit_date_validator == false){
					\Log::info("Wrong healer visit date format.'".$column[81]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong healer visit date format: '".$column[81]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}
				if($column[81] != '' && new \DateTime(date("Y-m-d", strtotime($column[81]))) > $date_now){
					\Log::info("You cannot have a future healer visit date . '".$column[81]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future healer visit date. '".$column[81]."' is greater than todays date. Correct this and try again",409);
				}
				// ''=> date("Y-m-d", strtotime($column[106])),
				$approval_date_validator = \DateTime::createFromFormat($file_date_format,$column[103]);
				if($column[103] != '' && $approval_date_validator == false){
					\Log::info("Wrong approval date format.'".$column[103]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong approval date format: '".$column[103]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}
				if($column[103] != '' && new \DateTime(date("Y-m-d", strtotime($column[103]))) > $date_now){
					\Log::info("You cannot have a future approval date . '".$column[103]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future approval date. '".$column[103]."' is greater than todays date. Correct this and try again",409);
				}
				if($approval_date_validator == true && new \DateTime(date("Y-m-d", strtotime($column[103]))) < new \DateTime(date("Y-m-d", strtotime($column[99]))) || new \DateTime(date("Y-m-d", strtotime($column[103]))) < new \DateTime(date("Y-m-d", strtotime($column[87]))) ||  new \DateTime(date("Y-m-d", strtotime($column[103]))) < new \DateTime(date("Y-m-d", strtotime($column[105])))){
					\Log::info("One of your approval dates is less than either a test date,sample collection date,sample reception date or review date in your csv. '".$column[103]."' Correct this and try again");
					return response()->json("One of your approval dates is less than either a test date,sample collection date,sample reception date or review date in your csv. '".$column[103]."' Correct this and try again",409);
				}

				$review_date_validator = \DateTime::createFromFormat($file_date_format,$column[105]);
				if($column[105] != '' && $review_date_validator == false){
					\Log::info("Wrong review date format.'".$column[105]."'. Expected date format is d-m-y e.g ".date('d-m-Y'));
					return response()->json("Wrong review date format: '".$column[105]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y'),409);
				}
				if($review_date_validator == true && new \DateTime(date("Y-m-d", strtotime($column[105]))) > $date_now){
					\Log::info("You cannot have a future review date . '".$column[105]."' is greater than todays date. Correct this and try again");
					return response()->json("You cannot have a future review date. '".$column[105]."' is greater than todays date. Correct this and try again",409);
				}
				if($review_date_validator == true && new \DateTime(date("Y-m-d", strtotime($column[105]))) < new \DateTime(date("Y-m-d", strtotime($column[99]))) || new \DateTime(date("Y-m-d", strtotime($column[105]))) < new \DateTime(date("Y-m-d", strtotime($column[87]))) || new \DateTime(date("Y-m-d", strtotime($column[105]))) > new \DateTime(date("Y-m-d", strtotime($column[103])))){
					\Log::info("One of your review dates is less than either a test date,sample collection date,sample reception date or greater than an approval date in your csv. '".$column[105]."' Correct this and try again");
					return response()->json("One of your review dates is less than either a test date,sample collection date,sample reception date or greater than an approval date in your csv. '".$column[105]."' Correct this and try again",409);
				}

				$sample_reception_date_validator = \DateTime::createFromFormat($file_dateTime_format,$column[107]);
				if($column[107] != '' && $sample_reception_date_validator == false){
				  \Log::info("Wrong sample reception date format.'".$column[107]."'. Expected date format is d-m-y HH:MM:SS e.g ".date('d-m-Y H:i:s'));
				  return response()->json("Wrong sample reception date format: '".$column[107]."'. Expected date format is dd-mm-yyyy e.g: ".date('d-m-Y H:i:s'),409);
				}

				if($sample_reception_date_validator == true && new \DateTime(date("Y-m-d", strtotime($column[107]))) > $date_now){
				  \Log::info("You cannot have a future sample reception date . '".$column[107]."' is greater than todays date. Correct this and try again");
				  return response()->json("You cannot have a sample reception date. '".$column[107]."' is greater than todays date. Correct this and try again",409);
				}

				if($sample_reception_date_validator == true && new \DateTime(date("Y-m-d", strtotime($column[107]))) > new \DateTime(date("Y-m-d", strtotime($column[99]))) || new \DateTime(date("Y-m-d", strtotime($column[107]))) < new \DateTime(date("Y-m-d", strtotime($column[87]))) || new \DateTime(date("Y-m-d", strtotime($column[107]))) > new \DateTime(date("Y-m-d", strtotime($column[103]))) || new \DateTime(date("Y-m-d", strtotime($column[107]))) > new \DateTime(date("Y-m-d",strtotime($column[105])))){
				  \Log::info("One of your sample reception dates is either  less than a sample collection date or greater than a review date,approval date or test date in your csv. '".$column[107]."' Correct this and try again");
				  return response()->json("One of your sample reception dates is either less than a sample collection date or greater than a review date,approval date or test date in your csv. '".$column[107]."' Correct this and try again",409);
				}

				else {
					$data = EbolaResult::updateOrCreate([
					'case_id' => $column[106],
					'form_serial_number' => $column[2],
					],
					[
					'date_of_case_report' => date("Y-m-d", strtotime($column[0])),
					'patient_surname' => $column[3],
					'patient_firstname' => $column[4],
					'age' => $column[5],
					'age_units' => $column[6],
					'sex' => $column[7],
					'patient_phone_number' => $column[8],
					'phone_owner' => $column[9],
					'patient_status' => $column[10],
					'deathDate' =>  $column[11] == '' ? NULL : date("Y-m-d", strtotime($column[11])),
					'household_head' => $column[12],
					'patient_village' => $column[13],
					'patient_parish' => $column[14],
					'patient_subcounty' => $column[15],
					'patient_district' => $column[16],
					'country_of_residence' => $column[17],
					'patient_occupation' => $column[18],
					'type_of_business' => $column[19],
					'type_of_transporter' => $column[20],
					'health_worker_position' => $column[21],
					'health_worker_facility' => $column[22],
					'village_where_patient_fell_ill_from' => $column[23],
					'subcounty_where_patient_fell_ill_from' => $column[24],
					'district_where_patient_fell_ill_from' => $column[25],
					'patient_home_gps_coordinates' => $column[26],
					'resided_at_residence_from' =>  $column[27] == '' ? NULL : date("Y-m-d", strtotime($column[27])),
					'resided_at_residence_to' =>  $column[28] == '' ? NULL : date("Y-m-d", strtotime($column[28])),
					'symptom_onset_date' =>  $column[29] == '' ? NULL : date("Y-m-d", strtotime($column[29])),
					'symptoms' => $column[30],
					'temperature_reading' => $column[31],
					'source_of_temperature_reading' => $column[32],
					'has_unexplained_bleeding' => $column[33],
					'bleeding_symptoms' => $column[34],
					'other_hemorrhagic_symptoms' => $column[35],
					'other_nonhemorrhagic_symptoms' => $column[36],
					'is_patient_admitted' => $column[37],
					'hospital_admission_date' =>  $column[38] == '' ? NULL : date("Y-m-d", strtotime($column[38])),
					'facility_admitted_at' => $column[39],
					'facility_town' => $column[40],
					'facility_subcounty' => $column[41],
					'facility_district' => $column[42],
					'is_patient_isolated' => $column[43],
					'date_isolated' =>  $column[44] == '' ? NULL : date("Y-m-d", strtotime($column[44])),
					'patient_previously_hospitalized' => $column[45],
					'previous_hospitalization_date' =>  $column[46],
					'previously_hospitalized_at' => $column[47],
					'previous_village_of_hospitalization' => $column[48],
					'previous_district_of_hospitalization' => $column[49],
					'patient_isolated_at_previous_hospitalization' => $column[50],
					'did_patient_contact_known_suspect' => $column[51],
					'name_of_patient_contact' => $column[52],
					'patient_contact_relationship' => $column[53],
					'date_of_exposure' => $column[54] == '' ? NULL : date("Y-m-d", strtotime($column[54])),
					'village_of_contact' => $column[55],
					'district_of_contact' => $column[56],
					'status_of_contact' => $column[57],
					'contact_death_date' =>  $column[58] == '' ? NULL : date("Y-m-d", strtotime($column[58])),
					'contact_type' => $column[59],
					'did_patient_attend_funeral' => $column[60],
					'name_of_deceased' => $column[61],
					'deceased_relation_to_patient' => $column[62],
					'funeral_dates' => $column[63],
					'village_of_funeral' => $column[64],
					'district_of_funeral' => $column[65],
					'did_patient_participate' => $column[66],
					'did_patient_travel_outside_home' => $column[67],
					'village_traveled_to' => $column[68],
					'district_traveled_to' => $column[69],
					'dates_of_travel' => $column[70],
					'paid_visit_or_hospitalized_before_illness' => $column[71],
					'name_of_patient_visited' => $column[72],
					'patient_visit_dates' => $column[73],
					'facility_where_patient_visited' => $column[74],
					'village_where_patient_visited' => $column[75],
					'district_where_patient_visited' => $column[76],
					'patient_visited_healer' => $column[77],
					'name_of_healer' => $column[78],
					'village_of_healer' => $column[79],
					'district_of_healer' => $column[80],
					'healer_visit_date' => $column[81] == '' ? NULL : date("Y-m-d", strtotime($column[81])),
					'had_animal_contact' => $column[82],
					'type_of_animal' => $column[83],
					'animal_condition' => $column[84],
					'patient_bitten_by_tick' => $column[85],
					'sample_type' => $column[86],
					'sample_collection_date' =>  date("Y-m-d", strtotime($column[87])),
					'interviewer_name' => $column[88],
					'interviewer_phone' => $column[89],
					'interviewer_email' => $column[90],
					'interviewer_position' => $column[91],
					'interviewer_district' => $column[92],
					'interviewer_facility' => $column[93],
					'info_provided_by' => $column[94],
					'proxy_name' => $column[95],
					'proxy_relation_to_patient' => $column[96],
					'result' => $column[97],
					'organism' => $column[98],
					'test_date' =>  date("Y-m-d", strtotime($column[99])),
					'tested_by' => $column[100],
					'test_type' => $column[101],
					'results_approver' => $column[102],
					'approval_date'=> date("Y-m-d", strtotime($column[103])),
					'reviewed_by'=>$column[104],
					'review_date'=> date("Y-m-d", strtotime($column[105])),
					'lab_number'=>$column[1],
					'sample_reception_date'=> date("Y-m-d H:i:s", strtotime($column[107])),
					'testing_lab' => $column[108],
					'ct_value' => $column[109],
					'ref_lab' => Auth::user()->ref_lab,
					'original_file_name_used' => $_FILES['import_file']['name'],
					'uploaded_by' => Auth::user()->id,
					'is_classified' => 0,
					'is_printed' => 0
					]);
				}
			}
			return \Redirect::back()->with('message','Results succesfully uploaded');
		}
	}

	//loads the CIF Form
	public function cifEvd(){
		$districts = MyHTML::array_merge_maintain_keys([''=>''],District::where('id', '>', '0')->pluck('district', 'id'));
		$facilities= MyHTML::array_merge_maintain_keys([''=>''],Facility::where('id', '>', '0')->pluck('facility', 'facility'));
		$disease_symptoms = MyHTML::array_merge_maintain_keys(['' => ''], DiseaseSymptoms::where('disease_id',2)->pluck('symptoms','symptoms'));
		return view("ebola.cifevd", compact('facilities','districts','disease_symptoms'));
	}
	public function resultPdf()
	{
		$query = EbolaResult::where('ebola_results.id',\Request::get('id'))->leftJoin('users','ebola_results.uploaded_by','=','users.id')
		->get(['ebola_results.id','form_serial_number','case_id','date_of_case_report','patient_firstname','patient_surname','age','age_units','sex',
		'patient_occupation','symptoms','symptom_onset_date','patient_status','sample_type','sample_collection_date','interviewer_facility',
		'interviewer_district','result','organism','test_type','test_date','ct_value','sample_reception_date','testing_lab','tested_by','results_approver','approval_date','reviewed_by',
		'review_date','ebola_results.ref_lab','ebola_results.created_at','download_counter','is_printed','lab_number','family_name','other_name']);

		$data_arr = json_decode($query,true);

		$download_counter = $data_arr[0]['is_printed'] == 0 ? 1 : $data_arr[0]['download_counter'] +1;
		EbolaResult::where('id',$data_arr[0]['id'])->update(['is_printed' => 1,'download_counter' => $download_counter, 'printed_by' => \Auth::user()->id]);

		//save printing log
		$update_printing_log = new EvdPrintingLog;

		$update_printing_log->result_id = $data_arr[0]['id'];
		$update_printing_log->printed_by = \Auth::user()->id;
		$update_printing_log->print_date = date('Y-m-d H:i:s');
		$update_printing_log->save();

		$pdf = \PDF::loadView("ebola.ebv_result_pdf", compact("data_arr","download_counter"));
		return $pdf->download($data_arr[0]['case_id'].'.pdf');
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



	// downloads ALL evd data as CSV

	public function exportData(){
		$fro = \Request::get('exp_fro');
		$to = \Request::get('exp_to');
		$patients = [];
		if($fro != '' && $to != ''){
			$query = "SELECT * FROM ebola_results WHERE date(test_date) BETWEEN '".$fro."' AND '".$to."'";
			//Auth::user()->ref_lab;

			$patients = \DB::select($query);

			header('Content-Type: text/csv; charset=utf-8');
			header("Content-Disposition: attachment; filename=evd_data_$fro"."_$to.csv");
			$output = fopen('php://output', 'w');
			$headers = array(
			'DATE OF CASE REPORT',
			'MOH / UVRI LAB NUMBER',
			'FORM SERIAL NUMBER',
			'PATIENT SURNAME',
			'PATIENT FIRSTNAME',
			'AGE',
			'AGE UNITS (years/months)',
			'SEX (male / Female)',
			'PATIENT PHONE NUMBER',
			'PHONE OWNER',
			'PATIENT STATUS AT TIME OF REPORT',
			'DEATH DATE (if dead)',
			'HEAD OF HOUSEHOLD',
			'PATIENT PERMANENT VILLAGE / TOWN OF RESIDENCE',
			'PATIENT PARISH OF RESIDENCE',
			'PATIENT PERMANENT SUBCOUNTY OF RESIDENCE',
			'PATIENT PERMANENT DISTRICT OF RESIDENCE',
			'COUNTRY OF RESIDENCE',
			'PATIENT OCCUPATION',
			'TYPE OF BUSINESS (if business man /woman)',
			'TYPE OF TRANSPORTER (if occupation is transporter)',
			'HEALTH WORKER POSITION (if patient is health worker)',
			'HEALTH WORKER FACILITY (if patient is health worker)',
			'VILLAGE WHERE PATIENT FELL ILL FROM',
			'SUBCOUNTY WHERE PATIENT FELL ILL FROM',
			'DISTRICT WHERE PATIENT FELL ILL FROM',
			'PATIENT HOME GPS COORDINATES',
			'RESIDED AT RESIDENCE FROM DATE (if different from permanent residence)',
			'RESIDED AT RESIDENCE TO DATE (if different from permanent residence',
			'SYMPTOM ONSET DATE',
			'SYMPTOMS (separate symptoms by comma)',
			'TEMPERATURE_READING',
			'TEMPERATURE READING SOURCE',
			'HAS UNEXPLAINED BLEEDING (Yes/No)',
			'BLEEDING SYMPTOMS',
			'OTHER HEMORRHAGIC SYMPTOMS',
			'OTHER NONHEMORRHAGIC SYMPTOMS',
			'IS PATIENT CURRENTLY HOSPITALIZED OR ADMITTED? (Yes / No)',
			'HOSPITAL ADMISSION DATE (dd-mm-yyyy)',
			'NAME OF HEALTH FACILITY ADMITTED AT',
			'NAME OF TOWN WHERE THE HEALTH FACILITY IS LOCATED',
			'NAME OF SUBCOUNTY WHERE THE HEALTH FACILITY IS LOCATED',
			'NAME OF DISTRICT WHERE THE HEALTH FACILITY IS LOCATED AT',
			'IS PATIENT ISOLATED (Yes / No)',
			'DATE ISOLATED',
			'WAS THE PATIENT HOSPITALIZED OR DID HE/SHE VISIT A HEALTH CLINIC PREVIOUSLY FOR THIS ILLNESS',
			'PREVIOUS HOSPITALIZATION DATE',
			'PREVIOUSLY HOSPITALIZED AT (name of health facility)',
			'PREVIOUS VILLAGE OF HOSPITALIZATION',
			'PREVIOUS DISTRICT OF HOSPITALIZATION',
			'WAS THE PATIENT ISOLATED AT PREVIOUS HOSPITALIZATION (yes/no)',
			'DID PATIENT CONTACT KNOWN SUSPECT OR WITH ANY SICK PERSON BEFORE BECOMING ILL? (Yes / No / Unknown)',
			'NAME OF CONTACT',
			'RELATION TO PATIENT',
			'DATES OF EXPOSURE (Separate dates with a comma)',
			'VILLAGE OF CONTACT',
			'DISTRICT OF CONTACT',
			'WAS THE PERSON DEAD OR ALIVE? (Alive/Dead)',
			'CONTACT DEATH DATE (dd-mm-yyyy if contact died)',
			'CONTACT TYPE (1,2,3,4)',
			'DID PATIENT ATTEND A FUNERAL BEFORE BECOMING ILL? (yes/no/Unknown)',
			'NAME OF DECEASED(if patient attended funeral)',
			'RELATION OF DECEASED TO PATIENT',
			'DATES OF FUNERAL',
			'VILLAGE WHERE FUNERAL WAS',
			'DISTRICT WHERE FUNERAL WAS',
			'DID THE PATIENT PARTICIPATE? (carry or touch the body)',
			'DID PATIENT TRAVEL OUTSIDE HOME (Yes / No/ Unknown)',
			'VILLAGE TRAVELED TO',
			'DISTRICT TRAVELED TO',
			'DATES OF TRAVEL',
			'WAS THE PATIENT HOSPITALIZED OR DID HE/SHE GO TO A CLINIC OR VISIT ANYONE IN THE HOSP. BEFORE THIS ILLNESS',
			'NAME OF PATIENT VISITED',
			'DATES OF VISIT',
			'FACILITY WHERE PATIENT VISITED',
			'VILLAGE WHERE FACILITY IS LOCATED',
			'DISTRICT WHERE FACILITY IS LOCATED',
			'DID THE PATIENT CONSULT TRADITIONAL HEALER BEFORE BECOMING ILL? (Yes/No)',
			'NAME OF HEALER',
			'VILLAGE OF HEALER',
			'DISTRICT OF HEALER',
			'HEALER VISIT DATE',
			'DID PATIENT HAVE DIRECT ANIMAL CONTACT BEFORE BECOMING ILL?',
			'ANIMAL (Separate animals with comma. e.g Bats, Pigs, Primates)',
			'STATUS OF THE ANIMAL (separate status by comma e.g Healthy, Sick/Dead))',
			'PATIENT BITTEN BY TICK?',
			'SAMPLE TYPE',
			'SAMPLE COLLECTION DATE ',
			'INTERVIEWER NAME',
			'INTERVIEWER PHONE',
			'INTERVIEWER EMAIL',
			'INTERVIEWER POSITION',
			'INTERVIEWER DISTRICT',
			'INTERVIEWER FACILITY',
			'INFORMATION PROVIDED BY',
			'PROXY NAME (if information was provided by proxy)',
			'PROXY RELATION TO PATIENT',
			'TEST RESULT',
			'ORGANISM',
			'TEST DATE',
			'TESTED BY',
			'TEST TYPE',
			'APPROVED BY',
			'DATE APPROVED',
			'REVIEWED BY',
			'DATE REVIEWED',
			'CASE ID',
			'SAMPLE RECEPTION DATE',
			'UPLOAD DATE',
			'APPROVAL DATE',
			'REVIEW DATE',
			'TESTING LAB');

			fputcsv($output, $headers);
			foreach ($patients as $patient) {
				$row=array(

				$patient->date_of_case_report,
				$patient->lab_number,
				$patient->form_serial_number,
				$patient->patient_surname,
				$patient->patient_firstname,
				$patient->age,
				$patient->age_units,
				$patient->sex,
				$patient->patient_phone_number,
				$patient->phone_owner,
				$patient->patient_status,
				$patient->deathDate,
				$patient->household_head,
				$patient->patient_village,
				$patient->patient_parish,
				$patient->patient_subcounty,
				$patient->patient_district,
				$patient->country_of_residence,
				$patient->patient_occupation,
				$patient->type_of_business,
				$patient->type_of_transporter,
				$patient->health_worker_position,
				$patient->health_worker_facility,
				$patient->village_where_patient_fell_ill_from,
				$patient->subcounty_where_patient_fell_ill_from,
				$patient->district_where_patient_fell_ill_from,
				$patient->patient_home_gps_coordinates,
				$patient->resided_at_residence_from,
				$patient->resided_at_residence_to,
				$patient->symptom_onset_date,
				$patient->symptoms,
				$patient->temperature_reading,
				$patient->source_of_temperature_reading,
				$patient->has_unexplained_bleeding,
				$patient->bleeding_symptoms,
				$patient->other_hemorrhagic_symptoms,
				$patient->other_nonhemorrhagic_symptoms,
				$patient->is_patient_admitted,
				$patient->hospital_admission_date,
				$patient->facility_admitted_at,
				$patient->facility_town,
				$patient->facility_subcounty,
				$patient->facility_district,
				$patient->is_patient_isolated,
				$patient->date_isolated,
				$patient->patient_previously_hospitalized,
				$patient->previous_hospitalization_date,
				$patient->previously_hospitalized_at,
				$patient->previous_village_of_hospitalization,
				$patient->previous_district_of_hospitalization,
				$patient->patient_isolated_at_previous_hospitalization,
				$patient->did_patient_contact_known_suspect,
				$patient->name_of_patient_contact,
				$patient->patient_contact_relationship,
				$patient->date_of_exposure,
				$patient->village_of_contact,
				$patient->district_of_contact,
				$patient->status_of_contact,
				$patient->contact_death_date,
				$patient->contact_type,
				$patient->did_patient_attend_funeral,
				$patient->name_of_deceased,
				$patient->deceased_relation_to_patient,
				$patient->funeral_dates,
				$patient->village_of_funeral,
				$patient->district_of_funeral,
				$patient->did_patient_participate,
				$patient->did_patient_travel_outside_home,
				$patient->village_traveled_to,
				$patient->district_traveled_to,
				$patient->dates_of_travel,
				$patient->paid_visit_or_hospitalized_before_illness,
				$patient->name_of_patient_visited,
				$patient->patient_visit_dates,
				$patient->facility_where_patient_visited,
				$patient->village_where_patient_visited,
				$patient->district_where_patient_visited,
				$patient->patient_visited_healer,
				$patient->name_of_healer,
				$patient->village_of_healer,
				$patient->district_of_healer,
				$patient->healer_visit_date,
				$patient->had_animal_contact,
				$patient->type_of_animal,
				$patient->animal_condition,
				$patient->patient_bitten_by_tick,
				$patient->sample_type,
				$patient->sample_collection_date,
				$patient->interviewer_name,
				$patient->interviewer_phone,
				$patient->interviewer_email,
				$patient->interviewer_position,
				$patient->interviewer_district,
				$patient->interviewer_facility,
				$patient->info_provided_by,
				$patient->proxy_name,
				$patient->proxy_relation_to_patient,
				$patient->result,
				$patient->organism,
				$patient->test_date,
				$patient->tested_by,
				$patient->test_type,
				$patient->results_approver,
				$patient->approval_date,
				$patient->reviewed_by,
				$patient->review_date,
				$patient->case_id,
				$patient->sample_reception_date,
				$patient->created_at,
				$patient->approval_date,
				$patient->review_date,
				$patient->testing_lab,
				);

				fputcsv($output, $row);
			}
			fclose($output);
		}else{
			return view("ebola.export", compact('patients'));
		}

	}

	public function pendingPrinting(){
		$query = "select district, count(er.id) as number_of_results,ud.contacts from ebola_results er
    inner join districts d on er.interviewer_district LIKE CONCAT('%', er.interviewer_district, '%')
    inner join 
    (select district_id, GROUP_CONCAT(concat(family_name,' ', other_name,' ',telephone)) as contacts from users 
where district_id is not null and deactivated = 0 and (type = 23 or type = 39)
group by district_id ) as ud ON ud.district_id = d.id

    where d.district like concat('%', er.interviewer_district,'%') and er.is_printed = 0 group by district";
    $results_pending_printing = \DB::select($query);
    return view("ebola.pending_printing", compact('results_pending_printing'));
	}

	public function validateQrCode($result_id){

			try{

				$decrypted_id = Crypt::decrypt($result_id);
				//$decrypted_id = $result_id;
				//dd($decrypted_id);
				//don't show results without receipt number984
				$data = EbolaResult::where('id', $decrypted_id)->get()->first()->toArray();

				//dd($data);
				$test_date = $data['test_date'];
				$today = date('Y-m-d');
				$datetime1 = strtotime($test_date); // convert to timestamps
				$datetime2 = strtotime($today); // convert to timestamps
				$days = (int)(($datetime2 - $datetime1)/86400); // will give the difference in days , 86400 is the timestamp difference of a day

				$pdf = \PDF::loadView('ebola.validate', compact('data', 'days'));
				//dd($pdf);
				return $pdf->stream('Eboloa_result.pdf');


			}catch(DecryptException $e){
				echo "<script>alert('UNKNOWN RESULT: Your QR-Code scan did not match any record');</script>";

			}
		}
}
