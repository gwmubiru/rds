<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//use Illuminate\Http\Request;
use App\Models\IP;

use Validator;
use Lang;
use Redirect;
use Request;
use Session;

class IPController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
			
		$ips=IP::all();		
		return view('ips.index',compact('ips'));
	}

	public function create()
	{
		return view("ips.create");
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */

	public function store()
	{
		//
		//signature
		$data=Request::all();
		$data['classification']='A';
		$data['created']=date('Y-m-d H:i:s');
		$data['createdby']=Session::get('username')?Session::get('username'):"system";
		
		$validator = Validator::make($data, IP::$rules);
		if($validator->fails()){
			return redirect()->back()->withInput()->with('msge',trans('general.save_failure'));
		}else{
			$ip=IP::create($data);
			if(array_key_exists('create_new', $data)){
				return redirect('ips/create')->with('msge',trans('general.save_success'));
			}else{
				return redirect('ips/show/'.$ip->id)->with('msge',trans('general.save_success'));
			}
			//return redirect('user_roles/show/'.$facility->id)->with('msge',trans('general.save_success'));
		}

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */

	public function show($id)
	{
		//
		$ip=IP::find($id);
		return view("ips.show",compact("ip"));
	}


	public function edit($id)
	{
		//
		$ip=IP::find($id);
		return view("ips.edit",compact("ip"));
	}
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//		
		$ip = IP::findOrFail($id);
		$validator = Validator::make($data=Request::all(), IP::$rules);
		if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$ip->update($data);
		return redirect('ips/show/'.$id)->with('msge',trans('general.edit_success'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
