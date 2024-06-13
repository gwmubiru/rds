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
use App\Models\Branch;


class BranchController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		//
		$query = "SELECT b.id, b.name, d.district FROM branches b
			INNER JOIN districts d ON b.district_id = d.id";	
		$branches = \DB::select($query);	
		return view("branches.index",compact("branches"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{	
		$branch = '';	
		$distr_arr = District::districtsArr();
		return view("branches.create",compact("distr_arr","branch"));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data=Request::all();
		if($data['id']){
            $branch = Branch::findOrFail($data['id']);
            $msg = 'Branch updated successfully.';
           
        }else{
            $branch = new Branch;
            $msg = 'Sponsor added successfully.';
            $data['created_at']=date('Y-m-d H:i:s');
			$data['created_by']=\Auth::user()->id;	
        }
		
				

		$validator = Validator::make($data, Branch::$rules);
		if($validator->fails()){
			return redirect()->back()->withInput()->with('dnager',trans('general.save_failure'));
		}else{
			
			//$b=Branch::create($data);
			$branch->fill($data);
			$branch->save();
			
		}
		return \Redirect::Intended('/branches/index')->with('success','Branch added successfully');
	}

	public function edit($id)
	{
		$branch = Branch::findOrFail($id);
		$distr_arr = District::districtsArr();
		return view("branches.create",compact("branch","distr_arr"));
	}

}
