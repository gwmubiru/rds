<?php namespace App\Http\Controllers;

use View;
use App\Models\Covid;
use App\Models\CovidSamples;
use App\Models\HatResults;
use App\Models\District;
use App\Models\DistrictFileUpload;
use Illuminate\Http\Request;
use App\Models\Result;
use Session;
use Lang;
use Input;
use Carbon;
use Redirect;
use DB;
use Auth;
use Mail;
use App\Closet\MyHTML as MyHTML;

class HatController extends Controller {


	public function index()
	{
		// dd(\Request::all());
		$districts = MyHTML::array_merge_maintain_keys([''=>''],District::whereIn('id',[2,7,48,66,87,130,133,134])->orderBy('district','ASC')->pluck('district','id'));

		$hat_district_summaries = \DB::select("select d.district, count(p.id) as total_tests, max(p.created_at) as last_submission_date,
		sum(CASE WHEN hr.patientHatRdtResult = 'negative' THEN 1 ELSE 0 END) AS hat_rdt_neg,
		sum(CASE WHEN hr.patientHatRdtResult = 'positive' THEN 1 ELSE 0 END) AS hat_rdt_pos,
		sum(CASE WHEN cr.test_result = 'positive' THEN 1 ELSE 0 END) AS cov_pos,
		sum(CASE WHEN cr.test_result = 'negative' THEN 1 ELSE 0 END) AS cov_neg,
		sum(CASE WHEN p.id not in(select patientId from hat_results where patientId in(select id from covid_patients)  ) THEN 1 ELSE 0 END) AS has_no_result
		from covid_patients p left join districts d on p.patient_district = d.id
		left join covid_results cr on cr.patient_id = p.id
		left join hat_results hr on hr.patientId = p.id
		where p.hat_screened = 1 and d.id in (2,7,48,66,87,130,133,134) group by d.id ");

		$hat_results = HatResults::leftjoin('covid_patients','hat_results.patientId','=','covid_patients.id')
		->leftjoin('users','covid_patients.createdby','=','users.id')
		->leftjoin('villages','covid_patients.patient_village','=','villages.id')
		->leftjoin('parishes','covid_patients.patient_parish','=','parishes.id')
		->leftjoin('subcounties','covid_patients.patient_subcounty','=','subcounties.id')
		->leftjoin('districts','covid_patients.patient_district','=','districts.id')
		->orderBy('hat_results.created_at', 'ASC')->get();

		$covid_results = Result::leftjoin('covid_patients','results.patient_id','=','covid_patients.epidNo')
		->leftjoin('villages','covid_patients.patient_village','=','villages.id')
		->leftjoin('parishes','covid_patients.patient_parish','=','parishes.id')
		->leftjoin('subcounties','covid_patients.patient_subcounty','=','subcounties.id')
		->leftjoin('districts','covid_patients.patient_district','=','districts.id')
		->where('covid_patients.hat_screened','1')->get();

		$rdt_positives = HatResults::leftjoin('covid_patients','hat_results.patientId','=','covid_patients.id')
		->leftjoin('villages','covid_patients.patient_village','=','villages.id')
		->leftjoin('parishes','covid_patients.patient_parish','=','parishes.id')
		->leftjoin('subcounties','covid_patients.patient_subcounty','=','subcounties.id')
		->leftjoin('districts','covid_patients.patient_district','=','districts.id')
		->where('patientHatRdtResult','positive')->orderBy('hat_results.created_at', 'DESC')->get();

		$sex_age_aggregate = \DB::select("select count(p.id) as total_screened, v.village,p.created_at,
		sum(CASE WHEN p.age between 0 and 14 and p.sex = 'male' THEN 1 ELSE 0 END) AS m_b14,
		sum(CASE WHEN p.age between 0 and 14 and p.sex = 'female' THEN 1 ELSE 0 END) AS f_b14,
		sum(CASE WHEN p.age between 15 and 45 and p.sex = 'male' THEN 1 ELSE 0 END) AS m_a15_b45,
		sum(CASE WHEN p.age between 15 and 45 and p.sex = 'female' THEN 1 ELSE 0 END) AS f_a15_b45,
		sum(CASE WHEN p.age >= 46 and p.sex = 'male' THEN 1 ELSE 0 END) AS m_a46,
		sum(CASE WHEN p.age >= 46 and p.sex = 'female' THEN 1 ELSE 0 END) AS f_a46,
		sum(CASE WHEN hr.patientHatRdtResult = 'positive' and p.sex = 'male' THEN 1 ELSE 0 END) AS m_rdt_pos,
		sum(CASE WHEN hr.patientHatRdtResult = 'positive' and p.sex = 'female' THEN 1 ELSE 0 END) AS f_rdt_pos,
		sum(CASE WHEN hr.patientHCTResult = 'positive' and p.sex = 'male' THEN 1 ELSE 0 END) AS m_ctc_pos,
		sum(CASE WHEN hr.patientHCTResult = 'positive' and p.sex = 'female' THEN 1 ELSE 0 END) AS f_ctc_pos,
		sum(CASE WHEN hr.patientMaectResult = 'positive' and p.sex = 'male' THEN 1 ELSE 0 END) AS m_maect_pos,
		sum(CASE WHEN hr.patientMaectResult = 'positive' and p.sex = 'female' THEN 1 ELSE 0 END) AS f_maect_pos,
		sum(CASE WHEN hr.patientHatCase = 'positive' and p.sex = 'male' THEN 1 ELSE 0 END) AS m_hat_case,
		sum(CASE WHEN hr.patientHatCase = 'positive' and p.sex = 'female' THEN 1 ELSE 0 END) AS f_hat_case
		from covid_patients p left join villages v on p.patient_village = v.id
		left join hat_results hr on hr.patientId = p.id where p.patient_village is not null and p.patient_village != ''
		and  p.hat_screened = 1 and p.age is not null and p.age != '' group by p.patient_village,p.created_at");


		return view("hat.index", compact('rdt_positives','hat_results','hat_district_summaries','districts','sex_age_aggregate','covid_results'));
	}

	public function HatCsv(){

		$fro = \Request::get('fro');
		$to = \Request::get('to');
		$screening_district = \Request::get('screening_district') != "" ? "and p.facility_district = ".\Request::get('screening_district') : "";

		$query = "SELECT p.*,s.specimen_collection_date, s.specimen_type,s.test_type,pd.district as patientDistrict,pd.district as facilityDistrict,
		pv.village, pp.parish, ps.subcounty,hs.*
		FROM covid_patients as p
		LEFT JOIN covid_samples s ON(s.patient_id = p.id)
		LEFT JOIN districts pd ON (pd.id = p.patient_district)
		LEFT JOIN villages pv ON (pv.id = p.patient_village)
		LEFT JOIN parishes pp ON (pp.id = p.patient_parish)
		LEFT JOIN subcounties ps ON (ps.id = p.patient_subcounty)
		LEFT JOIN hat_results hs ON (hs.patientId = p.id)
		where p.hat_screened = 1  AND p.created_at between '$fro' and '$to'".$screening_district;

		$patients = \DB::select($query);


		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename="."HAT_data_$fro"."_$to.csv");
		$output = fopen('php://output', 'w');
		$headers = array(
		'SAMPLE ID/BARCODE',
		'PATIENT NAME',
		'SEX',
		'AGE',
		'NATIONALITY',
		'PATIENT CONTACT',
		'PATIENT NOK',
		'NOK CONTACT',
		'PURPOSE FOR TESTING',
		'IS PATIENT SYMPTOMATIC?',
		'SYMPTOM ONSET DATE',
		'SYMPTOMS',
		'KNOWN UNDERLYING CONDITION',
		'INTERVIEWER NAME',
		'INTERVIEWER FACILITY',
		'FACILITY CODE',
		'INTERVIEWER PHONE',
		'INTERVIEWER EMAIL',
		'PATIENT DISTRICT',
		'PATIENT VILLAGE',
		'PATIENT SUBCOUNTY',
		'PATIENT PARISH',
		'SWAB DISTRICT',
		'SAMPLE TYPE',
		'COLLECTION DATE',
		'PATIENT HAT RDT RESULT',
		'PATIENT GLAND PUNCTURE',
		'PATIENT THICK BLOOD SMEARS',
		'PATIENT HCT RESULT',
		'PATIENT MAECT RESULT',
		'PATIENT HAT CASE',
		'PATIENT HAT CASE STAGE',
		'PATIENT REFERRAL SITE',
		'PATIENT REMARKS',
		'TEST DATE',
		);

		fputcsv($output, $headers);
		foreach ($patients as $patient) {
			$row=array(
			$patient->epidNo,
			$patient->patient_surname ? $patient->patient_surname.' ' .$patient->patient_firstname : '',
			$patient->sex,
			$patient->age,
			$patient->nationality,
			$patient->patient_contact,
			$patient->patient_NOK,
			$patient->nok_contact,
			$patient->who_being_tested,
			$patient->patient_symptomatic,
			$patient->symptomatic_onset_date,
			$patient->symptoms,
			$patient->known_underlying_condition,
			$patient->interviewer_name,
			$patient->interviewer_facility,
			$patient->facility_code,
			$patient->interviewer_phone,
			$patient->interviewer_email,
			$patient->patientDistrict,
			$patient->village,
			$patient->subcounty,
			$patient->parish,
			$patient->facilityDistrict,
			$patient->specimen_type,
			date("Y-m-d", strtotime($patient->specimen_collection_date)),
			$patient->patientHatRdtResult,
			$patient->patientGlandPuncture,
			$patient->patientThickBloodSmears,
			$patient->patientHCTResult,
			$patient->patientMaectResult,
			$patient->patientHatCase,
			$patient->patientHatCaseStage,
			$patient->patientReferralSite,
			$patient->patientRemarks,
			$patient->created_at,
			);
			fputcsv($output, $row);
		}
		fclose($output);
	}

	public function uploadDistrictFile(Request $request)
	{
		$file_formats = ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","text/csv","application/vnd.ms-excel"];

		if(in_array($_FILES['import_file']['type'],$file_formats)){

			if ($_FILES["import_file"]["size"] > 0) {
				$extension =  '.'.\Request::file('import_file')->getClientOriginalExtension();
				$dest_folder = public_path().'/uploads/HAT/districtAdminUnits';
				$dest_fileName = \Request::file('import_file')->getClientOriginalName();
				$uploaded_file =  \Request::file('import_file')->move($dest_folder, $dest_fileName);
				$file = new DistrictFileUpload;
				$file->file_name =  \Request::file('import_file')->getClientOriginalName();;
				$file->uploaded_by = $request->uploaded_by;
				$file->uploader_email = $request->uploader_email;
				$file->save();

				//send uploaded file to email
				Mail::send('hat.admin_units_email_body', array(''),
				function($message){
					$file_name =  \Request::file('import_file')->getClientOriginalName();
					$dest_fileName = \Request::file('import_file')->getClientOriginalName();
					$request = \Request::all();

					$f = $request['import_file'];
					$message->to(explode(',','benbyron24@gmail.com'))->subject('Uploaded Administrative Units')
					->attachData($f,"$file_name");
				});

				return \Redirect::back()->with('message','Results succesfully uploaded');
			}
			else {
				dd("You are trying to upload an empty file");
			}

		}
		else {
			dd("You're trying to upload an Unsupported file type. Only .xls, .xlsx or .csv file formats are accepted");
		}
		dd($fileName);
		// code...
	}
}

