<?php namespace App\Models\covidmodels;

use Illuminate\Database\Eloquent\Model;

class CollectionPoints extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	use Authenticatable, CanResetPassword;

	protected $table = 'CollectionPoint';
	public $timestamps = 'true';

		protected $fillable = [
			'patient_id',
			'health_facility',
			'facility_sub_district',
			'facility_district',
			'pointOfEntry'
			];

}
