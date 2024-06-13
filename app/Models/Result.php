<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Result extends \Eloquent {


	protected $table = 'results';
	protected $guarded = array('AutoID');

	public $timestamps = true;

}