<?php namespace App\Http\Controllers;


use View;
use App\Forms\LoginForm;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\VerifyLoginRequest;
use App\Models\User;
use App\Models\UserRole;
use Mail;
use App\Models\Facility;
use Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\SessionsController;
class SessionsController extends Controller {

	/**
	 * @var Acme\Forms\LoginForm
	 */
	protected $loginForm;

	/**
	 * @param LoginForm $loginForm
	 */
	function __construct(LoginForm $loginForm)
	{
		$this->loginForm = $loginForm;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('sessions.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */


	public function store(VerifyLoginRequest  $request)
	{

		$credentials = \Input::only('username', 'password');

		if ($res=\Auth::attempt($credentials+['deactivated'=>'0']))
		{
			/*$jwt = JWTAuth::attempt($credentials);
			if(empty($jwt)) \Redirect::back()->withInput()->withFlashMessage('Login Failed');
			else \Response::json(compact('jwt'));*/
			$user=\Auth::user();
			$role=UserRole::findOrFail($user->type);
			$perms_arr=unserialize($role->permissions);
			$perm_parents_arr=unserialize($role->permission_parents);
			$poc_facility_name = "";
			$poc_district = "";
			if(!empty($user->poc_facility_id)){
				$poc_facility = Facility::getFacility($user->poc_facility_id);
				$poc_facility_name = $poc_facility->facility;
				$poc_district = $poc_facility->district;
			}

			session([
				'username'=>$user->username,
				'email'=>$user->email,
				'is_admin'=>$user->is_admin,
				'facility_limit'=>$user->facilityID,
				'hub_limit'=>$user->hubID,
				'ip_limit'=>$user->ipID,
				'permissions'=>$perms_arr,
				'permission_parents'=>$perm_parents_arr,
				'poc_facility_id'=>$user->poc_facility_id,
				'poc_facility_name'=>$poc_facility_name,
				'poc_district'=>$poc_district]);

				return \Redirect::intended('/otp');

		}

		return \Redirect::back()->withInput()->withFlashMessage('Login Failed');

	}

		public function verifyOTP(){

		$token = \Request::all();

		//check if otp is still valid (not older than 5mins)
		$dtNow = new \DateTime();
		$date = date('Y-m-d H:i:s');
		$dtToCompare =new \DateTime(\Auth::user()->otp_used_at);
		$t = \Request::all();

		$diff = $dtNow->diff($dtToCompare);
		$timeLapse = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

		if(\Auth::user()->verified_token == 0 && \Auth::user()->otp_token == preg_replace('/\s+/', '', $token['otp']) && $timeLapse < 120){
			\DB::select("update users set verified_token = 1, otp_used_at =". "'$date'". "where id = ". \Auth::user()->id);
			return \Redirect::intended('/');
		}

		elseif(\Auth::user()->otp_token != preg_replace('/\s+/', '', $token['otp'])) {
			return \Redirect::back()->with("auth_msge","<script>alert('Invalid Token')</script>");
		}

		else{
			return \Redirect::intended('/logout');
		}
	}

	public function store_OldVersion(VerifyLoginRequest  $request)
	{
		$credentials = Input::only('email', 'password');
		$jwt = JWTAuth::attempt($credentials) ?: null;

		return \Redirect::back()->withInput()->withFlashMessage('Login Failed');
	/*	if( $jwt == null ){// auth attempt failed
			return Redirect::back()->withInput()->withFlashMessage('Invalid credentials provided');
		}*/

		// auth succeeded:
		// $this->loginTo('http://chai.admin', $jwt);
		// return Redirect::intended('/');
		// auth attempt succeeded:
		$user = Auth::user();
		Auth::loginUsingId($user->id);
		return $this->loginTo('http://chai.admin/', $jwt);
		// return Redirect::home();
	}


	public function rlogin()
	{
		if(Auth::guest()){
			return "Please login first";// use flash msg instead
		}

		$user = Auth::user();

		if($user->is_admin){

			$jwt = JWTAuth::fromUser(Auth::user());
			return $this->loginTo('http://chai.admin/', $jwt);
		}
		else{
			return "You are not authorised to view admin page";
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id = null)
	{
		// \Auth::logout();
		\Session::flush();
		return redirect('/');
	}

	public static function php_session_getBool($key){

		if(empty($key)) return "false";

		if(Session::has($key)){
			return Session::get($key);
		}else{
			return "false";// works for me. YMMV
		}
	}


	public static function php_session_setBool($key){

		if(\Input::has($key)){

			if(\Input::get($key) === "YES")
				\Session::put($key, true);
			else
				\Session::put($key, false);
		}
	}


	public function loginTo($domain, $jwt){

		$url = $domain . "?token=" . $jwt;
		return redirect($url);
	}
}
