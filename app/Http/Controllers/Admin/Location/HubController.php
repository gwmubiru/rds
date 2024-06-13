<?php  namespace App\Http\Controllers\Admin\Location;

use App\Http\Requests;
use App\Http\Controllers\Admin\Controller;

//use Illuminate\Http\Request;
use App\Models\IP;
use App\Models\Location\Hub;

use Validator;
use Lang;
use Redirect;
use Request;
use Session;

class HubController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$hubs=Hub::hubsList();
		$post_url='locations/hubs/store';
		$ips=IP::ipsArr();		
		return view('locations.hubs',compact('ips','hubs','post_url','IP'));
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
		foreach ($hubs as $k => $v) {
			$data=array();
			$data['hub']=$v;
			$data['email']=$emails[$k];
			$data['ipID']=$ips[$k];
			$data['coordinator']=$coordinator[$k];
			$data['coordinator_contact']=$coordinator_contact[$k];
			$data['created']=date('Y-m-d H:i:s');
			$data['createdby']=Session::get('username')?Session::get('username'):"system";	
			$validator = Validator::make($data, Hub::$rules);
			if($validator->fails()) $errors++;
			else Hub::create($data);	
		}
		$msge=$errors>=1?trans('general.save_failure'):trans('general.save_success');
		
		return redirect('locations/hubs')->with('msge',$msge);

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
		$hubs=Hub::hubsList();
		$post_url='locations/hubs/update/'.$edit_id;
		$ips=IP::ipsArr();
		return view('locations.hubs',compact('ips','hubs','post_url','edit_id'));
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
		$hub = Hub::findOrFail($edit_id);
		$validator = Validator::make($data=Request::all(), Hub::$rules);
		if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$hub->update($data);
		return redirect('locations/hubs')->with('msge',trans('general.edit_success'));
	}

}
