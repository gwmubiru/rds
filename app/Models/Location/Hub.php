<?php namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model {

	//

	protected $table = 'hubs';

	public static $rules = [
		'hub' => 'required'
	];

	protected $fillable = ['hub','email','ipID','coordinator','coordinator_contact','created','createdby'];

	public $timestamps = false;

	public function facilities(){
        return $this->hasMany('App\Models\Location\Facility','hubID');
    }

    public function ip(){
        return $this->belongsTo('App\Models\IP');
    }

    public static function hubsList(){
    	return Hub::leftjoin('ips AS i','i.id', '=','h.ipID')->select('h.*','i.ip')->from("hubs AS h")->get();
    }

    public static function hubsArr(){
		$arr=array();
		foreach(Hub::all() AS $h){
			$arr[$h->id]=$h->hub;
		}
		return $arr;
	}

	public static function hubStats($cond=1){
        $sql = "SELECT h.*, i.ip, i.focal_person, i.focal_person_contact,
                SUM(CASE WHEN fp.dispatched =0 THEN 1 ELSE 0 END) AS pending,
                MAX(fp.dispatch_at) AS last_printed,
                MIN(CASE WHEN fp.dispatched =0 THEN fp.qc_at END) AS oldest_pending
                FROM dbs_samples AS s
                INNER JOIN batches AS b ON s.batch_id = b.id
                INNER JOIN facility_printing AS fp ON b.id = fp.batch_id
                INNER JOIN facilities AS f ON b.facility_id=f.id
                INNER JOIN hubs AS h ON f.hubID=h.id
                LEFT JOIN ips AS i ON h.ipID=i.id
                WHERE $cond AND date(fp.qc_at)>='".env('RESULTS_CUTOFF', '2018-09-01')."'
                GROUP BY hubID";
        return \DB::select($sql);
    }



}
