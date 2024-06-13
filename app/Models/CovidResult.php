<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CovidResult extends \Eloquent {

	protected $table = 'covid_results';
	protected $guarded = array('AutoID');


	public $timestamps = true;

}