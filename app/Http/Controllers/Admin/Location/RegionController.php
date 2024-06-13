<?php namespace App\Http\Controllers\Admin\Location;

use App\Http\Requests;
use App\Http\Controllers\Admin\Controller;

//use Illuminate\Http\Request;
use App\Models\Location\Region;

use Validator;
use Lang;
use Redirect;
use Request;
use Session;

class RegionController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	
	public function index()
	{
		//
		$regions=Region::all();
		$post_url='locations/regions/store';
		return view('locations.regions',compact('regions','post_url'));
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
		foreach ($regions as $k => $v) {
			$data=array();
			$data['region']=$v;
			$data['created']=date('Y-m-d H:i:s');
			$data['createdby']=Session::get('username')?Session::get('username'):"system";	
			$validator = Validator::make($data, Region::$rules);
			if($validator->fails()) $errors++;
			else Region::create($data);	
		}
		$msge=$errors>=1?trans('general.save_failure'):trans('general.save_success');
		
		return redirect('locations/regions')->with('msge',$msge);

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($edit_id)
	{
		//
		$regions=Region::all();
		$post_url='locations/regions/update/'.$edit_id;
		return view('locations.regions',compact('regions','post_url','edit_id'));
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
		$region = Region::findOrFail($edit_id);
		$validator = Validator::make($data=Request::all(), Region::$rules);
		if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$region->update($data);
		return redirect('locations/regions')->with('msge',trans('general.edit_success'));
	}

}
