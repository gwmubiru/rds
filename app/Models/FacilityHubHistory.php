<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class FacilityHubHistory extends Model {

	protected $table = 'facility_hub_history';

	public static $rules = [
		'facility_id' => 'required',
		'hub_id' => 'required'
	];

	protected $fillable = ['facility_id','hub_id','start_date','end_date','created','createdby'];

	public $timestamps = false;

}
?>
