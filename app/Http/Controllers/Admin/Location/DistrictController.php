<?php namespace App\Http\Controllers\Admin\Location;

use App\Http\Requests;
use App\Http\Controllers\Admin\Controller;

//use Illuminate\Http\Request;
use App\Models\Location\Region;
use App\Models\Location\District;

use Validator;
use Lang;
use Redirect;
use Request;
use Session;

class DistrictController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		//
		$districts=District::districtsList();
		//$districts=District::all();
		$post_url='locations/districts/store';
		$regions=Region::regionsArr();		
		return view('locations.districts',compact('regions','districts','post_url'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$posted=Request::all();
		extract($posted);
		$errors=0;		
		foreach ($districts as $k => $v) {
			$data=array();
			$data['district']=$v;
			$data['district_nr']=$district_nr[$k];
			$data['regionID']=$regions[$k];
			$data['scd_high_burden']=$scd_high_burden[$k];
			$data['created']=date('Y-m-d H:i:s');
			$data['createdby']=Session::get('username')?Session::get('username'):"system";	
			$validator = Validator::make($data, District::$rules);
			if($validator->fails()) $errors++;
			else District::create($data);	
		}
		$msge=$errors>=1?trans('general.save_failure'):trans('general.save_success');
		
		return redirect('locations/districts')->with('msge',$msge);

	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($edit_id)
	{
		//		
		$district = District::findOrFail($edit_id);
		$validator = Validator::make($data=Request::all(), District::$rules);
		if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$district->update($data);
		return redirect('locations/districts')->with('msge',trans('general.edit_success'));
	}

	

}
