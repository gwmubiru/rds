<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class DistrictFileUpload extends Model {

	protected $table = 'district_files_upload';
	public $timestamps = 'true';

	protected $fillable = [
		'file_name',
		'uploaded_by',
		'uploader_email',
	];
}
