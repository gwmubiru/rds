<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotherRetestingSchedule extends Model {

	//

	protected $table = 'mother_retesting_schedule';

	public static $rules = [
		'mother_name' => 'required'
	];
	
	protected $fillable = [
		'visit_date',
	    'mother_name',
	    'phone_number',
	    'art_nr',
	    'lnmp',
	    'edd',
	    'first_pcr',
	    'facility_id',
	    'facility_transferred_to',
	    'options',
	    'inactive',
	    'created',
	    'created_by'];


	public $timestamps = false;

	public static function getList($facility_id="",$p_m="",$p_y="",$due_m="",$due_y=""){
    	$qry=MotherRetestingSchedule::leftjoin('facilities AS f','f.id','=','mthr_ts.facility_id')  
    					->leftjoin('facilities AS ff','ff.id','=','mthr_ts.facility_transferred_to')  	
    					->leftjoin('districts AS d','d.id','=','f.districtID')
    					->leftjoin('hubs AS h','h.id','=','f.hubID')
                        ->leftjoin('regions AS r','r.id','=','d.regionID')
                        ->leftjoin('facility_levels AS fl','fl.id','=','f.facilityLevelID')
    					->select('mthr_ts.*','f.facility','fl.facility_level','d.district','h.hub','r.region','ff.facility AS facility_transferred_name')
    					->from('mother_retesting_schedule AS mthr_ts');

    	if(!empty($facility_id)) $qry=$qry->where('mthr_ts.facility_id','=',$facility_id);
    	if(!empty(session('hub_limit'))) $qry=$qry->where('f.hubID','=',session('hub_limit'));
    	if(!empty($p_m)) $qry=$qry->whereMonth("visit_date",'=',$p_m);
    	if(!empty($p_y)) $qry=$qry->whereYear("visit_date",'=',$p_y);
    	if(!empty($due_m)) $qry=$qry->whereMonth("first_pcr",'=',$due_m);
    	if(!empty($due_y)) $qry=$qry->whereYear("first_pcr",'=',$due_y);

    	return $qry->get();
    }


    public static function getRecord($id)
    {

$qry2=MotherRetestingSchedule::leftjoin("facilities AS f",'f.id','=','m.facility_id')
					->leftjoin("districts AS d","d.id","=","f.districtID")
					->leftjoin("facility_levels AS fl","fl.id","=","f.facilityLevelID")
					->select('m.*','f.facility','d.district','fl.facility_level')
					->from('mother_retesting_schedule AS m' )
					->where('m.id','=',$id)
					->get()->first();

					return $qry2;

    }


}



