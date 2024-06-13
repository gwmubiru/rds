<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class isHealthCareWorker extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	* The database table used by the model.
	*
	* @var string
	*/
	use Authenticatable, CanResetPassword;

	protected $table = 'healthCareWorker';
	public $timestamps = 'true';

	protected $fillable = [
		'is_health_care_worker_being_tested',
		'health_care_worker_facility',
		'reason_for_healthWorker_testing'
	];

}
