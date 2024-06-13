<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccessLog extends Model {

	//

	protected $table = 'user_access_logs';

	public static $rules = [
		'username' => 'required',
		'url_accessed'=> 'required'
	];
	
	protected $fillable = [
		'username',
	 	'url_accessed',
	 	'log_time',
        'resource_accessed',
        'user_ip_daddress'
       ];

	public $timestamps = false;


}



