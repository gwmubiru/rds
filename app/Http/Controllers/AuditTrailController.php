<?php namespace App\Http\Controllers;

use View;
use App\Models\Logistic;
use App\Models\Facility;
use App\Models\District;
use App\Models\ResultHistory;
use Input;
use Auth;
use App\Closet\MyHTML as MyHTML;

class AuditTrailController extends Controller {

	public function getParameters()
	{
		$start = \Request::get('test_date_fro');
		$end = \Request::get('test_date_to');
		$ref_lab_name = \Request::get('ref_lab_name');

		return array($start, $end,$ref_lab_name);
	}

	public function list_data(){
		$type = \Request::get("type");
		$cols = ['transaction_date','date_of_collection','sample_received_on','sample_type','sentinel_site',
		'specimen_ulin','case_name','passport_number','age_years','sex','case_contact','district',
		'nationality','specimen_ulin','test_date','result','original_file_name','ref_lab_name',
		'used_file_name','who_is_being_tested','receipt_number','is_printed','uploaded_by',
		'download_counter','printed_by','last_printed_on','test_method'];
		$params = MyHTML::datatableParams($cols);

		extract($params);

		$search_cond ='';
		$and_cond ='';
		if(!empty($search)){
			$search_cond .=" AND ( r.case_name LIKE '%$search%' OR r.patient_id LIKE '%$search%' OR r.ref_lab_name LIKE '%$search%' OR r.original_file_name LIKE '%$search%')";
		}
		 if(!MyHTML::isAuditUser()){
			$and_cond .= " AND r.ref_lab = ".\Auth::user()->ref_lab;
		}

		//make a general query to which other conditions will be added in case of filters and search
		$query_main = "select r.transaction_date,r.date_of_collection,r.sample_received_on,r.sample_type,r.sentinel_site,r.patient_id,
		r.case_name,r.passport_number,r.age_years,r.sex,r.case_contact,r.district,r.nationality,
		r.specimen_ulin,r.test_date,r.result,r.original_file_name,u.username as uploaded_by,
		r.ref_lab_name,r.used_file_name,r.who_is_being_tested,r.receipt_number,r.created_at,
		r.is_printed,r.download_counter,r.printed_by,r.last_printed_on,r.test_method from results_hist r
		left join users u on r.uploaded_by = u.id 	WHERE r.revision is not null	 ".$and_cond.$search_cond."";

		$results = \DB::select($query_main." ORDER BY patient_id LIMIT $start, $length");
		$recordsTotal = collect(\DB::select("SELECT count(r.revision) as num
		FROM results_hist r
		WHERE r.revision is not null ".$and_cond.$search_cond."
		"))->first()->num;

		$recordsFiltered = empty($search)?$recordsTotal:collect(\DB::select("SELECT count(r.revision) as num
		FROM results_hist r
		WHERE r.revision is not null".$and_cond.$search_cond."
		"))->first()->num;

		foreach ($results as $result) {

			$data[] = [
			$result->transaction_date,
			$result->date_of_collection,
			$result->sample_received_on,
			$result->sample_type,
			$result->sentinel_site,
			$result->district,
			$result->specimen_ulin,
			$result->case_name,
			$result->passport_number,
			$result->age_years,
			$result->sex,
			$result->case_contact,
			$result->nationality,
			$result->who_is_being_tested,
			$result->specimen_ulin,
			$result->test_date,
			$result->result,
			$result->test_method,
			$result->ref_lab_name,
			$result->uploaded_by,
			$result->created_at,
			$result->original_file_name,
			$result->used_file_name,
			$result->download_counter,
			$result->printed_by,
			$result->last_printed_on
			];
		}
		return compact( "recordsTotal", "recordsFiltered", "data");
	}

	public function index(){
		$audit = ResultHistory::where('ref_lab', \Auth::user()->ref_lab)->get();
		if(\Auth::user()->type == 26 || \Auth::user()->type ==1 || \Auth::user()->type == 40 || \Auth::user()->type == 41){
			$ref_labs =  MyHTML::array_merge_maintain_keys([''=>''],Facility::where('facilityLevelID', '=', '20')->pluck('facility', 'facility'));
	}
		else{
			$lab = \DB::select("select f.facility from users u left join facilities f on u.ref_lab = f.id where f.id = ".\Auth::user()->ref_lab);

			$ref_labs = $lab[0]->facility;

			// $ref_labs = [''=>''] + Facility::where('facilityLevelID', '=', '20')->where('id', \Auth::user()->ref_lab)->pluck('facility', 'facility');
	}
		$type = \Request::get("type");

		return view("Admin.audit.index",compact('audit','type','ref_labs'));
	}


	public function getAuditCsv(){

		$data = \DB::select("select r.transaction_date,r.date_of_collection,r.sample_received_on,r.sample_type,r.sentinel_site,r.patient_id,
		r.case_name,r.passport_number,r.age_years,r.sex,r.case_contact,r.district,r.nationality,
		r.specimen_ulin,r.test_date,r.result,r.original_file_name,u.username as uploaded_by,
		r.ref_lab_name,r.used_file_name,r.who_is_being_tested,r.receipt_number,r.created_at,
		r.is_printed,r.download_counter,r.printed_by,r.last_printed_on,r.test_method from results_hist r
		left join users u on r.uploaded_by = u.id WHERE ref_lab_name like '%".$this->getParameters()[2]."%'
		AND date(test_date) between '".$this->getParameters()[0]."' AND '".$this->getParameters()[1]."' ORDER BY patient_id desc");

		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=rds-".$this->getParameters()[2]."-audit-trail-".$this->getParameters()[0]." to ".$this->getParameters()[1].".csv");
		$output = fopen('php://output', 'w');
		$headers = array(

			'Transaction Date',
			'Date of Collection',
			'Sample Received',
			'Sample Type',
			'Collection Site',
			'Swabbing District',
			'Client Identifier',
			'Client Name',
			'Passport Number',
			'Age',
			'sex',
			'Client Contact',
			'Nationality',
			'Reason for Testing',
			'Specimen Identifier',
			'Test Date',
			'Result',
			'Test Method',
			'Testing Laboratory',
			'Uploaded By',
			'Date Uploaded',
			'CSV File Used',
			'System Given CSV name',
			'Number of times downloaded',
			'Printed By',
			'Date Last Printed'
		);

		fputcsv($output, $headers);
		foreach ($data as $data) {
			$row=array(
				$data->transaction_date,
				$data->date_of_collection,
				$data->sample_received_on,
				$data->sample_type,
				$data->sentinel_site,
				$data->district,
				$data->specimen_ulin,
				$data->case_name,
				$data->passport_number,
				$data->age_years,
				$data->sex,
				$data->case_contact,
				$data->nationality,
				$data->who_is_being_tested,
				$data->specimen_ulin,
				$data->test_date,
				$data->result,
				$data->test_method,
				$data->ref_lab_name,
				$data->uploaded_by,
				$data->created_at,
				$data->original_file_name,
				$data->used_file_name,
				$data->download_counter,
				$data->printed_by,
				$data->last_printed_on
			);

			fputcsv($output, $row);
		}
		fclose($output);

	}
}
