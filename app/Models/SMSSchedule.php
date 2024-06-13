<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SMSSchedule extends Model {

	//

	protected $table = 'sms_schedule';

	public static $rules = [
		'contact',
	 	'message',
        'scheduled_time'
	];
	
	protected $fillable = [
		'contact',
	 	'message',
        'scheduled_time',
        'status'
    ];

	public $timestamps = false;

}



