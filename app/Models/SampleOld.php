<?php   namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SampleOld extends Model {

	protected $table = 'samples';

	protected $connection = 'mysql2';

	public static function getTestOutComeReportOld($date_filter='datereceived',$fro_date="",$to_date="",$result="1",$conds=[]){
		$fro_ts=!empty($fro_date)?strtotime("$fro_date 00:00:00"):strtotime(date("Y-m-01 00:00:00"));
	  	$to_ts=!empty($to_date)?strtotime("$to_date 23:59:59"):strtotime(date("Y-m-d 23:59:59"));
	  	if($date_filter=='date_rcvd_by_cphl'){
	  		$date_filter="datereceived";
	  	}else if($date_filter=='date_dbs_tested'){
	  		$date_filter="datetested";
	  	}else{
	  		$date_filter="datereceived";
	  	}

		$res=SampleOld::select('f.name as facility','f.level AS facility_level','f.hub','d.name AS district',
								 'p.name AS region','pat.infantname AS infant_name','pat.gender','pat.age AS infant_age',
								 'pat.infantid AS infant_exp_id','px.name AS prophylaxis','s.batchno AS batch_number',
								 's.accessionno AS sample_id','s.datecollected AS date_dbs_taken',
								 's.datereceived AS date_rcvd_by_cphl','s.spots AS nSpots',
								 \DB::raw(SampleOld::_statusCase()),'s.pcr AS pcr_option','px_a.name AS ante_prophylaxis',
								 'px_d.name AS del_prophylaxis','px_p.name AS post_prophylaxis',
								 \DB::raw(SampleOld::_bfCase()),'ep.name AS entry_point','m.caregiverphn AS infant_contact_phone',
								 'datetested AS date_dbs_tested','s.datedispatched AS date_PCR_printed',\DB::raw(SampleOld::_resultCase()),'rjct_rsns.name AS rejection_reason'
								 )
			   ->leftjoin('facilitys AS f','f.ID','=','s.facility')
			   ->leftjoin('districts AS d','d.ID','=','f.district')
			   ->leftjoin('provinces AS p','p.ID','=','d.province');
		$res=$result==2?$res->leftjoin('patients AS pat','pat.ID','=','s.parentid'):$res->leftjoin('patients AS pat','pat.ID','=','accessionno');
		$res=$res->leftjoin('mothers AS m','m.ID','=','pat.mother')
			   ->leftjoin('prophylaxis AS px',"px.ID","=","pat.prophylaxis")
			   ->leftjoin('prophylaxis AS px_a',"px_a.ID",'=','m.antenalprophylaxis')
			   ->leftjoin('prophylaxis AS px_d',"px_d.ID",'=','m.deliveryprophylaxis')
			   ->leftjoin('prophylaxis AS px_p',"px_p.ID",'=','m.postnatalprophylaxis')
			   ->leftjoin('entry_points AS ep','ep.ID','=','m.entry_point')
			   ->leftjoin('rejectedreasons AS rjct_rsns','rjct_rsns.ID','=','s.rejectedreason')
			   ->from('samples as s')
			   ->whereRaw("UNIX_TIMESTAMP($date_filter) BETWEEN $fro_ts AND $to_ts")
			   ->where('repeatt','=','0');

		if(!empty($result)) $res=$res->where('result','=',$result);		
		if(array_key_exists("b.facility_id", $conds)) $res=$res->where("s.facility","=",$conds["b.facility_id"]);
		if(array_key_exists("accepted_result", $conds)){
			if($conds["accepted_result"]=="SAMPLE_WAS_REJECTED") $res=$res->where("s.status","=","0");
		}

		return $res->get();
	}
//->leftjoin('patients AS pat','pat.ID','=','accessionno')

//->leftjoin('patients AS pat','pat.ID','=',\DB::raw('s.accessionno AND result=1 OR (pat.ID=s.parentid AND result=2)'))

	private static function _statusCase(){
		return "CASE
	  				WHEN s.status = '0' THEN 'Rejected' 
	  				WHEN s.status = '1' THEN 'Accepted'
	  				ELSE ''
	  			END AS status";
	}

	private static function _bfCase(){
		return "CASE
 					WHEN m.`feeding` = 1 THEN 'Y'
 					WHEN m.`feeding` = 2 THEN 'N'
 					WHEN m.`feeding` = 3 THEN 'Unknown'
 					WHEN m.`feeding` = 4 THEN ' Left Blank'
 					ELSE ''
				END AS brest_feeding";
	} 

	private static function _resultCase(){
		return "CASE 
					WHEN s.result = 1 THEN 'NEGATIVE'
					WHEN s.result = 2 THEN 'POSITIVE'
					ELSE ''
				END AS accepted_result";
	}

}