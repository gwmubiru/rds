<?php namespace App\Http\Controllers;
use View;
use App\Models\Facility;
use App\Models\District;
use App\Models\CovidSamples;
use App\Models\SampleDetails;
use App\Models\CovidResult;
use App\Models\SampleResult;
use App\Models\ExternalResult;
use App\Models\ExternalResultDetail;
use App\Models\Appendix;
use Illuminate\Http\Request;
use Session;
use Validator;
use Lang;
use Input;
use Carbon;
use Redirect;
use yajra\Datatables\Facades\Datatables;
use DB;
use Auth;

class WwbsController extends Controller {

	public function index(){
		$samples = \Request::get("samples");
		$type = \Request::get("type");
		$printed = \Request::get("printed");
		$is_synced = \Request::get("is_synced");
		
		return view("covid.wwbs.index", compact('type','printed','is_synced'));
	}
	public function list_data(){
		$type = \Request::get("type");
		$cols = ['facility','serial_number','specimen_ulin', 'submerged_at', 'submerged_by','retrieved_at','retrieved_by',
'temperature_at_retrieval','temperature_at_reception', 
'sr.target1_ct_value','sr.target2_ct_value','sr.target3_ct_value','sr.target4_ct_value',
    'rd.test_kit', 'r.result','r.test_date',
 '' ];
		$params = \MyHTML::datatableParams($cols);
		extract($params);

		$search_cond ='';
		if(!empty($search)){
			$search_cond .=" AND ( r.specimen_ulin  LIKE '%$search%' OR r.serial_number_batch LIKE '%$search%')";
		}

		$and_cond = ' AND r.created_at > now() - interval 2 month';
		$test_method_val = '';
		
		
	
		// make a general query to which other conditions will be added in case of filters and search
		$query_main = "SELECT r.sentinel_site,r.specimen_ulin, r.serial_number_batch, submerged_by,  retrieved_by,r.id,rd.target1_ct_value,rd.target2_ct_value,rd.target3_ct_value,rd.target4_ct_value,
		rd.test_kit,r.test_date,r.test_method, r.result,rd.submerged_at, rd.retrieved_at,rd.
temperature_at_submersion,rd.temperature_at_retrieval,rd.temperature_at_reception
		FROM results r
		INNER JOIN result_details rd ON(rd.result_id = r.id)
		WHERE r.id > 0 
		".$and_cond.$search_cond;
		$results = \DB::select($query_main." ORDER BY $orderby LIMIT $start, $length");
		
		
		$recordsTotal = collect(\DB::select("SELECT count(s.id) as num
		FROM results r
		INNER JOIN result_details rd ON(rd.result_id = r.id)
		WHERE r.id > 0   
		".$and_cond))->first()->num;

		$recordsFiltered = empty($search)?$recordsTotal:collect(\DB::select("SELECT count(sd.id) as num
		FROM results r
		INNER JOIN result_details rd ON(rd.result_id = r.id)
		WHERE r.id > 0  
		".$and_cond.$search_cond))->first()->num;
		
		$data = [];
		foreach ($results as $result) {
			
			if($type=='pending_retrieval'){
				$links['Retrieve'] = "/wwbs/retrieve/swab/".$result->id;
				//$links['Release'] = "/wwbs/retrieve/swab/?id=$result->id&type=lab_retain";
			}elseif($type == 'pending_reception'){				
				$links['Receive'] = "/wwbs/receive/sample/".$result->id;
				//$links['Reject'] = "/wwbs/receive/sample/".$result->id;
			}elseif ($type == 'pending_results') {
				// code...
				$links['Assign Result'] = "/wwbs/assign/result/".$result->id;
			}else{
				$links = [];
			}

			$data[] = [
				$result->facility,
				$result->serial_number,
				$result->specimen_ulin,
				\MyHTML::localiseDate($result->submerged_at,'d M Y h:i a'), 
				$result->submerged_by,
				empty($result->retrieved_at)?$result->retrieved_at:\MyHTML::localiseDate($result->retrieved_at,'d M Y h:i a'),
				$result->retrieved_by, 
				$result->temperature_at_retrieval,
				$result->temperature_at_reception, 
				$result->target1_ct_value,
				$result->target2_ct_value,
				$result->target3_ct_value,
				$result->target4_ct_value,
				$result->test_kit, 
				$result->test_result,
				empty($result->test_date)?$result->test_date:\MyHTML::localiseDate($result->test_date,'d M Y h:i a'),
				\MyHTML::specialDropdownLinks($links)
			];
            
		}

		
		//the total number of records filtered -  based on search and filter conditions
		
		return compact( "recordsTotal", "recordsFiltered", "data");
	}
	
	
}
