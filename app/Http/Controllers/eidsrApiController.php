<?php namespace App\Http\Controllers;

use View;
use App\Models\Covid;
use App\Models\CovidSamples;
use App\Models\CovidResult;
use Illuminate\Http\Request;
use Session;
use Lang;
use Input;
use Carbon;
use Redirect;
use DB;
use Auth;
class eidsrApiController extends Controller {


	//used to eIDSR data
	public function getEidsr(Request $request){
		$data = $request->all();

		$patient_data = [

			'interviewer_name' => array_key_exists('screenerName',$data) ? $data['screenerName'] : "",
			'specimen_collection_date' =>  array_key_exists('sampleCollectionDate',$data) ?  date('Y-m-d',strtotime($data['sampleCollectionDate'])) : "",
			'where_sample_collected_from' =>  array_key_exists('sampleCollectionLocation', $data) ? $data['sampleCollectionLocation'] : "",
			'nameWhere_sample_collected_from' => array_key_exists('organisation', $data) ? $data['organisation'] : "",
			'who_being_tested' => array_key_exists('typeOfPersonTested', $data) ?  $data['typeOfPersonTested'] : "",
			'patient_surname' =>  array_key_exists('fullName', $data) ? $data['fullName'] : "",
			'epidNo' => array_key_exists('barcode',$data) ? $data['barcode'] : "",
			'serial_number' =>  array_key_exists('formId', $data) ? $data['formId'] : "",
			'caseID' =>  array_key_exists('poeId', $data) ? $data['poeId'] : "", //(system generated uuid for patient)
			'dob' => array_key_exists('dob', $data) ? $data['dob'] : "",
			'sex' => array_key_exists('sex', $data) ? $data['sex'] : "",
			'passportNo' => array_key_exists('passportOrNInNo', $data) ? $data['passportOrNInNo'] : "",
			'patient_contact' => array_key_exists('casePhoneContact',$data) ? $data['casePhoneContact'] : "" ,
			'nationality' => array_key_exists('nationality', $data) ? $data['nationality'] : "",
			'UgArrivalDate' => array_key_exists('entryDate', $data) ? date('Y-m-d',strtotime($data['entryDate'])) : "",
			'truckNo' =>  array_key_exists('truckOrFlightNo', $data) ? $data['truckOrFlightNo'] : "",
			'origin' =>  array_key_exists('departure', $data) ? $data['departure'] : "", //(country of origin)
			'truckDestination' =>  array_key_exists('addressInUganda',$data) ? $data['addressInUganda'] : "", //(place where driver going to in Uganda)
			// 'quarantine' =>  $data['underQuarantine'],
			'patient_NOK' =>  array_key_exists('nokName',$data) ? $data['nokName'] : "",
			'nok_contact' =>  array_key_exists('nokPhone',$data) ? $data['nokPhone'] : "",
			'tempReading' =>  array_key_exists('temperature',$data) ? $data['temperature'] : "",
			'patient_symptomatic' => array_key_exists('freeFromSymptoms', $data) ?  $data['freeFromSymptoms'] : "",
			'known_underlying_condition' => array_key_exists('knownUnderlyingConditions', $data) ? $data['knownUnderlyingConditions'] : "",
			'reason_for_healthWorker_testing' =>  array_key_exists('reasonsForHWTesting', $data) ? $data['reasonsForHWTesting'] : "",
			'age' =>  array_key_exists('age', $data) ? $data['age'] : "",
			'age_units' =>  array_key_exists('ageUnits',$data) ? $data['ageUnits'] : ""
			];

			// dd($patient_data);
			//
			// $patient_data = [
			// 	"caseID" => $data['caseID'],
			// 	"age" => $data ['age'],
			// 	"sex" => $data['sex'],
			// 	"age_units" => $data['age_units'],
			// 	"patient_surname" => $data['patient_surname'],
			// 	"nationality" => $data['nationality'],
			// 	"tempReading" => $data['temperature'],
			// 	"truckNo" => $data['truckNo'],
			// 	"truckEntryDate" => date('Y-m-d',strtotime($data['truckEntryDate'])),
			// 	"truckDestination" => $data['truckDestination'],
			// 	"nameWhere_sample_collected_from" => $data['nameWhere_sample_collected_from'],
			// 	"passportNo" => $data['passportNo'],
			// 	"request_date" => date('Y-m-d', strtotime($data['request_date'])),
			// 	"dob" => date('Y-m-d', strtotime($data['dob'])),
			// 	"where_sample_collected_from" => $data['where_sample_collected_from'],
			// 	"createdby" => $data['createdby'],
			// 	"patient_village" => $data['patient_village'],
			// 	"patient_parish" => $data['patient_parish'],
			// 	"patient_subcounty" => $data['patient_subcounty'],
			// 	"nok_contact" => $data['nok_contact'],
			// 	"interviewer_facility" => $data['interviewer_facility'],
			// 	"interviewer_email" => $data['interviewer_email'],
			// 	"patient_contact" => $data['patient_contact'],
			// 	"epidNo" => $data['epidNo'],
			// 	"serial_number" => $data['serial_number'],
			// 	"UgArrivalDate" => date('Y-m-d', strtotime($data['UgArrivalDate'])),
			// 	"interviewer_name" => $data['interviewer_name'],
			// 	"interviewer_phone" => $data['interviewer_phone']
			// 	];

			Covid::updateOrCreate(['caseID' => $patient_data['caseID']],$patient_data);

			// $w = Covid::get();
			// foreach ($w as $key => $v) {
			// 	$pID = $v->id;
			// }
			//
			// $sample_info = [
			// 'patient_id' => $pID,
			// 'is_accessioned' => 0,
			// 'has_result' => 0,
			// 'specimen_type' =>  array_key_exists('sampleType',$data) ? $data['sampleType'] : "",
			// 'caseID' => array_key_exists('barcode',$data) ? $data['barcode'] : "",
			// 'specimen_collection_date' => array_key_exists('entryDate', $data) ? date('Y-m-d', strtotime($data['entryDate'])) : "0000-00-00 00:00",
			// ];
			//
			// CovidSamples::updateOrCreate(['patient_id' => $pID],$sample_info);


			\Log::info($data);
			return response()->json("Case details received in LIMS", 201);

		}
		public function sendResults(){

			$requests = \Request::getMethod();
			dd($requests);

			$results = Covid::get(['id','caseID','test_type','result_type','test_result','confirmed_disease']);
			return $results;
		}

		//used to receive RECDTS data
		public function getRecdtsData(Request $request){

			$data = $request->all();

			if(array_key_exists('data_id',$data) && $data['data_id'] == env('RECDTS_API_KEY'))
			{
				$patient_data = [
				'data_entry_date' => array_key_exists('data_entry_date', $data) ? $data['data_entry_date'] : "",
				'patient_surname' => array_key_exists('patient_name', $data) ? $data['patient_name'] : "",
				'sex' => array_key_exists('sex',$data) ? $data['sex'] : "",
				'age' => array_key_exists('sex',$data) ? $data['age'] : "",
				'dob' => $data['dob'] == '' ? '0000-00-00 00:00:00' : date('Y-m-d', strtotime($data['dob'])),
				'age_units' =>   array_key_exists('age_units', $data) ? $data['age_units'] : "",
				'nationality' => array_key_exists('nationality', $data) ? $data['nationality'] : "",
				'passportNo' =>  array_key_exists('passport_no', $data) ? $data['passport_no'] : "",
				'createdby' => array_key_exists('origin_uuid', $data) ? $data['origin_uuid'] : "",
				'caseID' =>  array_key_exists('case_id', $data) ? $data['case_id'] : "",
				'truckNo' => array_key_exists('truck_no', $data) ? $data['truck_no'] : "",
				'truckDestination' => array_key_exists('truck_destination', $data) ? $data['truck_destination'] : "",
				'tempReading' => array_key_exists('temp_reading', $data) ? $data['temp_reading'] : "",
				//'request_date' => date('Y-m-d', strtotime($data['request_date'])),
				'caseID' => array_key_exists('caseID', $data) ? $data['form_number'] : "",
				'where_sample_collected_from' => array_key_exists('sample_collection_site_type', $data) ? $data['sample_collection_site_type'] : "",
				'nameWhere_sample_collected_from' => array_key_exists('sample_collection_site_name', $data) ? $data['sample_collection_site_name'] : "",
				// => $data['collection_site_district'],
				'truckEntryDate' => array_key_exists('entry_date', $data) ? date('Y-m-d', strtotime($data['entry_date'])) : "0000-00-00 00:00:00" ,
				'interviewer_name' => array_key_exists('interviewed_by',$data) ? $data['interviewed_by'] : "",
				'interviewer_facility' => array_key_exists('interviewer_facility', $data) ? $data['interviewer_facility'] : "",
				'interviewer_phone' => array_key_exists('interviewer_phone', $data) ? $data['interviewer_phone'] : "",
				'interviewer_email' => array_key_exists('interviewer_email', $data) ? $data['interviewer_email'] : "",
				'epidNo' => array_key_exists('epidNo', $data) ?  $data['epidNo'] : ""
				];
				Covid::updateOrCreate(['caseID' => $patient_data['caseID']],$patient_data);

				\Log::info($data);
				//return response()->json("Driver details successfully received in UNHLS-CoV19 LIMS", 201);
			}

			else {
				return response()->json("Key Mismatch or key not found, connection declined", 405);
			}

			if(array_key_exists('specimen_type',$data))
			{
				$w = Covid::get();
				foreach ($w as $key => $v) {
					$pID = $v->id;
				}

				$sample_info = [
				'patient_id' => $pID,
				'is_accessioned' => 0,
				'specimen_type' => $data['specimen_type'],
				'caseID' => $data['case_id'],
				'specimen_collection_date' => date('Y-m-d', strtotime($data['request_date'])),
				];

				CovidSamples::updateOrCreate(['patient_id' => $pID],$sample_info);

				\Log::info($data);
				return response()->json("Sample & Client details successfully received in UNHLS-CoV19 LIMS", 201);
			}

			else {
				return response()->json("No sample info was found in request, sample details not received", 200);
			}
		}
	}
