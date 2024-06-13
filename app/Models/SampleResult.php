<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SampleResult extends \Eloquent {
	protected $table = 'sample_results';
	protected $guarded = array('AutoID');
	protected $fillable = [
		'worksheet_id',
		'sample_id',
		'patient_id',
		'worksheet_tube_id',
		'result1',
		'result2',
		'final_result',
		'ct_value',
		'original_file_name',
		'used_file_name',
		'created_by',
		'test_date'
	];

	public $timestamps = true;

}