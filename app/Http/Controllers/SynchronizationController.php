<?php namespace App\Http\Controllers;

use View;
use Auth;

class SynchronizationController extends Controller {

	public function index(){
		$ref_lab = env('LAB_ID');
		$query = "SELECT p.epidNo,p.caseID,p.patient_surname,p.patient_firstname, p.age,p.age_units,p.sex,p.patient_contact,
		p.nationality,p.where_sample_collected_from,p.nameWhere_sample_collected_from,p.swabing_district,p.receipt_number,p.who_being_tested,
		s.specimen_collection_date, p.request_date, p.serial_number,p.foreignDistrict,pd.district as patientDistrict
		,p.dataEntryDate,cr.test_result as test_result, cr.test_date as testing_date, p.ref_lab, cr.uploaded_by, d.district as swab_district,
		p.interviewer_name,p.interviewer_phone, s.specimen_type, cr.id	as result_id,s.specimen_ulin,s.ulin, p.is_classified, p.email_address,
		p.passportNo,0 as ct_value,cr.platform_range,cr.testing_platform

		FROM covid_samples s
		LEFT JOIN  covid_results cr ON s.id = cr.sample_id
		LEFT JOIN users u ON u.id = cr.uploaded_by
		LEFT JOIN covid_patients p ON p.id = s.patient_id
		LEFT JOIN districts pd ON (pd.id = p.patient_district)
		LEFT JOIN districts d ON(p.facility_district = d.id)
		WHERE cr.is_synced = 0 AND p.ref_lab = ".$ref_lab." AND cr.is_released = 1 
	    LIMIT 50 ";

		$results = \DB::select($query);		
		$data_array = [];
		$result_ids = [];
		foreach($results as $result){
			$age_val = $result->age == '' ? 0 : $result->age;
			$recipt_date = $result->dataEntryDate == ''?$result->specimen_collection_date:$result->dataEntryDate;
			$s_district = $result->swab_district=="" ? $result->swabing_district : $result->swab_district;

			$individual_data_array = [
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
			'test_date' => $result->testing_date,
			'ref_lab' => $result->ref_lab,
			'ref_lab_name' =>  \MyHTML::getRefLabName($result->ref_lab),
			'uploaded_by' =>  $result->uploaded_by,
			'is_released' => 1,
			'specimen_ulin' => $result->specimen_ulin,
			'ulin' => $result->ulin,
			'is_classified' => $result->is_classified,
			'who_is_being_tested' => $result->who_being_tested,
			'receipt_number' => $result->receipt_number,
			'ct_value' => $result->ct_value,
			'platform_range' => $result->platform_range,
			'testing_platform' => $result->testing_platform,
			'user' => 'kazuri',
    		'password' = 'beURK%23863_uVS'
			];

			$data_array[] = $individual_data_array;
			$result_ids[] = $result->result_id;
		}
		$resut_id_string = implode(",",$result_ids);
		$url = 'http://localhost:9000/system_sync';
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_array));
	    curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	    $cur_success = curl_exec($ch);
	    if($cur_success){
	    	\DB::statement("UPDATE covid_results set is_synced = 1 WHERE id IN($resut_id_string)");
	    }
	   
	    return redirect()->back()->with('success','Synchronization successful');
	    //dd($donne);
	}	
}
