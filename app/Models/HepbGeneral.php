<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HepbGeneral extends Model {
	public static function printingStats($cond="1"){
		$query = "SELECT f.id,f.facility,
				SUM(CASE WHEN (r.printed = 0 OR r.printed IS NULL) THEN 1 ELSE 0 END) AS pending,
				SUM(CASE WHEN (vr.printed = 0 OR vr.printed IS NULL) THEN 1 ELSE 0 END) AS rejects_pending
				FROM vl_results_qc AS rqc
				INNER JOIN vl_results AS r ON (rqc.result_id = r.id)
				INNER JOIN vl_samples AS s ON (r.sample_id = s.id)
				LEFT JOIN vl_rejected_samples_release AS vr ON (vr.sample_id = s.id)
				INNER JOIN backend_facilities  AS f ON (s.facility_id = f.id)
				WHERE rqc.released = 1 OR vr.released = 1
				GROUP BY s.facility_id
				";
		return \DB::connection('myql_hepb')->select($query);;
		
	}
   
}
