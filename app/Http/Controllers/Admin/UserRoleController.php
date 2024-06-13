<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Admin\Controller;

//use Illuminate\Http\Request;
use Request;
use Redirect;
use Session;
use Validator;

use App\Models\UserRole;
use App\Models\User;
use App\Models\UserPermission;

class UserRoleController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		//

		$user_roles=UserRole::all();
		$perms_list=UserPermission::permsListArr();
		
		$role_users_arr=User::role_users_arr();	
		return view("user_roles.index",compact("user_roles","perms_list","role_users_arr"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		$perm_arr=UserPermission::permsArr();
		return view("user_roles.create",compact("perm_arr"));
	}



	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$data=Request::all();
		$data['created']=date('Y-m-d H:i:s');
		$data['createdby']=Session::get('username')?Session::get('username'):"system";			
		$sep_arr=$this->separate($data['role_permissions']);
		$data['permissions']=serialize($sep_arr['children']);
		$data['permission_parents']=serialize($sep_arr['parents']);
		$validator = Validator::make($data, UserRole::$rules);
		if($validator->fails()){
			$messages = $validator->errors();
			return redirect()->back()->withInput()->with('msge',trans('general.save_failure'))->with(compact('messages'));
		}else{
			$user_role=UserRole::create($data);
			//return redirect('user_roles/index')->with('msge',trans('general.save_success'));
			return redirect('user_roles/show/'.$user_role->id)->with('msge',trans('general.save_success'));
		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		$user_role=UserRole::findOrFail($id);
		$perm_arr=UserPermission::permsArr();
		return view("user_roles.show",compact("user_role","perm_arr"));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
		$user_role=UserRole::findOrFail($id);
		$perm_arr=UserPermission::permsArr();

		/*$ids = array();
		foreach($perms_list as $k=>$v){
			$ids[] = $k;
		}	
		$toDatabse = (serialize($ids));
		dd($toDatabse);*/
		return view("user_roles.edit",compact("perm_arr","user_role"));
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
		$user_role = UserRole::findOrFail($id);
		$data=Request::all();
		$sep_arr=$this->separate($data['role_permissions']);
		$data['permissions']=serialize($sep_arr['children']);
		$data['permission_parents']=serialize($sep_arr['parents']);
		$data['last_updated_at'] = date('Y-m-d H:i:s');
		$data['last_updated_by'] = \Auth::user()->id;
		$validator = Validator::make($data, ["description" => "required|unique:user_roles,description,$id,id"]);
		if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$user_role->update($data);
		return redirect('user_roles/show/'.$id)->with('msge',trans('general.edit_success'));
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

	private function separate($role_permissions){
		$children=[];
		$parents=[];
		foreach ($role_permissions as $rp) {
			$arr=explode("_", $rp);
			if(!in_array($arr['0'], $parents)) $parents[]=$arr['0'];
			$children[]=$arr['1'];
		}
		return compact("children","parents");
	}

}
