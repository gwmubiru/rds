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

use App\Models\Facility;
use App\Models\IP;
use App\Models\Location\Hub;
use App\Models\Location\District;
use App\Models\Appendix;


class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		if(\Auth::user()->district_id == 139){
			dd('Access denied');
		}
		
		$users=User::getUsers();
		return view("users.index",compact("users"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		$user_roles=UserRole::userRolesArr();
		if(\MyHTML::is_facility_admin()){
			$user_roles = [34 => 'RDT Site User'];
		}
		$facilities_arr=Facility::facilitiesByDistrictsArr();
		$hubs_arr=Hub::hubsArr();
		$ips_arr=IP::ipsArr();
		$facilities=Facility::facilitiesArr();
		$distr_arr = District::districtsArr();
		$ref_lab_arr = \MyHTML::getRefLabs();
		return view("users.create",compact("user_roles","facilities_arr","hubs_arr","ips_arr", "facilities","distr_arr","ref_lab_arr"));
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
		if(isset($data['other_facilities'])) $data['other_facilities'] = serialize($data['other_facilities']);
		$data['created']=date('Y-m-d H:i:s');
		$data['createdby']=Session::get('username')?Session::get('username'):"system";

		$sign_tmp_name=$_FILES['signature']['tmp_name'];
		$sign_name=$_FILES['signature']['name'];
		$sign_ext=\MyHTML::getFileExt($sign_name);
		$data['signature']="/uploads/signs/".$data['username'].'.'.$sign_ext;
		if(!array_key_exists('facilityID', $data) || $data['facilityID'] == ''){
				$data['facilityID'] = 0;
		}
		if( $data['type'] == 34 || $data['type']== 39){
			$data['ref_lab'] = $data['facilityID'];
		}
		if(array_key_exists('limit_by', $data)) $data=$this->limit_by_option($data);
		if(!array_key_exists('district_id', $data)){
			$data['district_id'] = 0;
		}
		if(!array_key_exists('ref_lab', $data)){
			$data['ref_lab'] = 0;
		}
		if(array_key_exists('district_id', $data)){
			if($data['district_id'] == ''){
				$data['district_id'] = 0;
			}
		}
		if(array_key_exists('ref_lab', $data)){
			if($data['ref_lab'] == ''){
				$data['ref_lab'] = 0;
			}
			
		}

		//dd($data);
		$validator = Validator::make($data, User::$rules);
		if($validator->fails()){
			return redirect()->back()->withInput()->with('msge',trans('general.save_failure'));
		}else{

			$sign_dirname=public_path()."/uploads/signs";
			if(is_dir($sign_dirname) && is_writable($sign_dirname)){
				$savein_drive=move_uploaded_file($sign_tmp_name, public_path().$data['signature']);
			}else{
				return redirect()->back()->withInput()->with('msge',"Can't write to signature to directory $sign_dirname");
			}

			$user=User::create($data);
			if(array_key_exists('create_new', $data)){
				return redirect('users/create')->with('msge',trans('general.save_success'));
			}else{
				return redirect('users/show/'.$user->id)->with('msge',trans('general.save_success'));
			}
			//return redirect('user_roles/show/'.$facility->id)->with('msge',trans('general.save_success'));
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
		$user=User::getUser($id);
		return view("users.show",compact("user"));
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
		$user=User::findOrFail($id);
		$user_roles=UserRole::userRolesArr();
		if(\MyHTML::is_facility_admin()){
			$user_roles = [34 => 'RDT Site User'];
		}
		$facilities_arr=Facility::facilitiesByDistrictsArr();
		$hubs_arr=Hub::hubsArr();
		$ips_arr=IP::ipsArr();
		$facilities=Facility::facilitiesArr();
		$ref_lab_arr = \MyHTML::getRefLabs();
		$distr_arr = District::districtsArr();
		return view("users.edit",compact("user_roles","id","user","facilities_arr","hubs_arr","ips_arr", "facilities","ref_lab_arr","distr_arr"));
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
		$data=Request::all();
		$user=User::findOrFail($id);

		$sign_tmp_name=$_FILES['signature']['tmp_name'];
		$sign_name=$_FILES['signature']['name'];
		$sign_ext=\MyHTML::getFileExt($sign_name);
		$data['signature']="/uploads/signs/".$data['username'].'.'.$sign_ext;
		if(!array_key_exists('facilityID', $data) || $data['facilityID'] == ''){
				$data['facilityID'] = 0;
		}
		if( $data['type']== 34 || $data['type']== 39){
			$data['ref_lab'] = $data['facilityID'];
		}
		if(array_key_exists('limit_by', $data)) $data=$this->limit_by_option($data);
		if(!array_key_exists('district_id', $data)){
			$data['district_id'] = 0;
		}
		if(!array_key_exists('ref_lab', $data)){
			$data['ref_lab'] = 0;
		}
		if(array_key_exists('district_id', $data)){
			if($data['district_id'] == ''){
				$data['district_id'] = 0;
			}
		}
		if(array_key_exists('ref_lab', $data)){
			if($data['ref_lab'] == ''){
				$data['ref_lab'] = 0;
			}
			
		}
		//dd($data);
		$data['last_updated_at'] = date('Y-m-d H:i:s');
		$data['last_updated_by'] = \Auth::user()->id;
		
		if(isset($data['other_facilities']))  $data['other_facilities'] = serialize($data['other_facilities']);

		if(array_key_exists('limit_by', $data)) $data=$this->limit_by_option($data);

		$validator = Validator::make($data, User::$rules);
		if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));

		$sign_dirname=public_path()."/uploads/signs";
		if(is_dir($sign_dirname) && is_writable($sign_dirname)){
			$savein_drive=move_uploaded_file($sign_tmp_name, public_path().$data['signature']);
		}else{
			return redirect()->back()->withInput()->with('msge',"Can't write to signature to directory $sign_dirname");
		}

		$user->update($data);
		return redirect('users/show/'.$id)->with('msge',trans('general.edit_success'));
	}

	public function change_password($id){
		$user=User::findOrFail($id);
		return view("users.change_password",compact("id","user"));
	}

	public function post_change_password($id){
		  $user=User::findOrFail($id);
		  $user->password=Request::get('password');
		  $saved=$user->save();
		  if($saved) return redirect('users/index')->with('msge',"<p class='alert alert-success'>Password successfully changed for $user->family_name $user->other_name </p>");
		  else return redirect()->back()->withErrors($validator)->withInput()->with('msge',"Changing of password failed");
	}

	public function deactivate_account($id,$status){
		  $user=User::findOrFail($id);
		  $user->deactivated=$status;
		  $saved=$user->save();
		  $rply=$status==1?'deactivated':'activated';
		  if($saved) return redirect('users/index')->with('msge',"<p class='alert alert-success'>Account ($user->username) successfully $rply</p>");
		  else return redirect()->back()->withErrors($validator)->withInput()->with('msge',"Failure");
	}

	public function user_pwd_change(){
		return view("users.user_pwd_change");
	}

	public function post_user_pwd_change(){
		$data=Request::all();
		$validator = Validator::make($data, [
			'current_password'=>'required',
			'password'=>'required',
			'confirm_password'=>'required|same:password']);

		if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$user=\Auth::user();
		if(\Hash::check($data['current_password'], $user->password)){
			$user->password=$data['password'];
			$saved=$user->save();
			\Auth::logout();
			return redirect('/')->withFlashMessage("<p class='alert alert-success'>Password successfully changed</p>");
		}else{

			return redirect()->back()->withInput()->with('msge',"<p class='alert alert-danger'>Authentication Failure</p>");
		}
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

	private function limit_by_option($data){
		$data['facilityID']=$data['hubID']=$data['ipID']=0;
		switch ($data['limit_by']) {
			case '1':
			$data['facilityID']=$data['facility'];
			break;
			case '2':
			$data['hubID']=$data['hub'];
			break;
			case '3':
			$data['ipID']=$data['ip'];
			break;
			default:
			break;
		}
		return $data;
	}


//VerifyLoginRequest from app-validator
	public function appValidator(){

		$data = \Request::all();

		try {

			$user = User::where('username', '=', $data['username'])->first();
			$password = \Hash::check($data['password'], $user->password);

			if ($user === null || $password == false) {
				return response()->json("Incorrect Username or Password", 401);
			}
			elseif ($user == true && $password == true) {
				return response()->json("successfully logged in", 200);
			}

		} catch (\Exception $e) {
			return response()->json("Incorrect Username or Password-", 401);
		}

	}

}
