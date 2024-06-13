<?php namespace App\Models\covidmodels;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Covid extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	use Authenticatable, CanResetPassword;

	protected $table = 'covid_patients';
	public $timestamps = 'true';

	public static $rules = [
		'request_date'=> 'required',
		'patient_surname' => 'required',
		'patient_firstname' => 'required',
		'sex' => 'required',
		'age' => 'required'
	];

	protected $fillable = [
		'patient_surname',
		'patient_firstname',
		'sex',
		'dob',
		'age',
		'nationality',
		'passportNo',
		'patient_contact',
		'patient_village',
		'patient_parish',
		'patient_subcounty',
		'patient_district',
		'patient_NOK',
		'nok_contact',
		'epidNo',
		'caseID',
		'truckNo',
		'truckDestination',
		'truckEntryDate',
		'tempReading',
		'sampleCollected',
		'pointOfEntry',
		'health_facility',
		'request_date',
		'ulin',
		'serial_number',
		'where_sample_collected_from',
		'who_being_tested',
		'is_health_care_worker_being_tested',
		'health_care_worker_facility',
		'reason_for_healthWorker_testing',
		'isolatedPerson_test_day',
		'travel_out_of_ug_b4_onset',
		'destination_b4_onset',
		'return_date',
		'patient_symptomatic',
		'symptomatic_onset_date',
		'symptoms',
		'known_underlying_condition',
		'TravelToChina',
		'travelDateToChina',
		'travelDateFromChina',
		'stateVisited',
		'UgArrivalDate',
		'closeContact4',
		'closeContact5',
		'healthFacilityHistory',
		'acuteRespiratory',
		'additionalSigns',
		'diagnosis',
		'comorbid',
		'admitted',
		'admissionDate',
		'icuAdmitted',
		'intubated',
		'ecmo',
		'patientDied',
		'deathDate',
		'otherEtiology',
		'interviewer_name',
		'interviewer_facility',
		'interviewer_phone',
		'interviewer_email'
		];

}
