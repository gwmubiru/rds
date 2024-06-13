<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class WorksheetTube extends Model {

	protected $table = 'worksheet_tubes';

	protected $fillable = ['worksheet_id', 'tube'];
	public $timestamps = true;
}
