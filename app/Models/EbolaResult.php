<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EbolaResult extends \Eloquent {


	protected $table = 'ebola_results';
	protected $guarded = array('AutoID');

	public $timestamps = true;

}
