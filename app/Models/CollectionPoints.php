<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class CollectionPoints extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	use Authenticatable, CanResetPassword;

	protected $table = 'collectionpoint';
	public $timestamps = 'true';

		protected $fillable = [
			'patient_id',
			'health_facility',
			'facility_sub_district',
			'facility_district',
			'pointOfEntry'
			];

}
