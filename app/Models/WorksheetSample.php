<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class WorksheetSample extends Model {

	protected $table = 'worksheet_samples';

	protected $fillable = ['locator_id', 'tube_id'];
	public $timestamps = false;
}
