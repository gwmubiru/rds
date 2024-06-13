<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model {

	//

	protected $table = 'facilities';

	public static $rules = [
		'facility' => 'required',
		'districtID' => 'required',
		'hubID' => 'required',
		'facilityLevelID'=>'required',
        'email'=>'email'
	];

	protected $fillable = [
	  'facilityCode',
	  'facility',
	  'facilityLevelID',
	  'districtID',
	  'hubID',
	  'phone',
	  'email',
	  'contactPerson',
	  'physicalAddress',
	  'returnAddress',
	  'created',
	  'createdby'];

	public $timestamps = false;

	public function district(){
        return $this->belongsTo('App\Models\Location\District');
    }

    public function hub(){
        return $this->belongsTo('App\Models\Location\Hub');
    }
    public function batch(){
        return $this->belongsTo('App\Models\Location\Batch');
    }

    public function facility_level(){
        return $this->belongsTo('App\Models\Location\FacilityLevel');
    }

    /*public static function getFacility($id){
    	return Facility::leftjoin('facility_levels AS fl','fl.id', '=','f.facilityLevelID')
    					->leftjoin('districts AS d','d.id','=','f.districtID')
    					->select('f.*','fl.facility_level','d.district')
    					->from("facilities AS f")
    					->where('f.id',$id)->get();
    }*/


    public static function getFacility($id){
    	return Facility::leftjoin('facility_levels AS fl','fl.id', '=','facilities.facilityLevelID')
    					->leftjoin('districts AS d','d.id','=','facilities.districtID')
    					->leftjoin('hubs AS h','h.id','=','facilities.hubID')
    					->select('facilities.*','fl.facility_level','d.district','h.hub')
    					->findOrFail($id);
    }


    private static function _getFacilities(){
    	return Facility::leftjoin('facility_levels AS fl','fl.id','=','f.facilityLevelID')
    					->leftjoin('districts AS d','d.id','=','f.districtID')
    					->leftjoin('hubs AS h','h.id','=','f.hubID')
                        ->leftjoin('regions AS r','r.id','=','d.regionID')
    					->select('f.*','fl.facility_level','d.district','h.hub','r.region')
    					->from('facilities AS f');

    }

    public static function getFacilitiesAll(){
    	return Facility::_getFacilities()->get();
    }

    public static function getFacility2($id){
        return Facility::_getFacilities()->where('f.id','=',$id)->get();
    }

     public static function getFacility3(){
        $o_f = \Auth::user()->other_facilities;
        $o_facilities = !empty($o_f)?unserialize($o_f):[]; 
        return Facility::_getFacilities()->whereIn('f.id',array_merge([\Auth::User()->facilityID], $o_facilities))->get();
    }

    public static function getFacilitiesByHub($hub_id){
    	return Facility::_getFacilities()->where('f.hubID','=',$hub_id)->get();
    }

    public static function getFacilitiesByDistrict($district_id){
    	return Facility::_getFacilities()->where('f.districtID','=',$district_id)->get();
    }

    public static function getFacilitiesByRegion($region_id){
        return Facility::_getFacilities()->where('d.regionID','=',$region_id)->get();
    }

    public static function searchFacilityByName($q){
        return Facility::select('id','facility')->where('facility','LIKE',"%$q%")->get();
    }

    public static function facilitiesArr($conds=[]){
        $arr=array();
        if(\MyHTML::is_district_user()){

             $arr = Facility::select('id','facility','districtID')
             ->where('districtID', \Auth::user()->district_id)
             ->where('facilityLevelID','!=',20)
             ->lists('facility','id');

        }else{
            $arr = Facility::select('id','facility','districtID')->lists('facility','id');
        }
        
       
        return $arr;
    }

    public static function facilitiesByDistrictsArr(){
        $arr=array();
        $districts= Location\District::districtsArr();
        foreach(Facility::select('id','facility','districtID')->get() AS $f){
           $d_name=array_key_exists($f->districtID, $districts)?$districts[$f->districtID]:'';
           $arr[$d_name][$f->id]=$f->facility;
        }
       ksort($arr);  
       return $arr;     
    }

    public static function facilitiesByDistrictsArr2($conds=[]){
        $arr=array();
        $districts= Location\District::districtsArr();
        $facilities = Facility::select('id','facility','districtID');
       
        if(!empty(\Auth::user()->ipID)) $facilities = $facilities->where('ipID',\Auth::user()->ipID);
        if(!empty(\Auth::user()->hubID)) $facilities = $facilities->where('hubID',\Auth::user()->hubID);
        if(!empty(\Auth::user()->facilityID)) $facilities = $facilities->where('id',\Auth::user()->facilityID);
        foreach ($conds as $k => $v) {
           $facilities->where($k, $v);
        }
        foreach($facilities->get() AS $f){
           $d_name=array_key_exists($f->districtID, $districts)?$districts[$f->districtID]:'';
           $v=json_encode(['facility_id'=>$f->id,'facility_name'=>$f->facility,'district'=>$d_name], JSON_FORCE_OBJECT);
           if(!empty($f->facility)){
            $arr[$d_name][$v]=$f->facility;
           }
           
        }
       ksort($arr); 
       return $arr;     
    }


    public static function facilityStats($cond="1"){
        $sql = "SELECT f.*, i.ip, i.focal_person_contact, i.focal_person,
                SUM(CASE WHEN fp.dispatched =0 THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN fp.dispatched =0 AND section =  'eid' THEN 1 ELSE 0 END) AS eid_pending, 
                SUM(CASE WHEN fp.dispatched =0 AND section =  'scd' THEN 1 ELSE 0 END ) AS scd_pending, 
                SUM(CASE WHEN fp.dispatched =0 AND section =  'rejects' THEN 1 ELSE 0 END ) AS rejects_pending,
                MAX(fp.dispatch_at) AS last_dispatch_date,
                MIN(CASE WHEN fp.dispatched =0 THEN fp.qc_at END) AS oldest_pending
                FROM dbs_samples AS s
                INNER JOIN batches AS b ON s.batch_id = b.id
                INNER JOIN facility_printing AS fp ON b.id = fp.batch_id
                RIGHT JOIN facilities AS f ON b.facility_id=f.id
                LEFT JOIN hubs AS h ON f.hubID=h.id
                LEFT JOIN ips AS i ON h.ipID=i.id
                WHERE $cond AND date(b.date_entered_in_DB)>='".env('RESULTS_CUTOFF', '2018-09-01')."'
                GROUP BY facility_id";
        return \DB::select($sql);
		/*Schema::connection('mysql2')->create('some_table', function($table)
		{
			$table->increments('id'):
		});*/
    }



}
