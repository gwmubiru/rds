<?php  namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model {	
	protected $table = 'branches';
	public $timestamps = 'true';

    /**
     * Fillable fields
     *
     * @var array
     */

    public static $rules = [
		'name' => 'required',
		'district_id'=>'required',
	];

    protected $fillable = [
        'name',
        'district_id',
        'status',
        'created_at',
        'created_by'
    ];

	

}
