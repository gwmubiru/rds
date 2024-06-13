<?php namespace App\Http\Controllers;

use App\Models\Logistic;
use App\Models\Facility;
use App\Models\District;
use Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use App\Closet\MyHTML as MyHTML;

class LogisticsController extends Controller {

	public function index(){
		if(MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user()){
			$condition = "'user_id = '".Auth::user()->id. "' or facility_id = '".Auth::user()->facilityID;
		}
		elseif(MyHTML::survailance()){
			$condition = 'facility_id is not null or facility_id is null';
		}
		else{
			$condition = "user_id = ".Auth::user()->id;
		}
		$logistics = \DB::select('select * from logistics where '.$condition);
		// dd($condition);
		return view("Logistics.index",compact('logistics'));
	}

	public function newLogisticsData(){
			$facility_and_district = \DB::select('select f.*, u.*,d.* from rds.facilities as f left join rds.users as u on u.facilityID = f.id left join rds.districts as d on f.districtID = d.id where f.id= '. \Auth::user()->facilityID);
					if(!empty($facility_and_district))
					{
						return view("Logistics.create",compact('facility_and_district'));

					}
					else{
						echo "<script>alert('YOU CAN NOT ADD ANY LOGISTIC REPORT BECAUSE YOU ARE NOT ATTACHED TO ANY KNOWN HEALTH FACILITY OR LABORATORY. contact administrators of this system if this is in error.');</script>";
						return $this->index();
					}
	}

	public function store(Request $request)
	{
		// dd(\Request::all());

			$input = $request->all();
			$condition = $input['commodity'];
			foreach ($condition as $key => $condition) {

				$logistics_data = new Logistic;

				if($request->commodity == ""){
					return \Redirect::back()->with('message', 'Requesting facility details missing, request has been NULLIFIED.');
				}
				if($request->start_date == "" || $request->end_date == ""){
					return Redirect::back()->with('message', 'Reporting period was missing from your report, request has been NULLIFIED.');
				}

				$logistics_data->facility = $request->facility;
				$logistics_data->facility_id = $request->facility_id;
				$logistics_data->district_id = $request->district_id;
				$logistics_data->district = $request->district;
				$logistics_data->start_date = date('Y-m-d',strtotime($request->start_date));
				$logistics_data->end_date = date('Y-m-d',strtotime($request->end_date));
				$logistics_data->submitted_by = Auth::user()->username;
				$request->date_submitted == "" ? $logistics_data->date_submitted = date('Y-m-d',strtotime(Carbon::now()->toDateString())) : $logistics_data->date_submitted = date('Y-m-d',strtotime($request->date_submitted));
				$logistics_data->commodity = $input['commodity'][$key];
				$logistics_data->commodity_category = $input['commodity_category'][$key];

				$logistics_data->opening_balance = $input['opening_balance'][$key];
				$logistics_data->quantity_received = $input['qty_received'][$key];
				$logistics_data->total_consumption = $input['total_consumption'][$key];
				$logistics_data->adjustment = $input['losses_adjustments'][$key];
				$logistics_data->closing_balance = $input['total_closing_balance'][$key];
				$logistics_data->comment = $input['comment'][$key];
				$logistics_data->user_id = Auth::user()->id;
				$logistics_data->save();
			}

			try{

				$logistics_data->save();

				return \Redirect::route('Logistics');
			}catch(QueryException $e){
				Log::error($e);
			}
		}

		public function getCsv()
	{

		$fro = date("Y-m-d",strtotime($request->fro));
		$to = date("Y-m-d",strtotime($request->to));

		$data = \DB::select("select * from logistics where start_date >= "."'$fro'". "and end_date <= "."'$to'");
// dd("select * from logistics where start_date >= "."'$fro'". "and end_date <= "."'$to'");

		// dd($query);
		// dd($data);
		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=Covid Commodity Report for period-$fro"."_$to.csv");
		$output = fopen('php://output', 'w');
		$headers = array(

		'COMMODITY',
		'COMMODITY CATEGORY',
		'OPENING BALANCE',
		'QUANTITY RECEIVED',
		'TOTAL CONSUMPTION',
		'ADJUSTMENTS',
		'CLOSING BALANCE',
		'COMMENT',
		'START DATE',
		'END DATE',
		'FACILITY',
		'DISTRICT'
		);

		fputcsv($output, $headers);
		foreach ($data as $data) {
			$row=array(
			$data->commodity,
			$data->commodity_category == ($data->commodity_category == 1) ? $data->commodity_category = 'RDT testing kit' : (($data->commodity_category == 2) ? $data->commodity_category = 'Sample collection swabs' : 'Sample collection swab'),
			$data->opening_balance,
			$data->quantity_received,
			$data->total_consumption,
			$data->adjustment,
			$data->closing_balance,
			$data->comment,
			$data->start_date,
			$data->end_date,
			$data->facility,
			$data->district,
			);

			fputcsv($output, $row);
		}
		fclose($output);

	}

	public function importExcel()
	{
		$request = \Request::all();

		$fileName = $_FILES['import_file']['tmp_name'];

		if ($_FILES["import_file"]["size"] > 0) {
			$file = fopen($fileName, "r");
			fgetcsv($file);
			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {

				$data = new Logistic;

				$data->commodity = $column[0];
				($column[1] == 'RDT testing kit') ? $data->commodity_category = 1 : (($column[1] == 'Sample collection kit') ? $data->commodity_category = 2 : $data->commodity_category = '3' ) ;
				$data->opening_balance = $column[2];
				$data->quantity_received = $column[3];
				$data->total_consumption = $column[4];
				$data->adjustment = $column[5];
				$data->closing_balance = $column[6];
				$data->comment = $column[7];
				$data->start_date = date("Y-m-d",strtotime($column[8]));
				$data->end_date = date("Y-m-d",strtotime($column[9]));
				$data->facility = $column[10];
				$data->district = $column[11];
				$data->date_submitted = \Carbon::now();
				$data->submitted_by = Auth::user()->family_name ." ". Auth::user()->other_name;
				$data->user_id = Auth::user()->id;
				$data->save();
			}
		}
return \Redirect::back()->with('message','Report succesfully uploaded');

	}

	}
