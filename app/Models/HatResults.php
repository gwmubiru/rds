<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class HatResults extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	use Authenticatable, CanResetPassword;

	protected $table = 'hat_results';
	public $timestamps = 'true';

	public static $rules = [

	];

	protected $fillable = [
	'patientId',
	'patientHatRdtResult',
	'patientGlandPuncture',
	'patientThickBloodSmears',
	'patientHCTResult',
	'patientMaectResult',
	'patientHatCase',
	'patientHatCaseStage',
	'patientReferralSite',
	'patientRemarks',
	'tested_by'
		];

}
