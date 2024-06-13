<?php   namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model {

	protected $guarded = array('id');
	protected $table = 'dbs_samples';
	protected $dates = array('f_date_results_collected', 'f_date_ART_initiated', 'date_dbs_taken', 'date_data_entered','poc_test_date','poc_dispatch_date');
	 public static $rules = [
		'batch_id' => 'required',
		'infant_exp_id'=>'required',
		'infant_name'=>'required'
	];
	protected $fillable = ['batch_id','infant_name','infant_exp_id','infant_gender','infant_age',
							'infant_dob','infant_entryPoint','infant_contact_phone','mother_antenatal_prophylaxis','mother_delivery_prophylaxis','mother_postnatal_prophylaxis','infant_prophylaxis','date_dbs_taken','date_data_entered', 'PCR_test_requested', 'SCD_test_requested', 'pcr', 'non_routine', 'given_contri', 'delivered_at_hc', 'infant_feeding', 'mother_htsnr', 'mother_artnr', 'mother_nin', 'pos_in_batch', 'is_single_form','test_type','infant_age_units','poc_device','poc_results','poc_sample_id','poc_invalid_comments','poc_tested_by','poc_telephone','poc_test_date','poc_reviewed_by','poc_dispatch_date'];

	protected $attributes = ['pos_in_batch'=>1];

	public $timestamps = false;

	public function parent_batch()
	{
		return $this->belongsTo('Batch');
	}

	public function getEntryPoint(){
		return $this->attributes["infant_entryPoint"];
	}

	public function getAnteNatalPMTCT(){
		return $this->attributes["mother_antenatal_prophylaxis"];
	}

	public function getDeliveryPMTCT(){
		return $this->attributes["mother_delivery_prophylaxis"];	
	}

	public function getPostNatalPMTCT(){
		return $this->attributes["mother_postnatal_prophylaxis"];
	}

	public function getInfantPMTCT(){
		return $this->attributes["infant_prophylaxis"];
	}

	public function wasRejected(){
		if($this->attributes["sample_rejected"] === "NOT_YET_CHECKED")
			return null;
		
		$sample_rejected = ($this->attributes(["sample_rejected"] === "REJECTED_FOR_EID" || ["sample_rejected"] === "YES")) ? true : false;

		
		return 	$sample_rejected;
	}

	public function wasAccepted(){
		$status = $this->WasRejected();

		if($status === null)
			return null;
		else
			return !$status;
	}

	public static function quickStats($level,$case){
		$res=Sample::select('id','batch_id');
		$res=$level=='batches'?Sample::select('batch_id'):Sample::select('id');
		switch ($case) {
			case 'pending2approve':
				$res=$res->where('sample_rejected','=','NOT_YET_CHECKED');// 
				break;

			case 'ready4EIDlab':
				$res=$res->where('sample_rejected','=','NO')->where('PCR_test_requested','=','YES')
						 ->where('in_workSheet','=','NO')->where('testing_completed', '=', 'NO');
				break;

			case 'ready4SClab':
				$res=$res->where('ready_for_SCD_test','=','YES')
						 ->where(function($query){
						 	$query->whereNull('in_scworksheet')->orWhere('in_scworksheet','=','NO')
						 	->orWhere('repeated_SC_test','=','YES');
						 });
				break;
			
			default:
				# code...
				break;
		}
		$res=$level=='batches'?$res->groupby('batch_id'):$res;
		return $res->get()->count();
	}


	public static function getTestOutComeReport($date_filter='date_rcvd_by_cphl',$fro_date="",$to_date="",$filter_params=[]){
	  $fro_ts=!empty($fro_date)?strtotime("$fro_date 00:00:00"):strtotime(date("Y-m-01 00:00:00"));
	  $to_ts=!empty($to_date)?strtotime("$to_date 23:59:59"):strtotime(date("Y-m-d 23:59:59"));

	  $status_case=Sample::_statusCase();
	  $gender_case=Sample::_genderCase();
	  $pcr_case=Sample::_pcrCase();
	  $bf_case=Sample::_bfCase();
	  $sql="SELECT apdx.appendix AS entry_point,facility,facility_level,hub,region,district,b.*,s.*,s.id AS sample_id,
	  			$status_case,$gender_case,$pcr_case,$bf_case,px.appendix AS prophylaxis,ante_px.appendix AS ante_prophylaxis,
	  			del_px.appendix AS del_prophylaxis,post_px.appendix AS post_prophylaxis, rsn_px.appendix AS rejection_reason, 
	  			GROUP_CONCAT(wi.worksheet_number) AS worksheet_num, fp.qc_at, fp.dispatch_at
	  		  FROM dbs_samples AS s
	  		  LEFT JOIN batches AS b ON s.batch_id=b.id
	  		  LEFT JOIN facilities AS f ON b.facility_id=f.id
	  		  LEFT JOIN districts AS d ON f.districtID=d.id
	  		  LEFT JOIN regions AS r ON d.regionID=r.id
	  		  LEFT JOIN facility_levels AS l ON f.facilityLevelID=l.id
	  		  LEFT JOIN hubs AS h ON f.hubID=h.id
	  		  LEFT JOIN appendices AS px ON s.infant_prophylaxis=px.id
	  		  LEFT JOIN appendices AS ante_px ON s.mother_antenatal_prophylaxis=ante_px.id
	  		  LEFT JOIN appendices AS del_px ON s.mother_delivery_prophylaxis=del_px.id
	  		  LEFT JOIN appendices AS post_px ON s.mother_postnatal_prophylaxis=post_px.id
	  		  LEFT JOIN appendices AS apdx ON s.infant_entryPoint=apdx.id
	  		  LEFT JOIN appendices AS rsn_px ON s.rejection_reason_id=rsn_px.id
	  		  LEFT JOIN worksheet_index AS wi ON s.id=wi.sample_id
	  		  LEFT JOIN facility_printing AS fp ON b.id=fp.batch_id AND `section`='eid'
	  		  WHERE PCR_test_requested='YES'
	  		  AND (UNIX_TIMESTAMP($date_filter) BETWEEN $fro_ts AND $to_ts)";

	  		  if(count($filter_params)>0){
	  		  	foreach ($filter_params as $col => $val) {
	  		  		$sql.=" AND $col='$val'";
	  		  	}
	  		  }
	  		  return \DB::select("$sql GROUP BY s.id");
	}


	public static function getSCDTestOutComeReport($date_filter='date_rcvd_by_cphl',$fro_date="",$to_date="",$filter_params=[]){
	  $fro_ts=!empty($fro_date)?strtotime("$fro_date 00:00:00"):strtotime(date("Y-m-01 00:00:00"));
	  $to_ts=!empty($to_date)?strtotime("$to_date 23:59:59"):strtotime(date("Y-m-d 23:59:59"));

	  $status_case=Sample::_statusCase();
	  $gender_case=Sample::_genderCase();

	  $sql = "SELECT apdx.appendix AS entry_point,facility,facility_level,hub,region,district,b.*,s.*,s.id AS sample_id,
	  			$status_case,$gender_case,scw.DateTested AS date_sc_tested, 
	  			GROUP_CONCAT(scwi.worksheet_number) AS worksheet_num
	  		  FROM dbs_samples AS s
	  		  LEFT JOIN batches AS b ON s.batch_id=b.id
	  		  LEFT JOIN facilities AS f ON b.facility_id=f.id
	  		  LEFT JOIN districts AS d ON f.districtID=d.id
	  		  LEFT JOIN regions AS r ON d.regionID=r.id
	  		  LEFT JOIN facility_levels AS l ON f.facilityLevelID=l.id
	  		  LEFT JOIN hubs AS h ON f.hubID=h.id
	  		  LEFT JOIN sc_worksheet_index AS scwi ON s.id=scwi.sample_id
	  		  LEFT JOIN sc_worksheets AS scw ON scwi.worksheet_number=scw.id
	  		  LEFT JOIN appendices AS apdx ON s.infant_entryPoint=apdx.id

	  		  WHERE SCD_test_requested='YES'
	  		  AND (UNIX_TIMESTAMP($date_filter) BETWEEN $fro_ts AND $to_ts)";

	  		  if(count($filter_params)>0){
	  		  	foreach ($filter_params as $col => $val) {
	  		  		$sql.=" AND $col='$val'";
	  		  	}
	  		  }
	  		  return \DB::select("$sql GROUP BY s.id");
	}

	public static function getFollowupReport($date_filter='f_date_rcvd_by_cphl',$fro_date="",$to_date="",$filter_params=[]){
	  $fro_ts=!empty($fro_date)?strtotime("$fro_date 00:00:00"):strtotime(date("Y-m-01 00:00:00"));
	  $to_ts=!empty($to_date)?strtotime("$to_date 23:59:59"):strtotime(date("Y-m-d 23:59:59"));

	  $gender_case=Sample::_genderCase();
	  $pcr_case=Sample::_pcrCase();
	  $bf_case=Sample::_bfCase();
	  $sql = "SELECT f.facility,facility_level,hub,region,district,b.*,s.*,s.id AS sample_id,
	  		 $gender_case,$pcr_case,$bf_case,apdx.appendix AS rsn_not_initiated,fr.facility AS facility_referred
	  		  FROM dbs_samples AS s
	  		  LEFT JOIN batches AS b ON s.batch_id=b.id
	  		  LEFT JOIN facilities AS f ON b.facility_id=f.id
	  		  LEFT JOIN districts AS d ON f.districtID=d.id
	  		  LEFT JOIN regions AS r ON d.regionID=r.id
	  		  LEFT JOIN facility_levels AS l ON f.facilityLevelID=l.id
	  		  LEFT JOIN hubs AS h ON f.hubID=h.id
	  		  LEFT JOIN appendices AS apdx ON (s.f_reason_ART_not_initated=apdx.code AND apdx.categoryID=7)
	  		  LEFT JOIN facilities AS fr ON s.f_facility_referred_to=fr.id
	  		  WHERE PCR_test_requested='YES'
	  		  AND s.accepted_result='POSITIVE'
	  		  AND (UNIX_TIMESTAMP($date_filter) BETWEEN $fro_ts AND $to_ts)";

	  		  if(count($filter_params)>0){
	  		  	foreach ($filter_params as $col => $val) {
	  		  		$sql.=" AND $col='$val'";
	  		  	}
	  		  }
	  		  return \DB::select($sql);
	}

	private static function _statusCase(){
		return "CASE
		  		 WHEN sample_rejected = 'YES' THEN 'Rejected' 
		  		 WHEN sample_rejected = 'NO' THEN 'Accepted'
		  		 ELSE 'Rejected'
		  		END AS status";
	}

	private static function _genderCase(){
		return "CASE
				 WHEN infant_gender = 'MALE' THEN 'M' 
		  		 WHEN infant_gender = 'FEMALE' THEN 'F'
		  		 ELSE ''
		  		END AS gender";
	}

	private static function _pcrCase(){
		return "CASE
				 WHEN pcr = 'FIRST' THEN '1' 
			  	 WHEN pcr = 'SECOND' THEN '2'
			  	 ELSE ''
			  	END AS pcr_option";
	}

	private static function _bfCase(){
		return "CASE
			  	 WHEN infant_is_breast_feeding = 'YES' THEN 'Y' 
			  	 WHEN infant_is_breast_feeding = 'NO' THEN 'N'
			  	 WHEN infant_is_breast_feeding = 'UNKNOWN' THEN 'Unknown'
			  	 ELSE ''
			  	END AS brest_feeding";
	}
	

	public static function getscReport($date_filter='date_rcvd_by_cphl',$fro_date="",$to_date="",$filter_params=[]){
		$fro_ts=!empty($fro_date)?strtotime("$fro_date 00:00:00"):strtotime(date("Y-m-01 00:00:00"));
		$to_ts=!empty($to_date)?strtotime("$to_date 23:59:59"):strtotime(date("Y-m-d 23:59:59"));
		//$fro_ts-=10800;
		//echo "now is ".date("Y-m-d H:i:s")." $fro_date ($fro_ts),(".date("Y-m-d H:i:s",$fro_ts).")... $to_date ($to_ts) (".date("Y-m-d H:i:s",$to_ts).")   :: BETWEEN 1448917200 (".date("Y-m-d H:i:s",1448917200).") AND 1451509200 (".date("Y-m-d H:i:s",1451509200).")";
	  $sql="SELECT facility,facility_level,hub,region,district,worksheet.*,worksheet.worksheet_number AS EIDworksheetnumber,scworksheet.*,scworksheet.worksheet_number AS scworksheetnumber,sc_worksheets.*,b.*,s.*,s.id AS sample_id
	  			
	  		  FROM dbs_samples AS s
	  		  LEFT JOIN batches AS b ON s.batch_id=b.id
	  		  LEFT JOIN facilities AS f ON b.facility_id=f.id
	  		  LEFT JOIN districts AS d ON f.districtID=d.id
	  		  LEFT JOIN regions AS r ON d.regionID=r.id
	  		  LEFT JOIN facility_levels AS l ON f.facilityLevelID=l.id
	  		  LEFT JOIN hubs AS h ON f.hubID=h.id
	  		  LEFT JOIN worksheet_index AS worksheet ON s.id=worksheet.sample_id
	  		  LEFT JOIN sc_worksheet_index AS scworksheet ON s.id=scworksheet.sample_id
	  		  LEFT JOIN sc_worksheets ON scworksheet.worksheet_number=sc_worksheets.id
	  WHERE SCD_test_requested='YES'
	  		  AND (UNIX_TIMESTAMP($date_filter) BETWEEN $fro_ts AND $to_ts)";
	  		  return \DB::select($sql);
	}

	public static function searchSampleById($q){
    	return Self::select("id","batch_id","pos_in_batch")
    		        ->from("dbs_samples")
    		        ->where("in_workSheet", "=","NO")
    		        ->where("id", "like", "$q%")
    		        ->limit(10)
    		        ->get();
    }

    public static function scdSQL(){
    	#$headers=["Facility Name","Level","Hub","Region","District","Infant Name","Sex","DOB","Age","EXP Number","Batch No","Sample ID","Date Collected","Date Received","Entry Point","SCD Result","Result"];

    	return "SELECT facility,facility_level,hub,region,district,infant_name,CASE
				 WHEN infant_gender = 'MALE' THEN 'M' 
		  		 WHEN infant_gender = 'FEMALE' THEN 'F'
		  		 ELSE ''
		  		END AS gender,infant_dob,infant_age,infant_exp_id,batch_number, s.id, date_dbs_taken, date_rcvd_by_cphl, apdx.appendix AS entry_point, SCD_test_result, accepted_result
	  			
	  		  FROM dbs_samples AS s
	  		  LEFT JOIN batches AS b ON s.batch_id=b.id
	  		  LEFT JOIN facilities AS f ON b.facility_id=f.id
	  		  LEFT JOIN districts AS d ON f.districtID=d.id
	  		  LEFT JOIN regions AS r ON d.regionID=r.id
	  		  LEFT JOIN facility_levels AS l ON f.facilityLevelID=l.id
	  		  LEFT JOIN hubs AS h ON f.hubID=h.id
	  		  LEFT JOIN appendices AS apdx ON s.infant_entryPoint=apdx.id

	  		  WHERE SCD_test_requested='YES' 
	  		  AND  `SCD_test_result` in ('VARIANT','CARRIER','SICKLER')
	  		  AND date(date_rcvd_by_cphl)>='2017-06-01'";
    }
}


	
