<?php namespace App\Http\Controllers;

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
use App\Closet\MyHTML as MyHTML;

class CaseManagementController extends Controller {

	public function index(){
		$samples = \Request::get("samples");
		$type = \Request::get("type");
		$printed = \Request::get("printed");
		$is_synced = \Request::get("is_synced");
		if((\Auth::user()->ref_lab == 2891 && MyHTML::results_manager_editor()) && ($type == 'lab_numbers' || $type == 'pending_results' || $type == 'lab_numbers_results')){
			return redirect()->back()->with('danger','You do not have permission to access this page');
		}
		return view("case_management.list", compact('type','printed','is_synced'));
	}
	public function list_data(){
		$type = \Request::get("type");
		$cols = ['epidNo','patient_surname','sex','age', 'nationality','nameWhere_sample_collected_from','where_sample_collected_from','specimen_ulin','request_date','sample_type','test_result','test_date','testedBy','lab_tech_phone','test_method'];
		$params = MyHTML::datatableParams($cols);

		extract($params);

		$search_cond ='';
		if(!empty($search)){
			$search_cond .=" AND ( p.nameWhere_sample_collected_from LIKE '%$search%' OR p.epidNo LIKE '%$search%' OR p.patient_surname LIKE '%$search%' OR p.patient_firstname LIKE '%$search%' OR age LIKE '%$search%' OR s.specimen_ulin  LIKE '%$search%')";
		}

		$and_cond = ' AND p.created_at > now() - interval 1 month';
		$test_method_val = '';
		//add should see all results but lab should see their specific results
		if(session('is_admin') != 1 AND $type != 'lab_numbers' AND $type != 'lab_numbers_results'){
			$and_cond .= " AND p.ref_lab = ".\Auth::user()->ref_lab;
		}
		if($type == 'lab_numbers' || $type ==  'lab_numbers_results'){
			$and_cond .= ' AND s.specimen_ulin IS NULL AND r.sample_id IS NULL';
		}
		if($type == 'pending_results'){
			$and_cond .= ' AND s.specimen_ulin IS NOT NULL AND r.sample_id IS NULL AND s.testing_lab ='. \Auth::user()->ref_lab;
		}
		if($type == 'review_results'){
			$and_cond .= ' AND r.test_result IS NOT NULL AND is_synced = 0 AND is_released = 1';
		}
		if($type == 'view_rdt_results'){
			$and_cond .= ' AND s.testing_lab ='. \Auth::user()->ref_lab. ' AND r.testing_platform like "%rdt%" ';
		}

		// if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user()){
		if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() ){
			$and_cond .= " AND p.nameWhere_sample_collected_from = '".MyHTML::getUserSiteOfCollection()['facility_name']."'";
		}

		if(\Auth::user()->ref_lab == 2892 || \Auth::user()->ref_lab == 2891){
			$test_method_val = 'PCR';
			//$and_cond .= " AND nameWhere_sample_collected_from LIKE '%Elegu%' AND nameWhere_sample_collected_from LIKE '%Mutukula%'";
		}elseif(\Auth::user()->ref_lab == 2893){
			$test_method_val = 'Xpert Express SARS-COV-2';
		}
		//make a general query to which other conditions will be added in case of filters and search
		$query_main = "SELECT p.id,p.epidNo,s.specimen_ulin,s.specimen_type,patient_surname,patient_firstname,sex,age,nationality,patient_contact,
		where_sample_collected_from,nameWhere_sample_collected_from,p.request_date,r.test_result,r.test_date, s.id as sample_id, s.test_type as sample_type, s.specimen_collection_date ,r.testedBy,r.lab_tech_phone,r.test_method, IF(p.swabing_district <> '',p.swabing_district,d.district) as swabing_district, r.id as result_id,r.is_synced
		FROM covid_patients p
		LEFT JOIN covid_results r ON(p.id = r.patient_id)
		LEFT JOIN covid_samples s ON(p.id = s.patient_id)
		LEFT JOIN districts d ON(p.facility_district = d.id)
		WHERE p.id > 0".$and_cond.$search_cond."
		";
		//\Log::info($query_main." ORDER BY $orderby LIMIT $start, $length");
		$results = \DB::select($query_main." ORDER BY $orderby LIMIT $start, $length");

		$recordsTotal = collect(\DB::select("SELECT count(p.id) as num
		FROM covid_patients p
		LEFT JOIN covid_results r ON(p.id = r.patient_id)
		LEFT JOIN covid_samples s ON(p.id = s.patient_id)
		WHERE p.id > 0 ".$and_cond.$search_cond."
		"))->first()->num;

		$recordsFiltered = empty($search)?$recordsTotal:collect(\DB::select("SELECT count(p.id) as num
		FROM covid_patients p
		LEFT JOIN covid_results r ON(p.id = r.patient_id)
		LEFT JOIN covid_samples s ON(p.id = s.patient_id)
		WHERE p.id > 0 ".$and_cond.$search_cond."
		"))->first()->num;

		$data = [];
		$districts_arr = MyHTML::array_merge_maintain_keys([''=>''], District::where('id', '>', '0')->pluck('district', 'district'));
		$result_arr = [''=>'','Negative'=>'Negative&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','Positive'=>'Positive&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'];
		//inlcude soroti lab
		if((MyHTML::is_rdt_site_user() && \Auth::user()->facilityID != 199) || MyHTML::is_facility_dlfp_user() ){
			$result_method_arr = ['RDT'=>'RDT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'];
		}else{
			$result_method_arr = ['' => 'Select test method','PCR'=>'PCR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
													'RDT'=>'RDT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'];
		}
		//let Bombo military see both RDT and PCR
		if( \Auth::user()->facilityID == 82){
			$result_method_arr = ['' => 'Select test method','PCR'=>'PCR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
													'RDT'=>'RDT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'];
		}
		
		foreach ($results as $result) {
			$select_str = "<input type='checkbox' class='samples' name='samples[]' value='$result->id'>";
			$url = "/outbreaks/result/$result->id/?tab=".\Request::get('tab');
			$release_retain_links = "cases/release_retain_manual_entries";
			$links = [];


			/*$sentinel_site = $result->sentinel_site;
			if(strtolower($sentinel_site) == 'Other'){
			$sentinel_site = $result->sentinel_other;
		}*/
		if($type == 'lab_numbers'){
			if(Auth::user()->ref_lab==2891){
				$locator_ids_arr =  MyHTML::array_merge_maintain_keys([''=>''],CovidSamples::whereNull('patient_id')->pluck('specimen_ulin','specimen_ulin'));
				$specimen_uline_input =  MyHTML::select('samples['.$result->id.'][specimen_ulin]',$locator_ids_arr,$default='','sp_'.$result->id,'rest_dr form-control input-sm');
			}else{
				$specimen_uline_input = "<input type='text' class='ulin form-control input-sm' name='samples[$result->id][specimen_ulin]'>";
			}
			$specimen_uline_input .= "<input type='text' class='ulin form-control hidden input-sm' name='samples[$result->id][sample_id]' value='$result->sample_id'>";
			$collection_date_input = "<input type='text' class='ulin form-control input-sm date_field standard-datepicker-nofuture' name='samples[$result->id][specimen_collection_date]' value='$result->specimen_collection_date'>";
			$sample_type_input = "<input type='text' class='ulin form-control input-sm' name='samples[$result->id][specimen_type]' value='$result->specimen_type'>";
			//$epidno_input = $result->epidNo;
			$epidno_input =  "<input type='text' class='form-control input-sm' name='samples[$result->id][epidno_id]' value='$result->epidNo'>";
			$case_name_input =  "<input type='text' class='form-control input-sm' name='samples[$result->id][case_name]' value='$result->patient_surname $result->patient_firstname'>";
			$result_input = '';
			$result_date = '';
			$result_testedBy = '';
			$result_lab_tech_phone = '';
			$result_test_method = '';
			$collection_point_input = "<input type='text' class='form-control input-sm' name='samples[$result->id][nameWhere_sample_collected_from]' value='$result->nameWhere_sample_collected_from'>";
			$swabbing_district_input = "<input type='text' class='form-control input-sm' name='samples[$result->id][swabing_district]' value='$result->swabing_district'>";


		}
		elseif($type == 'view_rdt_results'){
			$specimen_uline_input = "<input type='text' class='ulin form-control input-sm' name='samples[$result->id][specimen_ulin]'>";
			$specimen_uline_input .= "<input type='text' class='ulin form-control hidden input-sm' name='samples[$result->id][sample_id]' value='$result->sample_id'>";
			$collection_date_input = "<input type='text' class='ulin form-control input-sm date_field standard-datepicker-nofuture' name='samples[$result->id][request_date]' value='$result->request_date'>";
			$sample_type_input = "<input type='text' class='ulin form-control input-sm' name='samples[$result->id][sample_type]' value='$result->sample_type'>";
			$result_input = MyHTML::select('samples['.$result->id.'][result]',$result_arr,$default=$result->test_result,'res_'.$result->id,'rest_dr form-control input-sm');
			$result_date = "<input type='text' class='result form-control input-sm date_field standard-datepicker-nofuture date-field' name='samples[$result->id][test_date]' value='$result->test_date'>";

			$result_testedBy = "<input type='text' class='result form-control input-sm' name='samples[$result->id][testedBy]' value='$result->testedBy'>";
			$result_lab_tech_phone = "<input type='text' class='result form-control input-sm' name='samples[$result->id][lab_tech_phone]' value='$result->lab_tech_phone'>";
			$result_test_method = MyHTML::select('results['.$result->id.'][test_method]',$result_method_arr ,$default=$result->test_result,'res_'.$result->id,'rest_dr form-control input-sm');
			$result_input .= "<input type='text' class='result form-control hidden input-sm' name='samples[$result->id][sample_id]' value='$result->sample_id'>";
			$epidno_input =  "<input type='text' class='form-control input-sm' name='samples[$result->id][epidno_id]' value='$result->epidNo'>";
			$case_name_input =  "<input type='text' class='form-control input-sm' name='samples[$result->id][case_name]' value='$result->patient_surname $result->patient_firstname'>";
			$collection_point_input = "<input type='text' class='form-control input-sm' name='samples[$result->id][nameWhere_sample_collected_from]' value='$result->nameWhere_sample_collected_from'>";
			$swabbing_district_input = "<input type='text' class='form-control input-sm' name='samples[$result->id][swabing_district]' value='$result->swabing_district'>";

		}

		elseif($type == 'pending_results' || $type == 'review_results' ){
			/*TBD
			if(Auth::user()->ref_lab==22){
			$locator_ids_arr = [''=>''] + WorksheetSample::where('assigned_sample', '=', 0)->lists('locator_id','locator_id');
			$specimen_uline_input =  MyHTML::select('results['.$result->id.'][specimen_ulin]',$locator_ids_arr,$result->specimen_ulin,'sp_'.$result->id,'rest_dr form-control input-sm');
			$specimen_uline_input = $result->specimen_ulin;
		}else{
		$specimen_uline_input = "<input type='text' class='ulin form-control input-sm' name='results[$result->id][specimen_ulin]' value='$result->specimen_ulin'>";
	}
	$specimen_uline_input .= "<input type='text' class='ulin form-control hidden input-sm' name='results[$result->id][sample_id]' value='$result->sample_id'>";*/
	$specimen_uline_input = $result->specimen_ulin;

	$sample_type_input = $result->sample_type;
	$collection_date_input = "<input type='text' class='ulin form-control input-sm date_field standard-datepicker-nofuture' name='results[$result->id][request_date]' value='$result->specimen_collection_date'>";
	$result_input = MyHTML::select('results['.$result->id.'][result]',$result_arr,$default=$result->test_result,'res_'.$result->id,'rest_dr form-control input-sm');
	$result_date = "<input type='text' class='result form-control input-sm date_field standard-datepicker-nofuture date-field' name='results[$result->id][test_date]' value='$result->test_date'>";

	$result_testedBy = "<input type='text' class='result form-control input-sm' name='results[$result->id][testedBy]' value='$result->testedBy'>";
	$result_lab_tech_phone = "<input type='text' class='result form-control input-sm' name='results[$result->id][lab_tech_phone]' value='$result->lab_tech_phone'>";
	$result_test_method = MyHTML::select('results['.$result->id.'][test_method]',$result_method_arr ,$default=$result->test_result,'res_'.$result->id,'rest_dr form-control input-sm');
	$result_input .= "<input type='text' class='result form-control hidden input-sm' name='results[$result->id][sample_id]' value='$result->sample_id'>";
	$epidno_input = $result->epidNo;
	if($type == 'review_results'){
		$epidno_input .= '<br> <a href="/cases/release_retain_manual_entries/?id='.$result->result_id.'&type=retain">Retain</a>';
	}

	$case_name_input = $result->patient_surname.' '.$result->patient_firstname;
	$collection_point_input = $result->nameWhere_sample_collected_from;
	$swabbing_district_input = $result->swabing_district;
}elseif($type == 'lab_numbers_results'){
	//show both sample details and lab details
	$specimen_uline_input = "<input type='text' class='ulin form-control input-sm' name='samples[$result->id][specimen_ulin]'>";
	$specimen_uline_input .= "<input type='text' class='ulin form-control hidden input-sm' name='samples[$result->id][sample_id]' value='$result->sample_id'>";
	$collection_date_input = "<input type='text' class='ulin form-control input-sm date_field standard-datepicker-nofuture' name='samples[$result->id][request_date]' value='$result->request_date'>";
	$sample_type_input = "<input type='text' class='ulin form-control input-sm' name='samples[$result->id][sample_type]' value='$result->sample_type'>";
	$result_input = MyHTML::select('samples['.$result->id.'][result]',$result_arr,$default=$result->test_result,'res_'.$result->id,'rest_dr form-control input-sm');
	$result_date = "<input type='text' class='result form-control input-sm date_field standard-datepicker-nofuture date-field' name='samples[$result->id][test_date]' value='$result->test_date'>";

	$result_testedBy = "<input type='text' class='result form-control input-sm' name='samples[$result->id][testedBy]' value='$result->testedBy'>";
	$result_lab_tech_phone = "<input type='text' class='result form-control input-sm' name='samples[$result->id][lab_tech_phone]' value='$result->lab_tech_phone'>";
	$result_test_method = MyHTML::select('results['.$result->id.'][test_method]',$result_method_arr ,$default=$result->test_result,'res_'.$result->id,'rest_dr form-control input-sm');
	$result_input .= "<input type='text' class='result form-control hidden input-sm' name='samples[$result->id][sample_id]' value='$result->sample_id'>";
	$epidno_input =  "<input type='text' class='form-control input-sm' name='samples[$result->id][epidno_id]' value='$result->epidNo'>";
	$case_name_input =  "<input type='text' class='form-control input-sm' name='samples[$result->id][case_name]' value='$result->patient_surname $result->patient_firstname'>";
	$collection_point_input = "<input type='text' class='form-control input-sm' name='samples[$result->id][nameWhere_sample_collected_from]' value='$result->nameWhere_sample_collected_from'>";
	$swabbing_district_input = "<input type='text' class='form-control input-sm' name='samples[$result->id][swabing_district]' value='$result->swabing_district'>";

}

else{
	$epidno_input = $result->epidNo;
	if($result->test_result == ''){
		$epidno_input .= '<br><a href="/cases/edit/'.$result->id.'?sample_id='.$result->sample_id.'">Edit</a>';
	}
	$case_name_input = $result->patient_surname.' '.$result->patient_firstname;
	$specimen_uline_input = $result->specimen_ulin;
	$collection_date_input = $result->specimen_collection_date;
	$sample_type_input = $result->sample_type;
	$result_input = $result->test_result;
	$result_date = $result->test_date;
	$result_testedBy = '';
	$result_lab_tech_phone = '';
	$result_test_method ='';
	$collection_point_input = $result->nameWhere_sample_collected_from;
	$swabbing_district_input = $result->swabing_district;
}

$data[] = [
$epidno_input,
$case_name_input,
$result->sex,
$result->age?$result->age.' Yrs':'',
$result->nationality,
$collection_point_input,
$swabbing_district_input,
$specimen_uline_input,
$collection_date_input,
$sample_type_input,
$result_input,
$result_date,
$result_testedBy,
$result_lab_tech_phone,
$result_test_method
];

}
//the total number of records filtered -  based on search and filter conditions

return compact( "recordsTotal", "recordsFiltered", "data");
}

/*
*mas update results with district where the sample was taken from -  swabbing district
*/
public function massUpdatePatientInfo(){
	$samples = \Request::get('samples');
	foreach($samples as $patient_id =>$sample){

		//echo  $patient_id;
		//update the patient details
		if($sample['swabing_district'] == ''){
			return redirect()->back()->with('danger','Lab number not saved - swabbing district must have a value');
		}else{
			$patient = Covid::findOrFail($patient_id);
			$patient->epidNo = $sample['epidno_id'];
			$patient->patient_surname = $sample['case_name'];
			$patient->nameWhere_sample_collected_from = $sample['nameWhere_sample_collected_from'];
			$patient->swabing_district = $sample['swabing_district'];
			$patient->patient_firstname = '';
			$patient->save();
		}
		//deal with data that has sample details - ulin is provided
		if($sample['specimen_ulin'] != ''){
			//if sample_id not empty, simply update
			if($sample['sample_id'] == ''){
				$sample_info = [
				'patient_id' => $patient_id,
				'id' => $sample['sample_id'],
				'specimen_ulin' => $sample['specimen_ulin'],
				'testing_lab' => Auth::user()->ref_lab,
				'is_accessioned' => 1,
				'status' => 1,
				'specimen_type' => $sample['specimen_type'],
				'specimen_collection_date' => date('Y-m-d', strtotime($sample['specimen_collection_date'])),
				];
				$sample_obj = CovidSamples::updateOrCreate(['patient_id' => $patient_id,'id'=>$sample['sample_id']],$sample_info);
			}else{
				//create new record
				$sample_info = [
				'patient_id' => $patient_id,
				'specimen_ulin' => $sample['specimen_ulin'],
				'is_accessioned' => 1,
				'status' => 1,
				'specimen_type' => $sample['specimen_type'],
				'testing_lab' => Auth::user()->ref_lab,
				'specimen_collection_date' => date('Y-m-d', strtotime($sample['specimen_collection_date'])),
				];
				$sample_obj = CovidSamples::updateOrCreate(['patient_id' => $patient_id],$sample_info);

			}
			//update the worksheet with sample_id
			// \DB::statement("UPDATE worksheet_samples SET assigned_sample = 1, sample_id = ".$sample_obj->id." WHERE locator_id = '".$sample['specimen_ulin']."'");
		}

		//Worksheet
		\DB::statement("UPDATE covid_patients SET ref_lab = '".Auth::user()->ref_lab."', ulin = '".$sample['specimen_ulin']."' WHERE id = ".$patient_id);

		$added_arr = [$sample['specimen_ulin']=>$sample['specimen_ulin']];
	}
	return \Redirect::Intended('/cases/list?type=lab_numbers')->with('success','Case sample details added successfully');
}
public function massAssignResults(){
	$results = \Request::get('results');
	//dd(\Input::all());
	foreach($results as $patient_id =>$result){
		if($result['result'] != '' && $result['test_date'] != ''){
			//&& \Auth::user()->ref_lab
			if(CovidResult::where('sample_id', '=', $result['sample_id'])->first() && Auth::user()->ref_lab == 2891){
				return redirect()->back()->with('success','This selected lab number already has a result');
			}else{
				$result_info = [
				'test_result'=>$result['result'],
				'sample_id'=>$result['sample_id'],
				'test_date'=>date('Y-m-d H:i:s', strtotime($result['test_date'])),
				'patient_id' => $patient_id,
				'testedBy' => $result['testedBy'],
				'lab_tech_phone' => $result['lab_tech_phone'],
				'test_method' => $result['test_method'],
				'uploaded_by' => Auth::user()->id,
				'is_released' =>  1,
				'is_printed' =>  0,
				'original_file_name' =>  0,
				'used_file_name' =>  0,
				//final result
				'result_type' =>  1,
				];

				CovidResult::updateOrCreate(['sample_id' => $result['sample_id']],$result_info);
				//update the sample details
				$sample = CovidSamples::findOrFail($result['sample_id']);
				$sample->status = 3;
				//$sample->specimen_collection_date = date('Y-m-d', strtotime($result['request_date']));
				$sample->save();


				//dd($result_info);
				$ref_lab = Auth::user()->ref_lab;
				$query = "SELECT distinct(cr.sample_id), p.epidNo,p.caseID,p.patient_surname,p.patient_firstname, p.age,p.age_units,p.sex,p.patient_contact,
				p.nationality,p.where_sample_collected_from,p.nameWhere_sample_collected_from,p.swabing_district,p.receipt_number,p.who_being_tested,
				s.specimen_collection_date, p.request_date, p.serial_number,p.foreignDistrict,pd.district as patientDistrict
				,p.dataEntryDate,cr.test_result as test_result, cr.test_date as testing_date, p.ref_lab, cr.uploaded_by, d.district as swab_district,
				p.interviewer_name,p.interviewer_phone, s.specimen_type, cr.id  as result_id, s.specimen_ulin, p.is_classified, p.email_address,
				p.passportNo,p.eac_driver_id,p.eac_pass_id,cr.ct_value,cr.platform_range,cr.testing_platform, cr.test_method

				FROM covid_samples s
				LEFT JOIN  covid_results cr ON s.id = cr.sample_id
				LEFT JOIN users u ON u.id = cr.uploaded_by
				LEFT JOIN covid_patients p ON p.id = s.patient_id
				LEFT JOIN districts pd ON (pd.id = p.patient_district)
				LEFT JOIN districts d ON(p.facility_district = d.id)
				WHERE cr.sample_id not in (select sample_id from covid_results rr left join covid_samples css on rr.sample_id = css.id where rr.is_synced = 1)
				AND cr.is_synced = 0 AND p.ref_lab = ".$ref_lab." AND cr.is_released = 1";
				//dd(\DB::select($query));

				$results = \DB::select($query);

				$exists_array = [];

				foreach($results as $result){

					$age_val = $result->age == '' ? 0 : $result->age;

					$recipt_date = $result->dataEntryDate == ''?$result->specimen_collection_date:$result->dataEntryDate;

					$s_district = $result->swabing_district !="" ? $result->swabing_district : $result->swab_district;


					$create_update = [
					'date_of_collection' => $result->specimen_collection_date,
					'requester_contact' => $result->interviewer_phone,
					'requested_by' => $result->interviewer_name,
					'sample_type' => $result->specimen_type,
					'sample_received_on' => $recipt_date,
					'sentinel_site' => $result->nameWhere_sample_collected_from,
					'serial_number_batch' => $result->epidNo,
					'patient_id' => $result->epidNo,
					'case_name' => $result->patient_surname.' '.$result->patient_firstname,
					'district' => $s_district,
					'patient_district' => $result->patientDistrict == "" ? $result->foreignDistrict : $result->patientDistrict,
					'age_years' =>$age_val,
					'sex' => $result->sex,
					'nationality' => $result->nationality,
					'case_contact' => $result->patient_contact,
					'email_address' => $result->email_address,
					'passport_number' => $result->passportNo,
					'result' => $result->test_result,
					'case_id' => $result->caseID,
					'eacpass_id'=> $result->caseID,
					'eac_driver_id' => $result->eac_driver_id,
					'test_date' => $result->testing_date,
					'ref_lab' => $ref_lab,
					'ref_lab_name' =>  MyHTML::getRefLabName($ref_lab),
					'uploaded_by' =>  $result->uploaded_by,
					'is_released' => 1,
					'specimen_ulin' => $result->specimen_ulin,
					'ulin' => '',
					'is_classified' => $result->is_classified,
					'who_is_being_tested' => $result->who_being_tested,
					'receipt_number' => $result->receipt_number,
					'ct_value' => $result->ct_value,
					'platform_range' => $result->platform_range,
					'testing_platform' => $result->testing_platform,
					'test_method' => $result->test_method
					];
					$result_obj = Result::updateOrCreate($create_update);


					//dd($create_update);
					$is_date_valid = MyHTML::validateDates($result->specimen_collection_date,$recipt_date,$result->testing_date);

					$is_valid_result = MyHTML::validateResult(strtolower($result->test_result),$s_district, $result->specimen_ulin,$result->who_being_tested, $result->receipt_number);

					if(!Result::where('specimen_ulin', '=', $result->specimen_ulin)->where('ref_lab','=',$result->ref_lab)->first()){

						//      $result_obj = Result::Create($create_update);

						//now mark result as synced in the poe datatabase, if result is created
						\DB::statement("UPDATE covid_results set is_synced = 1 WHERE id = ".$result->result_id);
					}

				}

			}
			//      CovidResult::updateOrCreate(['sample_id' => $result['sample_id']],$result_info);
			//update the sample details
			//      $sample = CovidSamples::findOrFail($result['sample_id']);
			//      $sample->status = 3;
			//      $sample->specimen_collection_date = date('Y-m-d', strtotime($result['request_date']));
			//      $sample->save();
		}
	}

	//return \Redirect::Intended('/cases/list?type=pending_results')->with('success','Results added successfully');
	return redirect()->back()->with('success','Results added successfully');
}

public function massAssignLabNumbersResults(){

	$samples = \Request::get('samples');
	foreach($samples as $patient_id =>$sample){
		//update the patient details
		if($sample['swabing_district'] == ''){
			return redirect()->back()->with('danger','Results not saved - swabbing district must have a value');
		}else{
			$patient = Covid::findOrFail($patient_id);
			$patient->epidNo = $sample['epidno_id'];
			$patient->patient_surname = $sample['case_name'];
			$patient->nameWhere_sample_collected_from = $sample['nameWhere_sample_collected_from'];
			$patient->swabing_district = $sample['swabing_district'];
			$patient->patient_firstname = '';
			$patient->save();
		}

		if($sample['specimen_ulin'] != ''){
			//if sample_id not empty, simply update
			if($sample['sample_id'] == ''){
				$sample_info = [
				'patient_id' => $patient_id,
				'id' => $sample['sample_id'],
				'specimen_ulin' => $sample['specimen_ulin'],
				'testing_lab' => Auth::user()->ref_lab,
				'is_accessioned' => 1,
				'status' => 1,
				'specimen_type' => $sample['sample_type'],
				//'specimen_collection_date' => date('Y-m-d', strtotime($sample['request_date'])),
				];
				$sample_obj = CovidSamples::updateOrCreate(['patient_id' => $patient_id,'id'=>$sample['sample_id']],$sample_info);

			}else{
				//create new record
				$sample_info = [
				'patient_id' => $patient_id,
				'specimen_ulin' => $sample['specimen_ulin'],
				'is_accessioned' => 1,
				'status' => 1,
				'specimen_type' => $sample['sample_type'],
				'testing_lab' => Auth::user()->ref_lab,
				//'specimen_collection_date' => date('Y-m-d', strtotime($sample['request_date'])),
				];
				$sample_obj = CovidSamples::updateOrCreate(['patient_id' => $patient_id],$sample_info);

			}
			//assign ref_lab to the patient record
			\DB::statement("UPDATE covid_patients SET ref_lab = '".Auth::user()->ref_lab."', ulin = '".$sample['specimen_ulin']."' WHERE id = ".$patient_id);
			//dd($result);
			if($sample['result'] != '' && $sample['test_date'] != ''){

				if(CovidResult::where('sample_id', '=', $sample_obj->id)->first() && Auth::user()->ref_lab == 2891){
					return redirect()->back()->with('success','This selected lab number already has a result');
				}else{
					$result_info = [
					'test_result'=>$sample['result'],
					'sample_id'=>$sample_obj->id,
					'test_date'=>date('Y-m-d', strtotime($sample['test_date'])),
					'patient_id' => $patient_id,
					'testedBy' => $sample['testedBy'],
					'lab_tech_phone' => $sample['lab_tech_phone'],
					'test_method' => $sample['test_method'],
					'uploaded_by' => Auth::user()->id,
					'is_released' =>  1,
					'is_printed' =>  0,
					'original_file_name' =>  0,
					'used_file_name' =>  0,
					//final result
					'result_type' =>  1,
					];
					CovidResult::updateOrCreate(['sample_id' => $result['sample_id']],$result_info);
				}
			}
		}
	}
	return \Redirect::Intended('/cases/list?type=lab_numbers_results')->with('success','Lab number and results added successfully');;
}
public function editPatient($id){
	//dd($id);
	$patient = Covid::findOrFail($id);
	$sample = CovidSamples::findOrFail(\Request::get('sample_id'));

	$districts =  MyHTML::array_merge_maintain_keys([''=>''], District::where('id', '>', '0')->pluck('district', 'district'));
	$facilities=  MyHTML::array_merge_maintain_keys([''=>''], Facility::where('id', '>', '0')->pluck('facility', 'facility'));
	$nationality =  MyHTML::array_merge_maintain_keys([''=>''],\DB::table('nationalities')->pluck('nationality','nationality'));
	$poe =  MyHTML::array_merge_maintain_keys([''=>''], Facility::where('facilityLevelID', '=', '14')->pluck('facility','facility'));
	$gender_arr = [''=>'', 'Male'=>'Male','Female'=>'Female'];
	return view("case_management.edit", compact('facilities','poe','nationality','districts','patient','gender_arr','sample'));
}
public function update($id){

	$patient = Covid::findOrFail($id);
	//only creator can update this result
	if(\Auth::user()->ref_lab == 2891 && !MyHTML::results_manager_editor()){
		return \Redirect::back()->with('danger','You are not authorized to edit a patient you did not create');
	}
	$patient->dob = \Request::get('dob');
	$patient->age = \Request::get('age');
	$patient->patient_surname = \Request::get('patient_surname');
	$patient->nameWhere_sample_collected_from = \Request::get('nameWhere_sample_collected_from');
	$patient->swabing_district = \Request::get('swabing_district');
	$patient->patient_firstname = \Request::get('patient_firstname');
	$patient->passportNo = \Request::get('passportNo');
	$patient->last_updated_by = \Auth::user()->id;
	$patient->save();
	//update the sample info
	$sample = CovidSamples::findOrFail(\Request::get('sample_id'));
	$sample->specimen_ulin = \Request::get('specimen_ulin');
	$sample->save();
	return \Redirect::Intended('/cases/list')->with('success','updated successfully');
}

public function release_retain_manual_entries(){
	$page_type = \Request::get("type");
	$id = \Request::get("id");
	if($page_type == 'approve'){
		$query = "UPDATE covid_results SET is_released = 1 WHERE id=$id";
		\DB::unprepared($query);
		$success_message = "Result approved successfully";
	}
	if($page_type == 'retain'){

		$query = "UPDATE covid_results SET is_released =2 WHERE id=$id";
		\DB::unprepared($query);
		$success_message = "Result retained successfully";
	}
	//if post, means user selected many for approval, so approve the samples
	if(\Request::isMethod('post')){
		$samples = \Request::get("samples");

		if(count($samples)==0){
			return "please select at least one sample";
		}else{
			$samples_str = implode(",", $samples);
			$query = "UPDATE covid_results SET is_released = 1 WHERE id IN($samples_str)";
			\DB::unprepared($query);
			$success_message = "Results retained successfully";
		}

	}

	return \Redirect::back()->with('success',$success_message);

}

}
