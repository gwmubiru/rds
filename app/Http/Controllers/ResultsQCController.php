<?php namespace App\Http\Controllers;

use View;
use App\Models\Covid;
use App\Models\Facility;
use App\Models\Location\Hub;
use App\Models\CovidSamples;
use App\Models\CovidResult;
use App\Models\Worksheet;
use App\Models\WorksheetSample;
use App\Models\SampleResult;
use Auth;

class ResultsQCController extends Controller {

	public function index(){
		$test_fro = \Request::get("test_fro");
		$test_to = \Request::get("test_to");
		$samples = \Request::get("samples");
		$page_type =trim(\Request::get("page_type"));
		if($page_type == 'approved'){
			$page_title = "Approved Results";
		}elseif($page_type == 'retained'){
			$page_title = "Retained/Rescheduled Results";
		}elseif($page_type == 'pending_patient_info'){
			$page_title = "Pending Patient Details";
		}else{
			$page_title = "Pending Approval";			
		}
		if(!empty($_POST)){			
			$page_type = trim(\Request::get("p_type"));
			return \Redirect::Intended('/resultsqc/list?page_type='.$page_type.'&test_fro='.$test_fro.'&test_to='.$test_to);
		}else{
			return view("results_qc.results", compact('page_type','test_fro','test_to','page_title'));
		}
		
	}
	public function list_data(){
		//if it is a post, then process
		$page_type = trim(\Request::get("page_type"));
		$test_fro = \Request::get("test_fro");
		$test_to = \Request::get("test_to");
		//enable searching
		$cols = ['','epidNo','patient_surname', 'd.district','p.age','p.sex','p.patient_contact','passportNo','nationality','p.swabing_district','worksheet_number','tube','specimen_ulin','result1','result2', 'sr.ct_value','final_result',''];
		
		$params = \MyHTML::datatableParams($cols);

		extract($params);
		$search_cond ='';
		if(!empty($search)){
				$search_cond .=" AND ( tube LIKE '%$search%' OR final_result LIKE '%$search%' OR ws.locator_id LIKE '%$search%' OR w.worksheet_number LIKE '%$search%' OR sr.ct_value LIKE '%$search%' OR epidNo LIKE '%$search%' OR patient_surname LIKE '%$search%' OR patient_firstname LIKE '%$search%' OR d.district LIKE '%$search%' OR p.sex LIKE '%$search%'
OR p.patient_contact LIKE '%$search%' OR p.passportNo LIKE '%$search%' OR p.nationality LIKE '%$search%' OR p.swabing_district LIKE '%$search%' OR worksheet_number LIKE '%$search%')";  
		}

		//where page_type is 1, and tab is pending,
		$and_cond = '';
		
		if($page_type == 'approved'){
			$and_cond .= " AND is_completed = 1 AND is_approved = 1 AND is_synced = 0";
		}elseif($page_type == 'retained'){
			$and_cond .= " AND is_completed = 1 AND is_approved = 0 AND cs.created_at BETWEEN CURDATE() - INTERVAL 5 DAY AND CURDATE() + 1";
		}elseif($page_type == 'pending_approval'){
			$and_cond .= " AND is_completed = 0 AND is_approved = 0 AND p.id IS NOT NULL";
		}else{
			//panding patient info
			$and_cond .= " AND is_completed = 0 AND is_approved = 0 AND p.id IS NULL ";			
		}
		//sync any pending results
		/*\DB::statement("Update covid_patients p 
Inner join covid_results r on r.patient_id = p.id
Inner join users u
Set p.ref_lab = u.ref_lab
Where u.id = r.uploaded_by");*/
		
		$query_main = "SELECT sr.id, sr.worksheet_id, sr.is_completed, sr.sample_id, sr.result1, sr.result2, sr.ct_value, sr.final_result,
cs.specimen_ulin as locator_id, sr.is_completed,sr.is_approved, ws.locator_id, wt.tube as tube_id, cs.id as sample_id, cs.specimen_ulin, ws.id as worksheet_sample_id, p.patient_surname, p.patient_firstname,p.id as pid,d.district as swab_district,p.age,p.age_units,p.sex,p.patient_contact, p.passportNo,p.nationality,p.swabing_district,
		p.nationality,p.epidNo, w.worksheet_number, w.id as w_id, cr.test_result,cr.test_date, cr.id as r_id FROM worksheet_samples ws 
INNER JOIN worksheet_tubes wt ON ws.worksheet_tube_id = wt.id
INNER JOIN worksheets w ON wt.worksheet_id = w.id
INNER JOIN covid_samples cs ON ws.locator_id = cs.specimen_ulin
LEFT JOIN covid_patients p ON(cs.patient_id = p.id)
LEFT JOIN districts d ON(p.facility_district = d.id)
LEFT JOIN sample_results sr ON sr.sample_id = cs.id
LEFT JOIN covid_results cr ON cr.sample_id = sr.sample_id
WHERE cs.testing_lab = ".\Auth::user()->ref_lab." ".$and_cond.$search_cond.' GROUP BY sr.id';	
		//$query_all_filters = $query_main.$and_cond.$search_cond.' ORDER BY '.$orderby.' LIMIT '.$length;
		//\Log::info($query_main." ORDER BY $orderby LIMIT $start, $length");
		$results = \DB::select($query_main." ORDER BY $orderby LIMIT $start, $length");

		$recordsTotal = collect(\DB::select("SELECT count(sr.id) as num FROM worksheet_samples ws 
INNER JOIN worksheet_tubes wt ON ws.worksheet_tube_id = wt.id
INNER JOIN worksheets w ON wt.worksheet_id = w.id
LEFT JOIN covid_samples cs ON ws.locator_id = cs.specimen_ulin
LEFT JOIN covid_patients p ON(cs.patient_id = p.id)
LEFT JOIN districts d ON(p.facility_district = d.id)
LEFT JOIN sample_results sr ON sr.sample_id = cs.id
LEFT JOIN covid_results cr ON cr.sample_id = sr.sample_id
WHERE cs.testing_lab = ".\Auth::user()->ref_lab." ".$and_cond.$search_cond))->first()->num;

		$recordsFiltered = empty($search)?$recordsTotal:collect(\DB::select("SELECT count(sr.id) as num FROM worksheet_samples ws 
INNER JOIN worksheet_tubes wt ON ws.worksheet_tube_id = wt.id
INNER JOIN worksheets w ON wt.worksheet_id = w.id
LEFT JOIN covid_samples cs ON ws.locator_id = cs.specimen_ulin
LEFT JOIN covid_patients p ON(cs.patient_id = p.id)
LEFT JOIN districts d ON(p.facility_district = d.id)
LEFT JOIN sample_results sr ON sr.sample_id = cs.id
LEFT JOIN covid_results cr ON cr.sample_id = sr.sample_id
WHERE cs.testing_lab = ".\Auth::user()->ref_lab." ".$and_cond.$search_cond))->first()->num;

		$data = [];
		foreach ($results as $result) {
			$select_str = "<input type='checkbox' class='samples' name='sample_result_ids[]' value='$result->id'>";
			$url = "/outbreaks/result/$result->id/?tab=".\Request::get('tab');
			$approve_edit_links = "/outbreaks/release_retain";
			$reverse_link = "/outbreaks/release_retain?type=reverse&id=".$result->id;
			$approve_link = "/outbreaks/release_retain?type=approve&id=".$result->id;
			$retain_link = "/outbreaks/release_retain?type=retain&id=".$result->id;
			$use_existing_result = "/outbreaks/release_retain?type=use_existing_result&id=".$result->id;
			$links = [];
			if($page_type == "approved"){
				$links['Reverse'] = $reverse_link;								
			}elseif($page_type == "retained"){
				$links['Approve'] = $approve_link;
			}elseif($page_type == "pending_approval"){
				//2109-0006/1
				if($result->test_result == ''){					
					$links['Approve'] = $approve_link;
					$links['Reschedule'] = $retain_link;
				}else{
					$links['Do not override'] = $use_existing_result;
					$links['Override'] = $approve_link;
					$select_str = '';
				}
				
				$links['Edit Patient'] = '/cases/edit/'.$result->pid.'?sample_id='.$result->sample_id;
			}
			
			$w_number = '<a href="/worksheet/assign_results?worksheet_id='.$result->w_id.'">'.$result->worksheet_number.'</a>';
			$data[] = [
				$select_str,
				$result->epidNo,
				$result->patient_surname.' '.$result->patient_firstname,
				$result->age,
				$result->sex,
				$result->patient_contact,
				$result->passportNo,
				$result->nationality,
				$result->swab_district=="" ? $result->swabing_district : $result->swab_district,
				$w_number,
				$result->tube_id,
				$result->specimen_ulin,
				$result->result1,
				$result->result2,
				$result->ct_value,
				$result->final_result,
				$result->test_result,
				$result->test_date,
				//\MyHTML::localiseDate($result->specimen_collection_date,'d M Y'),
				\MyHTML::specialDropdownLinks($links)
			];

		}
		//the total number of records filtered -  based on search and filter conditions

		return compact( "recordsTotal", "recordsFiltered", "data");
	}
	
}
