<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CovidResultHistory extends \Eloquent {



	protected $table = 'results_history';
	protected $guarded = array('AutoID');


	public $timestamps = false;

}