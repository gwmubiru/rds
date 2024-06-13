<?php namespace App\Http\Controllers;

use View;
use App\Models\Worksheet;
use App\Models\WorksheetSample;
use App\Models\WorksheetTube;
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
use App\Models\CovidSamples;
class WorksheetController extends Controller {

	public function worksheetList(){
		$next = \Request::get('next');
		$worksheets = WorksheetSample::get();
		return view("covid.worksheets.worksheet_list", compact('worksheets','next'));
	}

	public function AbbotWorksheetList(){
		$worksheets = WorksheetSample::get();
		return view("covid.worksheets.worksheet_list", compact('worksheets'));
	}

	public function createWorksheet(){
		//
		$machine_type = \Request::get('machine_type');
		$worksheed_number = \MyHTML::gnerateWorksheetNumber($machine_type);
		$pool_type = \Request::get('pool_type');
		$number_of_tubes = \Request::get('number_of_tubes');
		$computed_number_of_tubes = $number_of_samples = $number_of_tubes;
		$total_pool = 1;
		//get the locator_ids to be added to the form
		//dd($pool_type);
		if(!$pool_type){
			return \Redirect::Intended('/worksheet/list')->with('msge','There was an error trying to create a worksheet');
		}
		if($pool_type == 3 || $pool_type == 4){
			//determine the max number of tubes based on pool type and number of samples
			$number_of_samples = $number_of_tubes * $pool_type;
			//there are instances when specified number of samples are too many for available samples
			$computed_number_of_tubes = $number_of_tubes;
			$total_pool = $pool_type;
			$query = 'SELECT id, specimen_ulin FROM covid_samples WHERE testing_lab = '.\Auth::user()->ref_lab.' AND status = 0 lIMIT '.$number_of_samples;

			//dd($locator_ids);
		}elseif($pool_type == 2){
			//get repeat samples

			$query = 'SELECT id, specimen_ulin FROM covid_samples WHERE testing_lab = '.\Auth::user()->ref_lab.' AND status = 2 lIMIT '.$number_of_samples;
		}else{
			//get priority samples
			$query = 'SELECT id, specimen_ulin FROM covid_samples WHERE testing_lab = '.\Auth::user()->ref_lab.' AND priority > 0 AND (status = 0 OR status = 2) ORDER BY priority LIMIT '.$number_of_samples;
		}
		//dd($query);
		$locator_ids = \DB::select($query);

		$total_ids = count($locator_ids);

		if($total_ids < $number_of_samples){
			//compute the numbe of tubes to be used (number of samples devide by pool_type)
			$computed_number_of_tubes = ceil($total_ids/$total_pool);
		}

		return view("covid.worksheets.create_worksheet", compact('pool_type','machine_type','locator_ids','limit','number_of_tubes','total_pool','computed_number_of_tubes','worksheed_number'));
	}

	public function getWorkseets(){

		$worksheet = WorksheetSample::Join('worksheets', 'worksheet_samples.worksheet_id', '=', 'worksheets.id')
		->groupBy('worksheet_id')->orderBy('worksheet_number','DESC')->get();

		return Datatables::of($worksheet)->addColumn('action', function($row) {
		return '<a href="' . route("worksheet", $row->id) . '" class="btn btn-sm btn-success right_alined">
		<i class="fa fa-edit"></i>View</a>

			<a href="' . url("/worksheet/assign_results?worksheet_id=".$row->id)
			. '" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>View with Results</a>';

		})->make(true);
	}

	public function assignResults(){
		$worksheet_id = \Request::get('worksheet_id');
		$worksheet = Worksheet::findOrFail($worksheet_id);
		$ww =\DB::select("SELECT sr.id, sr.worksheet_id, sr.is_completed, sr.sample_id, sr.result1, sr.result2,sr.final_result,
			cs.specimen_ulin as locator_id, sr.is_completed,sr.is_approved, ws.locator_id, wt.tube as tube_id,
			cs.id as sample_id, cs.specimen_ulin, ws.id as worksheet_sample_id  FROM
			worksheet_samples ws
			INNER JOIN worksheet_tubes wt ON(ws.worksheet_tube_id = wt.id)
			LEFT JOIN covid_samples cs ON ws.locator_id = cs.specimen_ulin
			LEFT JOIN sample_results sr ON sr.sample_id = cs.id
			WHERE ws.worksheet_id = ".$worksheet_id);
			return view("covid.worksheets.assing_results", compact('ww','worksheet'));
		}


	public function store(Request $request){

		$form_data = \Request::all();
		$worksheet_data_arr = $form_data['worksheet_data'];
		$worksheet = new Worksheet;
		$worksheet->worksheet_number = $request->worksheet_number;
		$worksheet->machine_type = $form_data['machine_type'];
		$worksheet->created_by = Auth::user()->id;

		$this->validate($request,[
			'worksheet_number' => 'unique:worksheets',
		],
		[
			'worksheet_number.unique' => 'Worksheet Number already EXISTS!',
		]);

		$worksheet->save();

		//now save worksheet_tubes
		foreach ($worksheet_data_arr as $key => $wksht_data) {
			$Worksheet_tube = new WorksheetTube;
			$Worksheet_tube->tube = trim($wksht_data['tube_id'][0]);
			$Worksheet_tube->worksheet_id = $worksheet->id;
			$Worksheet_tube->pool_type = $form_data['pool_type'];
			/*$this->validate($request,[
			 'tube' => 'unique:worksheet_tubes',
			],
			[
				'tube.unique' => 'Worksheet Number already EXISTS!',
			]);*/
			if(!WorksheetTube::where('tube', '=', trim($wksht_data['tube_id'][0]))->first()){
				$Worksheet_tube->save();


				//now save the worksheet samples
				$slocator_ids = $wksht_data['locator_ids'];
				$sample_ids = $wksht_data['sample_ids'];

				$worksheet_counter = count($slocator_ids);

				for($i=0; $i < $worksheet_counter; $i++){
					$sample_id = trim($sample_ids[$i]);
					$samples_sample = new WorksheetSample;
					$samples_sample->locator_id = trim($slocator_ids[$i]);
					$samples_sample->sample_id = $sample_id;
					$samples_sample->date_created = Carbon::today();
					$samples_sample->assigned_sample = 0;
					$samples_sample->worksheet_id = $worksheet->id;
					$samples_sample->worksheet_tube_id = $Worksheet_tube->id;


					$samples_sample->save();

					$samples_sample = CovidSamples::findOrFail($sample_id);
					$samples_sample->status = 1;
					$samples_sample->save();
				}
				//now mark this sample as  waiting for results

			}else{
				//dd('exists');
				return \Redirect::back()->with('msge','tube ID: '.trim($wksht_data['tube_id'][0]).' already used');
			}
		}

		return \Redirect::Intended('/worksheet/list')->with('msge','Successfully saved worksheet: '.$request->worksheet_number);
	}

	public function getWorksheetCsv(){
		$fro = \Request::get('created_from');
		$to = \Request::get('created_to');

		$query = "SELECT w.worksheet_number,ws.locator_id,wt.tube,ws.date_created, u.username, wt.id as wt
		FROM worksheets as w
		left join worksheet_samples ws on ws.worksheet_id = w.id
    left join worksheet_tubes wt on  ws.worksheet_tube_id = wt.id
		left join users u on u.id = w.created_by
		where ws.date_created between '$fro' and '$to'";
		$worksheet = \DB::select($query);

		header('Content-Type: text/csv; charset=utf-8');
		header("Content-Disposition: attachment; filename=worksheet_$fro"."_$to.csv");
		$output = fopen('php://output', 'w');
		$headers = array(
		'WORKSHEET No.',
		'LOCATOR ID',
		'TUBE ID (barcode)',
		'DATE CREATED',
		);

		fputcsv($output, $headers);
		foreach ($worksheet as $w_sheet) {
			$row=array(
			$w_sheet->worksheet_number,
			$w_sheet->locator_id,
			$w_sheet->tube,
			$w_sheet->date_created,
			);
			fputcsv($output, $row);
		}
		fclose($output);
	}


	public function getWorksheetPdf($id){

		$w = Worksheet::findOrFail($id);
		$ww = WorksheetSample::leftJoin('worksheet_tubes','worksheet_tubes.id','=','worksheet_samples.worksheet_tube_id')
			->where('worksheet_samples.worksheet_id', '=', $w['id'])
			->orderBy('locator_id','ASC')->get();

		if($w->machine_type == 0){
			$pdf = \PDF::loadView('covid.worksheets.worksheet', compact('ww'));
			return $pdf->stream('worksheet_'.$w['id'].'.pdf');
		}
		else {
			$pdf = \PDF::loadView('covid.worksheets.abbott.worksheet', compact('ww'));
			return $pdf->stream('worksheet_'.$w['id'].'.pdf');
		}
	}

	public function getID($id){

		$w = WorksheetSample::findOrFail($id);
		$w_no = $w['worksheet_number'];
		return $w;

	}

	public function updateWorksheetSamples(){
		$worksheet_sample = WorksheetSample::findOrFail(\Request::get('id'));
		$worksheet_tube = WorksheetTube::findOrFail($worksheet_sample->worksheet_tube_id);
		$locator_id = \Request::get('locator_id');
		$sample_id = \Request::get('sample_id');
		$worksheet_sample->locator_id = $locator_id;
		$worksheet_sample->save();
		//save the tube id now
		$worksheet_tube->tube = \Request::get('tube_id');
		$worksheet_tube->save();
		//update the samples table
		if($sample_id){
			\DB::statement("UPDATE covid_samples SET specimen_ulin = '".$locator_id."' WHERE id = ".$sample_id);
		}
		//retrun the expected array
		$ret_array = ['locator_id' => $worksheet_sample->locator_id,'tube_id'=>$worksheet_tube->tube];
		return json_encode($ret_array);
	}
}

//
//
//
//	¯\_(ツ)_/¯
// It works on my machine
//
//
//
//
