<?php namespace App\Models\covidmodels;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class CovidSamples extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	use Authenticatable, CanResetPassword;

	protected $table = 'covid_samples';
	public $timestamps = 'true';

	public static $rules = [

	];

	protected $fillable = [
		'patient_id',
		'specimen_type',
		'specimen_collection_date',
		'specimen_ulin',
 		'testing_lab',
		'serial_number',
		'testing_lab',
		'sentToUvri',
		'request_date'
		];

}
