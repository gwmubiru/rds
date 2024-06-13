<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Interviewer extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	use Authenticatable, CanResetPassword;

	protected $table = 'covid_interviewers';
	public $timestamps = 'true';


		public static $rules = [
			'interviewer_phone'=> ['required','digits:10','regex:/^(07\d{8})$/'],
			'interviewer_name' => 'required|max:50'
		];

		protected $fillable = [
			'interviewer_name',
			'interviewer_phone',
			'interviewer_facility',
			'interviewer_email'
			];

}
