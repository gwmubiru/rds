<?php namespace App\Http\Controllers;

use View;
use App\Models\CovidResult as CovidResult;
use App\Models\Covid;
use App\Models\Facility;
use App\Models\District;
use App\Models\Location\Hub;
use App\Models\Result;
use App\Models\Appendix;
use App\Models\CovidResultHistory;
use Auth;

class ResultSynchronizationController extends Controller {

	public function processSyncRequest(){
		$sync_data = \Input::all();
		foreach ($sync_data as $key => $result_data) {
			\Log::info($result_data);
			if(!Result::where('specimen_ulin', '=', $result_data['specimen_ulin'])->where('ref_lab','=',$result_data['ref_lab'])->first()){

				$result_obj = Result::Create($result_data);
			}
		}
	}
}
