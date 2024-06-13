<?php namespace App\Http\Controllers;
use View;

use App\Models\CovidSamples;
use App\Models\SampleReception;
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

class SampleReceptionController extends Controller {

	public function index(){
		$list_of_samples = SampleReception::orderBy('created_at')->get();
		return view("sample_reception.index", compact('list_of_samples'));
	}

	public function getBarcodes(){
		$list_of_samples = SampleReception::orderBy('created_at')->get();
		return view("sample_reception.index", compact('list_of_samples'));
	}

	public function create(){
		return view("sample_reception.create");
	}

	public function store(Request $request){
		$barcode = new SampleReception;

		//save package
		$barcode->barcode = $request->barcode;
		$barcode->created_by = Auth::user()->id;
		$this->validate($request,['barcode' => 'required|unique:sample_reception'],['barcode.required' => ' Barcode / Package ID already Exist',]);
		$barcode->save();

		//saving locator IDs
		$form_data = \Request::all();

		$box_number = [$form_data][0]['box_number'];
		$start =  [$form_data][0]['start_position'];
		$end =  [$form_data][0]['number_of_locators'];

		if(\Request::has('type')){

			$counter = $form_data['number_of_locators'];

			for($x=$start; $x<=$end; $x++){

				$samples = new CovidSamples;

				$samples->package_id =  $barcode->id;
				$samples->priority = array_key_exists('priority',$form_data) ? 1 : "0";
				$samples->testing_lab = \Auth::user()->ref_lab;
				$specimen_ulin = $form_data['yearmonth'].'-'.str_pad($form_data['box_number'],4,"0",STR_PAD_LEFT).'/'.str_pad($x,3,"0",STR_PAD_LEFT);

				//check if locator ID already exists in the samples table
				if(CovidSamples::where('specimen_ulin',$specimen_ulin)->first()){
					SampleReception::where('id','=', $barcode->id)->delete(); //unsave the package ID from its table
					return \Redirect::back()->with('msge',"Locator IDs already exists, Package ".$request->barcode." not saved");
				}

				//now save locator IDs if they do not exist in samples tables
				else{
					$samples->specimen_ulin = $specimen_ulin;

					$len = strlen($specimen_ulin);
					if($len == 13){

						$this->validate($request,[
						'box_number' => 'required|min:4|max:4',
						'start_position' => 'required|min:1|max:2',
						'number_of_locators' => 'required|min:1|max:2',
						],
						[
						'box_number.required' => 'Box number can not be less than 4 or more than 4.',
						'start_position.required' => 'Start position can not be less than 1 or more than 2 digits',
						'number_of_locators.required' => 'End position can not be greater than 1 or more than 2 digits.',
						]);
						$samples->save();
					}
					else {
						// code...
						return \Redirect::back()->with('msge','NOT Saved, your input had errors');
					}
				}
			}
		}

		else {

			foreach ($form_data['data'] as $key => $value) {

				$samples = new CovidSamples;

				$samples->package_id = $barcode->id;
				$samples->specimen_ulin = array_key_exists('specimen_ulin',$value) ? $value['specimen_ulin'] : "";
				$samples->priority = array_key_exists('priority',$value) ? $value['priority'] : "0";
				$samples->testing_lab = \Auth::user()->ref_lab;
				$samples->save();
			}
		}

		return \Redirect::back()->with('msge','Successfully saved, Please enter the next batch');
	}

	public function viewPackage($id){

		$packageID = SampleReception::findOrFail($id);

		$sql = "select sr.barcode, cs.priority, cs.specimen_ulin from sample_reception sr
		left join covid_samples cs on sr.id = cs.package_id where cs.package_id = ".$packageID['id']. " order by cs.specimen_ulin ASC ";

		$query = \DB::select($sql);

		$pdf = \PDF::loadView('sample_reception.viewPackage', compact('query'));
		return $pdf->stream('worksheet_'.$packageID['id'].'.pdf');
	}

	public function edit($id){

		$packageID = SampleReception::findOrFail($id);

		$sql = "select sr.barcode, cs.priority, cs.specimen_ulin from sample_reception sr left join covid_samples cs on sr.id = cs.package_id where cs.package_id = ".$packageID['id']. " order by cs.priority DESC ";

		$query = \DB::select($sql);

		return view('sample_reception.edit', compact('query'));
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
