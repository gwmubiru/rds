<?php namespace App\Http\Controllers\Admin;
use View;
use App\Models\CovidSamples;
use App\Models\SampleReception;
use Illuminate\Http\Request;
use Session;
use Lang;
use Input;
use Carbon;
use Redirect;
use DB;
use Auth;

class ReportController extends Controller {
	
	public function viewDetails(){
		$result_id = decode(\Request::get("result_id"));
		//dd($result_id);
		$query = "SELECT r.id, r.test_date as result_date, r.test_result,r.is_released,r.testing_platform,r.test_method, p.id as pid, p.epidNo, p.patient_district,
		p.health_facility,p.patient_surname,p.patient_firstname,p.age,p.sex,s.id as sid,p.who_being_tested,p.nationality,p.passportNo,b.name as branch,
		s.specimen_collection_date,d.district as swab_district,p.foreignDistrict,pd.district as patientDistrict, nameWhere_sample_collected_from, p.caseID,s.specimen_ulin, s.ulin,u.family_name, u.other_name FROM
		covid_samples s
		LEFT JOIN  covid_results r ON s.id = r.sample_id
		LEFT JOIN users u ON u.id = r.uploaded_by
		LEFT JOIN covid_patients p ON p.id = s.patient_id
		LEFT JOIN  branches b ON s.branch_id = b.id
		LEFT JOIN districts pd ON (pd.id = p.patient_district)
		LEFT JOIN districts d ON(p.facility_district = d.id)
		WHERE r.id = ".$result_id;

		$query_results = \DB::select($query);
		return view("reports.view_details", compact('query_results'));
	}
	public function index(){
		$test_fro = \Request::get("test_fro");
		$test_to = \Request::get("test_to");
		$samples = \Request::get("samples");
		if(empty($test_fro)){
			$test_to = date('Y-m-d');
			$test_fro = date('Y-m-d', strtotime('today - 30 days'));
		}
		
		$page_type = base64_decode(\Request::get("type"));
		if(!empty($_POST)){
			$page_type = \Request::get("p_type");
			return \Redirect::Intended('/reports/list?test_fro='.$test_fro.'&test_to='.$test_to);
		}else{
			return view("reports.list", compact('test_fro','test_to'));
		}

	}
	public function list_data(){

		//if it is a post, then process
		$status_type = \Request::get("type");
		
		$test_fro = \Request::get("test_fro");
		$test_to = \Request::get("test_to");

		//enable searching
		$cols = ['specimen_ulin','sentinel_site', 'specimen_collection_date', 'patient_surname',
		'age_years', 'sex', 'test_date','test_result'];
		$params = \MyHTML::datatableParams($cols);

		extract($params);

		$search_cond ='';
		if(!empty($search)){
			$search_cond .=" AND ( p.epidNo LIKE '%$search%' OR s.specimen_collection_date LIKE '%$search%' OR p.patient_surname LIKE '%$search%' OR r.test_result LIKE '%$search%' OR age LIKE '%$search%' OR s.specimen_ulin  LIKE '%$search%')";
		}


		//where page_type is 1, and tab is pending,
		$and_cond = '';

		if(!empty($test_fro) && !empty($test_to)){
			$and_cond .= " AND r.test_date BETWEEN '$test_fro' AND '$test_to'";
		}
		

		//sync any pending results
		\DB::statement("Update covid_patients p
		Inner join covid_results r on r.patient_id = p.id
		Inner join users u
		Set p.ref_lab = u.ref_lab
		Where u.id = r.uploaded_by");
		//make a general query to which other conditions will be added in case of filters and search
		$query_main = "SELECT r.id, r.test_date as result_date, r.test_result,r.is_released, p.id as pid, p.epidNo, p.patient_district,
		p.health_facility,p.patient_surname,p.patient_firstname,p.age,p.sex,s.id as sid
		,s.specimen_collection_date,d.district as swab_district,p.foreignDistrict,pd.district as patientDistrict, nameWhere_sample_collected_from, p.caseID,s.specimen_ulin, s.ulin FROM
		covid_samples s
		LEFT JOIN  covid_results r ON s.id = r.sample_id
		LEFT JOIN users u ON u.id = r.uploaded_by
		LEFT JOIN covid_patients p ON p.id = s.patient_id
		LEFT JOIN districts pd ON (pd.id = p.patient_district)
		LEFT JOIN districts d ON(p.facility_district = d.id)
		WHERE r.is_released = 1 ".$and_cond.$search_cond.' GROUP BY p.id';
		//\Log::info($query_main);
		$results = \DB::select($query_main." ORDER BY $orderby LIMIT $start, $length");

		$recordsTotal = collect(\DB::select("SELECT count(p.id) as num FROM
		covid_samples s
		LEFT JOIN  covid_results r ON s.id = r.sample_id
		LEFT JOIN users u ON u.id = r.uploaded_by
		LEFT JOIN covid_patients p ON p.id = s.patient_id
		LEFT JOIN districts pd ON (pd.id = p.patient_district)
		LEFT JOIN districts d ON(p.facility_district = d.id)
		WHERE r.is_released = 1 ".$and_cond))->first()->num;

		$recordsFiltered = empty($search)?$recordsTotal:collect(\DB::select("SELECT count(p.id) as num FROM
		covid_samples s
		LEFT JOIN  covid_results r ON s.id = r.sample_id
		LEFT JOIN users u ON u.id = r.uploaded_by
		LEFT JOIN covid_patients p ON p.id = s.patient_id
		LEFT JOIN districts pd ON (pd.id = p.patient_district)
		INNER JOIN districts d ON(p.facility_district = d.id)
		WHERE r.is_released = 1 ".$and_cond.$search_cond))->first()->num;

		$data = [];
		foreach ($results as $result) {
			
			$data[] = [
			"<a href='/reports/view_details?result_id=".encode($result->id)."'>".$result->specimen_ulin.'<a>',
			$result->nameWhere_sample_collected_from,
			\MyHTML::localiseDate($result->specimen_collection_date,'d M Y'),
			$result->patient_surname.' '.$result->patient_firstname,
			$result->age.'Years',
			$result->sex,
			\MyHTML::localiseDate($result->result_date,'d M Y'),
			$result->test_result
			];

		}
		//the total number of records filtered -  based on search and filter conditions

		return compact( "recordsTotal", "recordsFiltered", "data");
	}
	


}