<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//use Illuminate\Http\Request;
use App\Models\Appendix;
use App\Models\AppendixCategory;

use Validator;
use Lang;
use Redirect;
use Request;
use Session;

class AppendixController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($cat_id)
	{
		//
		if(!isset($cat_id) || empty($cat_id)){
			$cat_id=AppendixCategory::select('id')->get()->first()->id;
		}
		$appendices=Appendix::getByCat($cat_id);	
		$post_url="appendices/store/$cat_id";	
		return view('appendices.index',compact('appendices','cat_id','post_url'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($cat_id)
	{
		$posted=Request::all();
		extract($posted);
		$errors=0;		
		foreach ($appendix as $k => $apdx) {
			$data=array();
			$data['appendix']=$apdx;
			$data['code']=$apdx_code[$k];
			$data['categoryID']=$cat_id;
			$data['created']=date('Y-m-d H:i:s');	
			$data['createdby']=Session::get('username')?Session::get('username'):"system";	
			$rules=Appendix::$rules;
			$rules['appendix'].=$data['categoryID'];
			$rules['code'].=$data['categoryID'];
			$validator = Validator::make($data,$rules);
			if($validator->fails()) {
				$errors++;
			}else {
				$res=Appendix::create($data);
				if($res==false) $errors++;
			}
		}
		$msge=$errors>=1?trans('general.save_failure'):trans('general.save_success');
		
		return redirect("appendices/index/$cat_id")->with('msge',$msge);

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($cat_id,$edit_id)
	{
		//
		$appendices=Appendix::getByCat($cat_id);
		$post_url="appendices/update/$cat_id/$edit_id";
		return view('appendices.index',compact('appendices','post_url','edit_id','cat_id'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($cat_id,$edit_id)
	{
		//		
		$appendix = Appendix::findOrFail($edit_id);
		$data=Request::all();
		$data['categoryID']=$cat_id;

		/*$validator = Validator::make($data, Appendix::$rules);
		if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));*/

		$validator=Validator::make($data,[
			'appendix'=>"required|unique:appendices,appendix,$edit_id,id,categoryID,".$data['categoryID'],
			'code'=>"unique:appendices,code,$edit_id,id,categoryID,".$data['categoryID']
			]);
		if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$appendix->update($data);
		return redirect("appendices/index/$cat_id")->with('msge',trans('general.edit_success'));
	}

	public function deactivate($cat_id,$id,$status){
		  $appendix=Appendix::findOrFail($id);
		  $appendix->inactive=$status;
		  $saved=$appendix->save();
		  $rply=$status==1?'deactivated':'activated';
		  if($saved) return redirect("appendices/index/$cat_id")->with('msge',"<p class='alert alert-success'>Item ($appendix->appendix) successfully $rply</p>");
		  else return redirect()->back()->withErrors($validator)->withInput()->with('msge',"Failure");
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
