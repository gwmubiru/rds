<?php namespace App\Http\Controllers;
use View;
use App\Models\Batch;
use App\Models\Facility;
use App\Models\District;
use App\Models\Location\Hub;
use App\Models\Covid;
use App\Models\Interviewer;
use App\Models\CovidSamples;
use Session;
use Validator;
use Lang;
use Input;
use Carbon;
use Redirect;
use yajra\Datatables\Facades\Datatables;
use DB;
use Auth;

class AccessionController extends Controller {


	private function getCovidData(){
		$patients = Covid::get();
		$districts =  MyHTML::array_merge_maintain_keys([''=>''],District::where('id', '>', '0')->pluck('district', 'id'));
		$facilities=  MyHTML::array_merge_maintain_keys([''=>''], Facility::where('id', '>', '0')->pluck('facility', 'facility'));
		$nationality =  MyHTML::array_merge_maintain_keys([''=>''],DB::table('nationalities')->pluck('nationality','nationality'));
		$poe =  MyHTML::array_merge_maintain_keys([''=>''],Facility::where('facilityLevelID', '=', '14')->pluck('facility','facility'));

		return [
			'poe'=>$poe,
			'patients'=>$patients,
			'districts'=>$districts,
			'facilities'=>$facilities,
			'nationality'=>$nationality,
		];
	}

	public function accession($id){

		$patient = Covid::findOrFail($id);
		$covidData=$this->getCovidData();

		$pID = $patient['attributes']['id'];
		$sample = CovidSamples::where('patient_id','=',$pID)->get();

		{
			$facilities = $covidData['facilities'];
			$districts = $covidData['districts'];

			return view("covid.lab_accession.accession", compact('facilities','districts','patient','sample'));
		}
	}

	public function labAccession($id){
		$data = \Request::all();

		$specimen_ulin = $data['specimen_ulins'];
		$patient_ids = $data['patient_ids'];

		$counter = 0;
		foreach ($specimen_ulin as $ulin) {
			\DB::statement("update covid_samples set specimen_ulin = '".$ulin. "' ,is_accessioned = 1, status = 0 where patient_id = ".$patient_ids[$counter]);
			$counter++;
		}

		// $patient = Covid::findOrFail($id);
		// $pID = $patient['attributes']['id'];
		//
		// $samples = CovidSamples::where('patient_id','=',$pID)->first();
		// $sample = [
		// 'patient_id' => $pID,
		// 'caseID' => Input::get('caseID'),
		// 'specimen_ulin' => Input::get('specimen_ulin'),
		// 'specimen_collection_date' => Input::get('specimen_collection_date'),
		// 'is_accessioned' => 1,
		// 'status' => 0,
		// ];
		//
		// $samples = CovidSamples::updateOrCreate(['patient_id' => $pID],$sample);

		return redirect('suspected/cases')->with('msge',trans('general.save_success'));
	}

	public function getAccessioned(){

		$n = Covid::groupBy('covid_patients.epidNo')->leftJoin('covid_results','covid_patients.id','=','covid_results.patient_id')
		->Join('covid_samples', 'covid_patients.id', '=', 'covid_samples.patient_id')
		->select('covid_patients.id','covid_patients.epidNo','patient_surname','patient_firstname','sex','age','nationality','patient_contact',
		'nameWhere_sample_collected_from','covid_patients.request_date','covid_results.test_result','covid_results.test_date','covid_samples.specimen_ulin')
		->where('covid_samples.status','=',0)
		->where('covid_samples.is_accessioned', '=', 1)->get();

		if(Auth::user()->ref_lab == '23'){

			return Datatables::of($n)
			->addColumn('action', function($row) {
				return '	<select style="width: 100%;" name="results[]">
						<option value="" selected="selected"></option>
						<option value="Negative">Negative</option>
						<option value="Positive">Positive</option>
					</select>';
						})->addColumn('hidden', function($row) {
				return '<input type="text" name="patient_ids[]" class="form-control hidden" value= "' .  $row->id . '" style="color:#337ab7"/>';
			})->make(true);
		}

		else{
			return Datatables::of($n)->addColumn('action', function($row) {
				\Log::info($row);
				$link = '<a href="' . route("editForm", $row->id) . '" class="btn btn-sm btn-link pull-left"><i class="fa fa-edit"></i>Update</a>';
				if(empty($row->test_result)){
					$link .= '<br><a href="' . route("enter_results", $row->id) . '" class="btn btn-sm btn-link pull-left"><i class="fa fa-edit"></i>Enter Results</a>';
				}
				return $link;
			})->make(true);
		}
	}

//list samples with results
	public function hasResults(){

		$n = Covid::groupBy('covid_patients.epidNo')->leftJoin('covid_results','covid_patients.id','=','covid_results.patient_id')
		->Join('covid_samples', 'covid_patients.id', '=', 'covid_samples.patient_id')
		->select('covid_patients.id','covid_patients.epidNo','patient_surname','patient_firstname','sex','age','nationality','patient_contact','specimen_type',
		'nameWhere_sample_collected_from','covid_patients.request_date','covid_results.test_result','covid_results.test_date','covid_samples.specimen_ulin')
		// ->where('covid_results.patient_id', '=', 'covid_samples.patient_id')
		->where('covid_samples.status', '=',1)->get();


		if(Auth::user()->ref_lab == '23'){

			return Datatables::of($n)->addColumn('action', function($row) {
				\Log::info($row);
				$link = '<a href="' . route("accession", $row->id) . '" class="btn btn-md btn-link pull-left"><i class="fa fa-edit"></i>Add Lab#</a>';

				// $link .= '<br><a href="' . route("enter_results", $row->id) . '" class="btn btn-md btn-link pull-center"><i class="fa fa-edit"></i>Edit</a>';

				return $link;
			})->make(true);
		}
		else{
			return Datatables::of($n)->addColumn('action', function($row) {
				\Log::info($row);
				$link = '<a href="' . route("editForm", $row->id) . '" class="btn btn-sm btn-link pull-left"><i class="fa fa-edit"></i>Update</a>';
				if(empty($row->test_result)){
					$link .= '<br><a href="' . route("enter_results", $row->id) . '" class="btn btn-sm btn-link pull-left"><i class="fa fa-edit"></i>Enter Results</a>';
				}
				return $link;
			})->make(true);
		}
	}

	// public function massResult(Request $request){
	//
	// 	dd(Request::all());
	//
	// 			// $data = \Request::all());
	// 			$tester = '';
	// 			$test_date = '';
	// 			$sample_id ='';
	//
	//
	// 					$results = $data['results'];
	// 					$patient_ids = $data['patient_ids'];
	//
	// 					$counter = 0;
	// 					foreach ($results as $result) {
	// 						\DB::statement("insert into covid_results values '".$tester. "' , '".$result. "' , '".$test_date."' , '".$sample_id."', '".$patient_id."','".$is_released."',
	// 						'".$is_printed."','".$original_file_name."','".$used_file_name."', '".$uploaded_by."','".$created_at."','".$updated_at."',
	// 						'".$result_type."','".$testedBy."','".$lab_tech_phone."','".$case_name."','".$case_phone."','".$lab_number."','".$test_method."',,is_accessioned = 1, status = 0 where patient_id = ".$patient_ids[$counter]);
	// 						$counter++;
	// 					}
	//
	// 					$result_array = [
	// 						'testedBy' =>   \Request::get('tested_by'),
	// 						'lab_tech_phone' =>   \Request::get('lab_tech_phone'),
	// 						'test_result' =>  \Request::get('test_result'),
	// 						'test_date' =>  \Request::get('result_date'),
	// 						'sample_id' =>  \Request::get('sample_id'),
	// 						'patient_id' => \Request::get('patient_id'),
	// 						'test_method' => \Request::get('test_method'),
	// 						'is_released' =>  1,
	// 						'is_printed' =>  0,
	// 						'original_file_name' =>  0,
	// 						'used_file_name' =>  0,
	// 						'uploaded_by' => Auth::user()->id,
	// 						//final result
	// 						'result_type' =>  1,
	// 					];
	// 					// dd($result);
	// 					$update_accession = [
	// 						'patient_id' => \Request::get('patient_id'),
	// 						'status' => 1,
	// 					];
	//
	// 					//updatess samples table if sample has result
	// 					CovidSamples::updateOrCreate(['patient_id' =>\Request::get('patient_id')], $update_accession);
	//
	// 					CovidResult::updateOrCreate(['sample_id' => \Request::get('sample_id')],$result_array);
	// 					return \Redirect::Intended('/suspected/cases')->with('success','Result added successfully');
	//
	//
	// }

}
