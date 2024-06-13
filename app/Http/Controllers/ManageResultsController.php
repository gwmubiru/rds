<?php namespace App\Http\Controllers;

use View;
use App\Models\Result;
use App\Models\Facility;
use App\Models\Covid;
use App\Models\CovidResult;
use App\Models\CovidSamples;
use App\Models\Location\Hub;
use Mail;
use Crypt;
use \Illuminate\Contracts\Encryption\DecryptException;
use Validator;
use Auth;

class ManageResultsController extends Controller {

	public function index(){
		$samples = \Request::get("samples");
		$page_type = base64_decode(\Request::get("type"));
		$printed = \Request::get("printed");
		return view("outbreaks.results", compact('page_type','printed'));
	}
	public function list_data(){
		//if it is a post, then process
		$status_type = \Request::get("type");
		$printed = \Request::get("printed");
		//enable searching
		$cols = ['patient_id', 'district', 'sentinel_site', 'date_of_collection', 'case_name',
		'age_years', 'sex', 'test_date','result'];
		$params = \MyHTML::datatableParams($cols);

		extract($params);
		$search_cond = !empty($search)?" AND (patient_id LIKE '%$search' OR district LIKE '$search%' OR sentinel_site LIKE '$search%' OR date_of_collection LIKE '$search%' OR case_name LIKE '$search%'  OR testing_platform LIKE '$search%' OR result LIKE '$search%' OR age_years LIKE '$search%')":" ";

		//where page_type is 1, and tab is pending,
		$and_cond = '';
		if($status_type == 1){
			if($printed == 'undefined'){
				$printed = 0;
			}
			$and_cond .= ' AND is_printed = '.$printed;
			if(\MyHTML::is_eoc()){
				$and_cond .= " AND result LIKE '%Negative%'";
			}
			if(\MyHTML::is_incident_commander()){
				//$and_cond .= " AND result LIKE '%Positive%'";
			}
		}
		if(\MyHTML::is_ref_lab()){
			$and_cond .= " AND ref_lab=".\Auth::user()->ref_lab;
		}

		$query = "SELECT * FROM results WHERE is_released=$status_type ".$and_cond.$search_cond.' ORDER BY '.$orderby.' LIMIT '.$length;
		//\Log::info($query);
		$recordsTotal = collect(\DB::select("SELECT count(id) as num FROM results WHERE is_released=$status_type ".$and_cond))->first()->num;
		$results = \DB::select($query);
		$data = [];
		foreach ($results as $result) {
			$select_str = "<input type='checkbox' class='samples' name='samples[]' value='$result->id'>";
			$url = "/outbreakrlts/result/$result->id/?tab=".\Request::get('tab');
			$approve_edit_links = "/outbreakrlts/approve_retain";
			$links = [];
			if($result->is_released==1){
				$links['Print'] = "javascript:windPop('$url')";
				$links['Download'] = "$url&pdf=1";
			}

			if($result->is_released==0 && \MyHTML::permit(22)){
				$links['Approve'] = "$approve_edit_links?type=approve&id=$result->id";
				$links['Retain'] = "$approve_edit_links?type=retain&id=$result->id";
				$links['Edit'] = "$approve_edit_links?type=edit&id=$result->id";
			}
			if($result->is_released==1 && \MyHTML::is_eoc()){
				$links['Edit'] = "$approve_edit_links?type=edit&id=$result->id";
			}

			$sentinel_site = $result->sentinel_site;
			if(strtolower($sentinel_site) == 'Other'){
				$sentinel_site = $result->sentinel_other;
			}

			$data[] = [
			$select_str,
			$result->patient_id,
			$result->district,
			$sentinel_site,
			\MyHTML::localiseDate($result->date_of_collection,'d M Y'),
			$result->case_name,
			$result->age_years?$result->age_years.'Years':'',
			$result->sex,
			\MyHTML::localiseDate($result->test_date,'d M Y'),
			$result->result,
			\MyHTML::specialDropdownLinks($links)];
		}
		$recordsFiltered = count($data);
		return compact( "recordsTotal", "recordsFiltered", "data");
	}
	public function result($id=""){
		$vldbresult = [];
		if(!empty($id)){
			$samples = [$id];
		}else{
			$samples = \Request::get("samples");
			if(count($samples)==0){
				return "please select at least one sample";
			}
		}
		$vldbresult = $this->fetch_result($samples);
		$tab = \Request::get('tab');
		$print_version = "1.0";

		if(\Request::has('pdf')){
			$pdf = \PDF::loadView('direct.result_slip', compact('vldbresult', 'print_version'));
			return $pdf->download('vl_results_'.\Request::get('facility').'.pdf');
		}
		return view('direct.result_slip', compact('vldbresult', 'print_version'));
	}

	private function fetch_result($samples, $f=0){
		$samples_str = implode(",", $samples);
		$samples_cond = !empty($f)?"form_number in ($f)":"s.id in ($samples_str)";
		//mark each printed
		foreach($samples as $sample_id){
			$result = Result::findOrFail($sample_id);
			$result->is_printed = 1;
			$result->save();
		}
		$sql = "SELECT * FROM results AS s
		WHERE $samples_cond LIMIT 100
		";
		return \DB::select($sql);
	}


	public function store_out(){
		//return \Redirect::back()->withInput()->withFlashMessage('You do not have permission to upload results. Contact the systems administrator');
		if((\MyHTML::is_ref_lab() && Auth::user()->ref_lab == 2891 && Auth::user()->id != 5115 && Auth::user()->id !=1048) || (\MyHTML::is_ref_lab() && Auth::user()->ref_lab == 2907) || (Auth::user()->id == 976)){
			return \Redirect::back()->withInput()->withFlashMessage('You do not have permission to upload results. Contact the systems administrator');
		}
		if(\MyHTML::is_ref_lab()  || \MyHTML::is_cphl_lab() || \MyHTML::is_rdt_site_user() || \MyHTML::is_facility_dlfp_user()){
			if(Auth::user()->ref_lab == 2907){
				return \Redirect::back()->withInput()->withFlashMessage('You do not have permission to upload results. Contact the systems administrator');
			}
			$page_type = \Request::get('type');
			if($page_type == 'form'){
				return view("outbreaks.upload");

			}
			else{
				//process form
				$fileInput_field = "csv";
				if( \Request::hasFile($fileInput_field) == false ){
					return "Upload Failed: No File was found - please select the file to upload";
				}

				if( \Request::file($fileInput_field)->isValid() == false ){
					return "File upload failed";
				}
				$file_name =  \Request::file($fileInput_field)->getClientOriginalName();
				$extension =  '.'.\Request::file($fileInput_field)->getClientOriginalExtension();

				$dest_folder = public_path().'/uploads/results';
				$dest_fileName = time(). $extension;
				$uploaded_file =  \Request::file($fileInput_field)->move($dest_folder, $dest_fileName);

				$uploaded_file = $dest_folder . "/" . $dest_fileName;
				if(\MyHTML::is_rdt_site_user() || \MyHTML::is_facility_dlfp_user()){
					$ret = $this->save_rdt_file_results($uploaded_file,$file_name,$dest_fileName);
				}else{
					$ret = $this->save_file_results($uploaded_file,$file_name,$dest_fileName);
				}
				

				if(count($ret['failed_arr'])){
					$err_message = $this->generateMessageFromArray($ret['failed_arr']);

					return \Redirect::back()->with('danger',$err_message);
				}else{
					return \Redirect::Intended('/outbreaks/list?type=MQ==&printed=2')->with('success','Results uploaded successfully');
				}

			}
		}else{
			dd('Operation not allowed!');
		}
	}

	private function save_file_results($file_path,$original_file_name,$used_file_name){
		$file = fopen($file_path, 'r');
		// dd(\DB::getDatabaseName());

		$count = 0;
		$line = 0;
		$failed_array = [];
		$exists_array = [];
		$ref_lab_name = \MyHTML::getRefLabName(\Auth::User()->ref_lab);

		$error_messages = [];

		while( ($row = fgetcsv($file)) !== FALSE){
			$col_count = count($row); //column count

			if($col_count == 18 && $_FILES["csv"]["size"] > 0){
				$file = fopen($file_path, "r");
				fgetcsv($file);
				while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
					// dd($column);
					$data = \DB::select('select p.epidNo, s.* from covid_patients p left join covid_samples s on s.patient_id = p.id where p.epidNo = ' ."'$column[0]'");
					for ($i=0; $i <count($data) ; $i++) {

						$results = [
						'tested_by'	=> \Auth::user()->id,
						'test_result'	=> $column[11],
						'test_method'	=> $column[14],
						'test_date'	=> $column[12],
						'sample_id'	=> $data[$i]->id,
						'patient_id' => $data[$i]->patient_id,
						'date_of_collection'	=> $data[$i]->specimen_collection_date,
						'sample_result_id'	=> 0,
						'is_released' => 1,
						'is_printed' => 0,
						'is_synced' => 0,
						'original_file_name' => $_FILES['csv']['name'],
						'used_file_name'	=>  $_FILES['csv']['name'],
						'uploaded_by' =>	\Auth::user()->id,
						'testedBy'	=> \Auth::user()->username,
						];

						$result_obj = CovidResult::updateOrCreate(['patient_id' => $data[$i]->patient_id,'sample_id'=>$data[$i]->id],$results)->whereNull('patient_id');

					$sample_info = [
						'specimen_ulin' => $column[10],
						'testing_lab' => Auth::user()->ref_lab,
						'is_accessioned' => 1,
						'status' => 3,
						'is_accessioned'=> 1,
						'in_lims'=> 1
						];
						$sample_obj = CovidSamples::updateOrCreate(['patient_id' => $data[$i]->patient_id,'id'=>$data[$i]->id],$sample_info);

					}
				}
			}

			else{

				if($count){
					$row_data[]=$row;
					$line++;
					$result_data = array();
					//load row data in the corresponding db columns
					list(
					$result_data['specimen_ulin'],
					$result_data['patient_id'],
					$result_data['date_of_collection'],
					$result_data['sample_received_on'],
					$result_data['type_of_site'],
					$result_data['sentinel_site'],
					$result_data['case_name'],
					$result_data['sex'],
					$result_data['age_years'],
					$result_data['nationality'],
					$result_data['case_contact'],
					$result_data['patient_district'],
					$result_data['district'],
					$result_data['worksheet_no'],
					$result_data['sample_type'],
					$result_data['result'],
					$result_data['test_date'],
					$result_data['passport_number'],
					$result_data['email_address'],
					$result_data['who_is_being_tested'],
					$result_data['receipt_number'],
					$result_data['ever_tested_positive'],
					$result_data['is_vaccinated'],
					$result_data['vaccine_type'],
					$result_data['approver_signature'],
					$result_data['approved_by']
					) = $row;
					unset($result_data['worksheet_no']);
					//add columns necessary but were not part of the csv
					$result_data['ref_lab'] = \Auth::User()->ref_lab;
					$result_data['ref_lab_name'] = $ref_lab_name;
					$result_data['uploaded_by'] = \Auth::User()->id;
					$result_data['is_released'] = 1;
					$result_data['original_file_name'] = $original_file_name;
					$result_data['used_file_name'] = $used_file_name;
					$result_data['show_result'] = 1;

					if(\Auth::user()->ref_lab==2896){
						$result_data['eacpass_id'] = $row[21];
						$result_data['ever_tested_positive'] = $row[22];
						$result_data['is_vaccinated'] = $row[23];
						$result_data['vaccine_type'] = $row[24];
						unset($result_data['approver_signature']);
					}
					if(strtolower($result_data['is_vaccinated']) == 'no' || $result_data['is_vaccinated'] == 'Unknown'){
						//reset vaccination type
						$result_data['vaccine_type'] = '';
					}
					if(strtolower($result_data['is_vaccinated']) == 'yes' && $result_data['vaccine_type'] == ''){
						//reset vaccination type
						$result_data['vaccine_type'] = 'Any';
					}
					//reconstruct date fields - to be in the required format
					$result_data['date_of_collection'] = \MyHTML::reconstructDate($result_data['date_of_collection']);
					$result_data['sample_received_on'] = \MyHTML::reconstructDate($result_data['sample_received_on']);
					$result_data['test_date'] = \MyHTML::reconstructDate($result_data['test_date']);
					
					//$result_data['date_of_collection'] = date('Y-m-d H:i:s', strtotime($result_data['date_of_collection']));
					//$result_data['sample_received_on'] = date('Y-m-d H:i:s', strtotime($result_data['sample_received_on']));
					//$result_data['test_date'] = date('Y-m-d H:i:s',strtotime($result_data['test_date']));
					//if no patient_d, assign the  specimen_ulin

					if($result_data['patient_id'] == '' or $result_data['patient_id'] == '""'){
						$result_data['patient_id'] = $result_data['specimen_ulin'];
					}
					$result_data['serial_number_batch'] = $result_data['patient_id'];
					//dd($result_data);
					//add custom date rules
					Validator::extend('before_or_equal', function($attribute, $value, $parameters) {
						//$attribute - collection date;
						//$value - value of collection date
						//value being compared - reception date;
						return strtotime($parameters[0]) >= strtotime($value);
					});
					Validator::extend('after_or_equal', function($attribute, $value, $parameters) {
						//$attribute - collection date;
						//$value - value of collection date
						//value being compared - reception date;
						//echo $parameters[0];
						//dd(($value));
						return strtotime($parameters[0]) <= strtotime($value);
					});
					$this->accepted_results = array('Positive', 'Negative');
					$this->yes_no_options = array('Yes','No','Unknown');
					$this->vaccine_types = array('AstraZeneca', 'Pfizer', 'Moderna', 'Sinopham', 'Sinovac','Johnson & Johnson', 'Johnson and Johnson');
					$validator = Validator::make($result_data, [
					'specimen_ulin' => 'required|unique:results',
					'case_name' => 'required',
					'district' => 'required',
					'date_of_collection'=>"required|date|before_or_equal:".$result_data['sample_received_on']
					."|before_or_equal:".date('Y-m-d H:i:s')."|after_or_equal:".date('2019-03-02 00:00:00'),
					'sample_received_on'=>"required|date|before_or_equal:".$result_data['test_date']."|before_or_equal:".date('Y-m-d H:i:s')."|after_or_equal:".date('1970-01-01'),
					'test_date'=>'required|date|before_or_equal:'.date('Y-m-d H:i:s')."|after_or_equal:".date('1970-01-01'),
					'result' => 'required|in:' . implode(',', $this->accepted_results),
					'ever_tested_positive' => 'required|in:' . implode(',', $this->yes_no_options),
					'is_vaccinated' => 'required|in:' . implode(',', $this->yes_no_options),
					'vaccine_type' => 'in:' . implode(',', $this->vaccine_types),
					],
					[
					'specimen_ulin.required' => 'The patient Id is required for row '.$count,
					'district.required' => 'The swabbing district is required for row '.$count,
					'specimen_ulin.unique' => 'The patient Id in row '.$count.' is already used',
					'case_name.required' => 'The name is required for row '.$count,
					'date_of_collection.before_or_equal' => "The collection date cannot be greater than reception date or today's date for row ".$count,
					'date_of_collection.after_or_equal' => "enter a correct collection date row ".$count,
					'sample_received_on.before_or_equal' => "The reception date cannot be greater than the test date or today's date for row ",
					'sample_received_on.after_or_equal' => "Enter correct date format for date of reception for row ",
					'test_date.before_or_equal' => "The test date cannot be greater today's date for row ".$count,
					'test_date.after_or_equal' => "Enter correct test date value row ".$count,
					'result.in' => 'The only accepted values for result are: Positive, Negative. Check row '.$count,
					'ever_tested_positive.in' => 'The only accepted values ever been confirmed with COVID-19 are: Yes, No. Check row '.$count,
					'ever_tested_positive.required' => 'Specify whether patient has ever been covid 19 positive. Check row '.$count,
					'is_vaccinated.in' => 'The only accepted values for have you been vaccinated are: Yes or No. Check row '.$count,
					'vaccine_type.in' => 'The only accepted values vaccination types are'. implode(',', $this->vaccine_types).' Chech row'.$count,
					'is_vaccinated.required' => 'The the vaccination status. Check row '.$count,
					]
					);
					if($validator->fails()) {
						$messages = $validator->errors();
						$return_msg = array();
						foreach ($messages->all() as $message) {
							array_push($return_msg, $message);
						}
						// dd($return_msg);
						$error_messages[] = $return_msg;
					}else{
						//dd('created object');
						Result::Create($result_data);
					}

				}
			}

			$count++;
			//do validate

		}
		return ['failed_arr' => $error_messages];
		//return ['failed_arr' => $failed_array,'exists_array' => $exists_array];
	}

	private function save_rdt_file_results($file_path,$original_file_name,$used_file_name){
		$file = fopen($file_path, 'r');
		 //dd(\DB::getDatabaseName());
		$count = 0;
		$failed_array = [];
		$exists_array = [];
		$ref_lab_name = \MyHTML::getRefLabName(\Auth::User()->facilityID);

		$error_messages = [];

		while( ($row = fgetcsv($file)) !== FALSE){
			//dd($row);
			if($count){
				$row_data[]=$row;
				$result_data = array();
				//load row data in the corresponding db columns
				list(
				$result_data['specimen_ulin'],
				$result_data['case_name'],
				$result_data['age_years'],
				$result_data['sex'],
				$result_data['district'],
				$result_data['sentinel_site'],				
				$result_data['date_of_collection'],
				$result_data['who_is_being_tested'],
				$result_data['sample_type'],
				$result_data['result'],
				$result_data['pcr_required'],
				) = $row;
				unset($result_data['worksheet_no']);
				unset($result_data['pcr_required']);
				//add columns necessary but were not part of the csv
				$result_data['ref_lab'] = \Auth::User()->facilityID;
				$result_data['ref_lab_name'] = $ref_lab_name;
				$result_data['uploaded_by'] = \Auth::User()->id;
				$result_data['is_released'] = 1;
				$result_data['original_file_name'] = $original_file_name;
				$result_data['used_file_name'] = $used_file_name;
				$result_data['show_result'] = 1;
				$result_data['test_method'] = 'RDT';
				$result_data['patient_district'] = $result_data['district'];
				//reconstruct date fields - to be in the required format
				$result_data['date_of_collection'] = \MyHTML::reconstructDate(trim($result_data['date_of_collection']),1);
				$result_data['sample_received_on'] = $result_data['date_of_collection'];
				$result_data['test_date'] = trim($result_data['date_of_collection']);
				$result_data['type_of_site'] = 'Health Facility';
				$result_data['nationality'] = 'Ugandan';	
				$result_data['patient_id'] = $result_data['specimen_ulin'];				
				$result_data['serial_number_batch'] = $result_data['patient_id'];
				$result_data['age_years'] = (int)$result_data['age_years'];
				$result_data['result'] = ucfirst(strtolower(trim($result_data['result'])));
				
				$result_data['is_vaccinated'] = $row[12];
				$result_data['vaccine_type'] = $row[13];
				$result_data['ever_tested_positive'] = $row[14];
				//dd($result_data);
				// only pcr result is specified, enter the pcr results
				if($result_data['result'] == ''){
					$result_data['result'] = $row[11];
					$result_data['test_method'] = 'PCR';
				}

				//add custom date rules
				Validator::extend('before_or_equal', function($attribute, $value, $parameters) {
					//$attribute - collection date;
					//$value - value of collection date
					//value being compared - reception date;
					return strtotime($parameters[0]) >= strtotime($value);
				});
				Validator::extend('after_or_equal', function($attribute, $value, $parameters) {
					//$attribute - collection date;
					//$value - value of collection date
					//value being compared - reception date;
					//echo $parameters[0];
					//dd(($value));
					return strtotime($parameters[0]) <= strtotime($value);
				});
								
				$this->accepted_results = array('Positive', 'Negative');
				$this->yes_no_options = array('Yes','No','Unknown');
				$this->vaccine_types = array('AstraZeneca', 'Pfizer', 'Moderna', 'Sinopham', 'Sinovac','Johnson & Johnson', 'Johnson and Johnson');

				$validator = Validator::make($result_data, [
				'specimen_ulin' => 'required|unique:results',
				'case_name' => 'required',
				'district' => 'required',
				'date_of_collection'=>"required|date|before_or_equal:".$result_data['sample_received_on']
				."|before_or_equal:".date('Y-m-d H:i:s')."|after_or_equal:".date('1970-01-02 00:00:00'),
				'sample_received_on'=>"required|date|before_or_equal:".$result_data['test_date']."|before_or_equal:".date('Y-m-d H:i:s')."|after_or_equal:".date('1970-01-01'),
				'test_date'=>'required|date|before_or_equal:'.date('Y-m-d H:i:s')."|after_or_equal:".date('1970-01-01'),
				'result' => 'required|in:' . implode(',', $this->accepted_results),
				'ever_tested_positive' => 'required|in:' . implode(',', $this->yes_no_options),
				'is_vaccinated' => 'required|in:' . implode(',', $this->yes_no_options),
				'vaccine_type' => 'in:' . implode(',', $this->vaccine_types),
				],
				[
				'specimen_ulin.required' => 'The patient Id is required for row '.$count,
				'district.required' => 'The swabbing district is required for row '.$count,
				'specimen_ulin.unique' => 'The patient Id in row '.$count.' is already used',
				'case_name.required' => 'The name is required for row '.$count,
				'date_of_collection.before_or_equal' => "The collection date cannot be greater than reception date or today's date for row ".$count,
				'date_of_collection.after_or_equal' => "enter a correct collection date row ".$count,
				'sample_received_on.before_or_equal' => "The reception date cannot be greater than the test date or today's date for row ",
				'sample_received_on.after_or_equal' => "Enter correct date format for date of reception for row ",
				'test_date.before_or_equal' => "The test date cannot be greater today's date for row ".$count,
				'test_date.after_or_equal' => "Enter correct test date value row ".$count,
				'result.in' => 'The only accepted values for result are: Positive, Negative. Check row '.$count,
				'is_vaccinated.in' => 'The only accepted values for have you been vaccinated are: Yes, No. Check row '.$count,
				'vaccine_type.in' => 'The only accepted values vaccination types are'. implode(',', $this->vaccine_types).' Chech row'.$count,
				'is_vaccinated.required' => 'The the vaccination status. Check row '.$count,
				]
				);
				if($validator->fails()) {
					$messages = $validator->errors();
					$return_msg = array();
					foreach ($messages->all() as $message) {
						array_push($return_msg, $message);
					}
					 dd($return_msg);
					$error_messages[] = $return_msg;
				}else{
					//dd($result_data);
					Result::Create($result_data);
					if($row[9] !='' && array_key_exists(11,$row) && (strtolower($row[11]) == 'positive' || strtolower($row[11]) == 'negative')){
						
						$result_data['result'] = $row[11];
						$result_data['test_method'] = 'PCR';
						$result_data['specimen_ulin'] = 'PCR_'.$result_data['specimen_ulin'];
						Result::Create($result_data);
					}
				}
				

			}
			$count++;
			//do validate

		}
		//dd($error_messages);
		return ['failed_arr' => $error_messages];
		//return ['failed_arr' => $failed_array,'exists_array' => $exists_array];
	}

	private function generateMessageFromArray($error_messages){

		$error_msg = '<ul>';
			foreach($error_messages as $msg){
				foreach($msg as $key => $value){
					$error_msg .= '<li>'.$value.'</li>';
				}
			}

			$error_msg .= '</ul>';
			return $error_msg;
		}
		public function approve_retain(){
			$page_type = \Request::get("type");
			$id = \Request::get("id");
			if($page_type == 'approve'){
				$query = "UPDATE results SET is_released = 1 WHERE id=$id";
				\DB::unprepared($query);
				$success_message = "Result approved successfully";
			}
			if($page_type == 'retain'){
				$query = "UPDATE results SET is_released = 2 WHERE id=$id";
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
					$query = "UPDATE results SET is_released = 1 WHERE id IN($samples_str)";
					\DB::unprepared($query);
					$success_message = "Results retained successfully";
				}
			}

			//return \Redirect::back()->withInput()->withFlashMessage('Login Failed');
			if($page_type == 'edit'){
				$result = Result::findOrFail($id);
				$genders = ['Male'=>'Male', 'Female'=>'Female'];
				$result_values = ['Positive'=>'Positive', 'Negative'=>'Negative'];
				$result_status = ['0'=>'Pending Approval', '1'=>'Approved','2'=>'Retained'];
				return View('outbreaks.edit_result',compact('page_type','result','genders','result_values','result_status'));
			}

			return \Redirect::back()->with('success',$success_message);

		}
		public function update_result(){
			//update the object
			$result = Result::findOrFail(\Request::get('id'));
			$result->date_of_collection = \Request::get("date_of_collection");
			$result->sentinel_site = \Request::get("sentinel_site");
			$result->sentinel_site_other = \Request::get("sentinel_site_other");
			$result->patient_id = \Request::get("patient_id");
			$result->case_name = \Request::get("case_name");
			$age = \Request::get("age_years");
			$result->age_years = $age?$age:0;
			$result->sex = \Request::get("sex");
			$result->result = \Request::get("result");
			$result->test_date = \Request::get("test_date");
			$result->is_released = \Request::get("is_released");
			$result->save();
			$page_type = \MyHTML::is_eoc()?1:0;
			return \Redirect::Intended('/outbreakrlts/list?type='.base64_encode($page_type))->with('success','Result updated successfully');
		}

		//CSV returning positive cases; used for email alert
		public function createCSV()
		{
			$date = date('Y-m-d His');

			$query = "select case_name,sex, age_years, case_contact,who_is_being_tested, patient_district, patient_id, sentinel_site,
			date_of_collection,district,ref_lab_name, result, test_date
			from results where result = 'positive' AND is_classified = 0  AND sentinel_site NOT LIKE '%cabinet%' AND sentinel_site NOT LIKE '%Cabinet%'
			AND patient_district NOT LIKE '%stec%' AND date(test_date) = date (NOW()-interval 1 day)";

			$patients = \DB::select($query);
			// dd($patients);

			if(sizeof($patients) <= 0){
				//exit to avoid sending blank CSV
				exit("Terminating running process....");
			}

			else{

				header('Content-Type: text/csv; charset=utf-8');
				//header without attachment; this instructs the function not to download the csv,
				header("Content-Disposition: filename=Covid_data_$date.csv");
				//temporarily  open a file and store it in a temp file using php's wrapper function php://temp
				$output = fopen('php://temp', 'w');

				//state headers / column names for the csv
				$headers = array('Name','Sex','Age','Phone Contact','Reason for testing','Village and District of residence','Case No','Sampling site or facility',
				'Sampling Date','Sampling District','Testing Lab', 'Result','Test Date');

				//write the headers to the opened file
				fputcsv($output, $headers);

				//parse data to get rows
				foreach ($patients as $patient) {
					$row=array(
					$patient->case_name,
					$patient->sex,
					$patient->age_years,
					$patient->case_contact,
					$patient->who_is_being_tested,
					$patient->patient_district,
					$patient->patient_id,
					$patient->sentinel_site,
					$patient->date_of_collection,
					$patient->district,
					$patient->ref_lab_name,
					$patient->result,
					date('Y-m-d',strtotime($patient->test_date)),
					);

					//write the data to the opened file;
					fputcsv($output, $row);
				}

				//rewind is a php function that sets the pointer at begining of the file to handle the streams of data
				rewind($output);
				//instead of downloading the data, it streams it to a buffer file
				return stream_get_contents($output);
			}
		}

		public function sendEmail(){

			//send Summary of results
			Mail::send('covid_results.email_body', array(''),
			function($message){
				$message->to(explode(',', env('EMAILS')))->subject('Covid-19 Positive Stats')
				->attachData($this->createCSV(), "covid-Positives-alert.csv");
			});

			$array = Result::where('email_sent', '=', 1)->where('result','=','Positive')->get();

			if(sizeof($array) > 0){

				foreach ($array as  $value) {
					// code...
					$sql = "update results set email_sent = 1 where id = ". $value->id;
					$s = \DB::select($sql);
				}

			}

			else{
				return "No results out here buddy";
			}
		}

		// public function validateQrCode($result_id){
		//
		// 	try{
		//
		// 		//decipher ID
		// 		$decrypted_id = Crypt::decrypt($result_id);
		// 		//don't show results without receipt number984
		// 		$data = Result::where('id', $decrypted_id)->get()->first();
		// 		//if pepurpose is travel,
		// 		if(strtolower($data['attributes']['who_is_being_tested']) == 'traveller' && $data['attributes']['receipt_number'] == ''){
		// 			echo "<script>alert('UNKNOWN RESULT: Your QR-Code scan did not match any record');</script>";
		// 		}else{
		// 			$test_date = $data['attributes']['test_date'];
		// 			$today = date('Y-m-d');
		// 			$datetime1 = strtotime($test_date); // convert to timestamps
		// 			$datetime2 = strtotime($today); // convert to timestamps
		// 			$days = (int)(($datetime2 - $datetime1)/86400); // will give the difference in days , 86400 is the timestamp difference of a day
		//
		// 			if($data['attributes']['result'] == "Positive"){
		// 				echo "<script>alert('POSITIVE result detected!!');</script>";
		// 			}
		// 			elseif($days < 14 && $data['attributes']['result'] != "Positive"){
		// 				echo "<script>alert('Name: ".$data['attributes']['case_name']." Age.: ".$data['attributes']['age_years']." years Result: ".$data['attributes']['result']." Test Date: ".$data['attributes']['test_date']." Result No. ".$data['attributes']['patient_id']." Testing Laboratory: ".$data['attributes']['ref_lab_name']." Valid Certificate');</script>";
		// 			}
		// 			elseif($days > 14 && strtolower($data['attributes']['result']) != "Positive"){
		// 				echo "<script>alert('Certificate Expired ".$days." days ago');</script>";
		// 			}
		// 		}
		//
		// 	}catch(DecryptException $e){
		// 		echo "<script>alert('UNKNOWN RESULT: Your QR-Code scan did not match any record');</script>";
		//
		// 	}
		// }

		//used by results verifier APP
		public function validateQrCode($result_id){

			try{

				$decrypted_id = Crypt::decrypt($result_id);
//dd($decrypted_id);
				//don't show results without receipt number984
				$data = Result::where('id', $decrypted_id)->get()->first();

				//if pepurpose is travel,
				//if(strtolower($data['attributes']['who_is_being_tested']) == 'traveller' && $data['attributes']['receipt_number'] == ''){
				//	echo "<script>alert('UNKNOWN RESULT: Your QR-Code scan did not match any record');</script>";
				//}else{
					$test_date = $data['attributes']['test_date'];
					$today = date('Y-m-d');
					$datetime1 = strtotime($test_date); // convert to timestamps
					$datetime2 = strtotime($today); // convert to timestamps
					$days = (int)(($datetime2 - $datetime1)/86400); // will give the difference in days , 86400 is the timestamp difference of a day

					$pdf = \PDF::loadView('covid_results.validate', compact('data', 'days'));
					return $pdf->stream('Covid19_result.pdf');

				//}

			}catch(DecryptException $e){
				echo "<script>alert('UNKNOWN RESULT: Your QR-Code scan did not match any record');</script>";

			}
		}
			public function airportResult()

		{

			//get results from 3013
			// $results = Result::where('ref_lab',2901)->where('download_counter',0)->get(['case_name','passport_number']);

				// return Datatables::of($results)->make(true);
			return Datatables::collection(Result::where('ref_lab',3013)->where('is_printed',1)->get(['case_name','passport_number']))->make(true);
		}

		public function airportMarquee()

		{

			$results = Result::where('ref_lab', 2901)->where('is_printed',0)->get(['case_name','passport_number']);
			return view("covid_results.airport_marquee",compact('results'));

		}
	}
