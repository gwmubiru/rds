<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EvdPrintingLog extends \Eloquent {

	protected $table = 'evd_printing_logs';
	protected $guarded = array('AutoID');

	public $timestamps = true;

}
