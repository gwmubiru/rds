<?php   namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logistic extends Model {

	protected $table = 'logistics';
	public $timestamps = 'true';
	protected $dates = array('date_submitted');

	protected $fillable = ['commodity',
													'commodity_category',
													'opening_balance',
													'quantity_received',
													'total_consumption',
													'adjustment',
													'closing_balance',
													'comment',
													'reporting_period',
													'date_submitted'
												];
}
