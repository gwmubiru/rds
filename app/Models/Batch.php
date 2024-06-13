<?php  namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model {

	protected $guarded = array('id');
	protected $table = 'batches';
	protected $dates = ['date_rcvd_by_cphl', 'date_dispatched_from_facility', 'date_entered_in_DB'];
	protected $fillable = ['batch_number', 'date_rcvd_by_cphl', 'facility_id', 'facility_name', 'envelope_number', 'date_dispatched_from_facility', 'senders_name','senders_telephone','requesting_unit','date_entered_in_DB', 'senders_comments','is_single_form','facility_district','entered_by'];

	protected $attributes = ['senders_comments'=>'','results_return_address'=>''];

	public static $rules = [
		'batch_number' => 'required',
		'date_rcvd_by_cphl'=>'required',
		'facility_id'=>'required',
		'envelope_number'=>'required'
	];

	public $timestamps = false;


	public function samples()
	{
		return $this->hasMany('App\Models\Sample', 'batch_id', 'id');		
	}

	public function getFacilityID(){
		return $this->attributes["facility_id"];
	}

	public static function  getBatches(){
		return Batch::leftjoin("dbs_samples AS d","d.batch_id","=","b.id")
			   ->select("b.*",\DB::raw("count(d.id) AS nr_smpls,
			   	SUM(CASE WHEN d.sample_rejected = 'NOT_YET_CHECKED' THEN 1 ELSE 0 END) AS nr_not_yet_checked,
			   	SUM(CASE WHEN d.sample_rejected = 'NO' THEN 1 ELSE 0 END) AS nr_approved,
			   	SUM(CASE WHEN d.sample_rejected = 'YES' THEN 1 ELSE 0 END) AS nr_rejected,
			   	IF(SUM(CASE WHEN d.sample_rejected = 'NOT_YET_CHECKED' THEN 1 ELSE 0 END)=0, 'YES', 'NO') AS batch_checked"
			   	))
			   ->from("batches AS b")
			   ->groupby("b.id")
			   ->orderby("b.id", "DESC")
			   ->orderby("b.envelope_number")
			   ->take(5000)
			   ->get();
	}

	public static function getapprovedsamples($batchID)
    {		
		$qry2=Batch::leftjoin("dbs_samples AS d","d.batch_id","=","b.id")
				->select('b.*',\DB::raw("count(d.id) AS nr_smpls"))
				->from('batches AS b' )
				->where('b.batch_number','=',$batchID)
				->groupby("b.id")
		        ->orderby("b.id", "DESC")
				->get()->first();
				return $qry2;

    }

    public static function searchBatchByName($q){
    	return Batch::select("id","batch_number")
    		        ->from("batches")
    		        ->where("batch_number", "like", "$q%")
    		        ->limit(10)
    		        ->get();
    }




}