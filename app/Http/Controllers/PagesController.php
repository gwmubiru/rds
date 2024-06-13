<?php 
namespace App\Http\Controllers;
    
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PagesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(): View
	{
		//$auth=\Auth::check();
		//return $auth?View('pages.index'):View('sessions.create');
		return View('sessions.create');
	}

	//public function otp()
	//{
	//	$auth=\Auth::check();
	//	return $auth?View('pages.otp'):View('sessions.create');
	//}
	public function otp()
	{
		$auth=\Auth::check();
		list($first, $last) = explode("@", \Auth::user()->email); // split an email by "@"
		$len = floor(strlen($first)/3); // get 3rd of the length of the first part
		$email = substr($first, 0, $len) . str_repeat('*', $len) . "@" . $last; // partially hide a string by "*" and return full string

		return $auth ? View('pages.otp')->with(compact('email')):View('sessions.create');
	}

	protected function followUpFormAsPDF(){


		if(! \Request::has('f'))
			return "ERROR: No form number";


		$batch_id = \Request::get('f');
		\DB::unprepared("UPDATE facility_printing SET followup='dispatched' WHERE batch_id=$batch_id");

		$view = \View('art_init');
		$contents = $view->renderSections()['content'];

		$pdf = \PDF::loadHTML($contents);

		return $pdf->setOrientation('landscape')->stream("$batch_id".".pdf");
	}


	public function art()
	{

		if(\Request::has('fd'))
			return $this->followUpFormAsPDF();
		else
			return View('art_init');

	}

	public function ng_test()
	{
		return View('ng-ajax');
	}

	public function updateEmail(){

		$email = \Request::all();

		if(\Auth::user()->verified_token == 0 && \Auth::user()->otp_token == $token['otp'] && $timeLapse > 5){
			\DB::select("update users set verified_token = 1, otp_used_at =". "'$date'". "where id = ". \Auth::user()->id);
			return \Redirect::intended('/');
		}

		elseif(\Auth::user()->otp_token != $token['otp']){
			return \Redirect::back()->with("auth_msge","<script>alert('Invalid Token')</script>");
		}

		else{
			return \Redirect::intended('/logout');
		}

	}
}
