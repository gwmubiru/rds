<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ResultHistory extends \Eloquent {


	protected $table = 'results_hist';
	protected $guarded = array('AutoID');

	public $timestamps = true;

}
