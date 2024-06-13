<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Admin\Controller;

//use Illuminate\Http\Request;
use Request;
use Redirect;
use Session;
use Validator;

use App\Models\Facility;
use App\Models\FacilityLevel;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\Location\Hub;
use App\Models\IP;
use App\Models\IPFacility;
use App\Models\FacilityHubHistory;

class FacilityController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		//
		$regions_filter=$districts_filter=$hubs_filter=$region_id=$district_id=$hub_id="";
		$data=Request::all();
		extract($data);

		if($regions_filter=='Filter' && !empty($region_id)){
			$facilities=Facility::getFacilitiesByRegion($region_id);
		}elseif($districts_filter=='Filter' && !empty($district_id)){
			$facilities=Facility::getFacilitiesByDistrict($district_id);
		}elseif($hubs_filter=='Filter' && !empty($hub_id)){
			$facilities=Facility::getFacilitiesByHub($hub_id);
		}else{
			$facilities=Facility::getFacilitiesAll();
		}
		
		$regions=Region::regionsArr();
		$districts=District::districtsArr();
		$hubs=Hub::hubsArr();
		return view("facilities.index",compact("facilities","regions","districts","hubs","region_id","district_id","hub_id"));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		$facility_levels=array(""=>"Select")+FacilityLevel::facilityLevelsArr();
		$districts=array(""=>"Select")+District::districtsArr();
		$hubs=array(""=>"Select")+Hub::hubsArr();
		$ips=array(""=>"Select")+IP::ipsArr();
		return view("facilities.create",compact("facility_levels","districts","hubs","ips"));
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
		$validator = Validator::make($data, Facility::$rules);
		if($validator->fails()){
			return redirect()->back()->withInput()->with('msge',trans('general.save_failure'));
		}else{
			$facility=Facility::create($data);
			$data['facility_id']=$facility->id;
			if(array_key_exists('ips', $data)) $this->_storeFacilityIPs($data);			
			return redirect('facilities/show/'.$facility->id)->with('msge',trans('general.save_success'));
		}

	}

	private function _storeFacilityIPs($data){
		foreach ($data['ips'] as $k => $ipID) {
			$ip_facility=array();
			$ip_facility['ipID']=$ipID;
			$ip_facility['facilityID']=$data['facility_id'];
			$ip_facility['start_date']=$data['start_date_y'][$k]."-".$data['start_date_m'][$k]."-1";
			$ip_facility['created']=date('Y-m-d H:i:s');
			$ip_facility['createdby']=Session::get('username')?Session::get('username'):"system";
			$exists=IPFacility::facilityIPExists($ip_facility);
			if(count($exists)==0) IPFacility::create($ip_facility);
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
		//$facility=Facility::find($id);
		$facility=Facility::getFacility($id);
		$facility_ips=IPFacility::getFacilityIPs($id);
		return view("facilities.show",compact("facility","facility_ips"));
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
		$facility=Facility::FindOrFail($id);
		$facility_levels=FacilityLevel::facilityLevelsArr();
		$districts=District::districtsArr();
		$hubs=Hub::hubsArr();
		$ips=array(""=>"Select")+IP::ipsArr();
		$facility_ips=IPFacility::getFacilityIPs($id);
		return view("facilities.edit",compact("facility_levels","districts","hubs","id","facility","ips","facility_ips"));
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
		$facility = Facility::findOrFail($id);
		$validator = Validator::make($data=Request::all(), Facility::$rules);
		if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput()->with('msge',trans('general.edit_failure'));
		$facility->update($data);
		$data['facility_id']=$id;
		if(array_key_exists('facility_ips', $data)) $this->_updateFacilityIPs($data);		
		if(array_key_exists('ips', $data)) $this->_storeFacilityIPs($data);
		if($data['old_hub']!=$data['hubID']){
			$fhh_data=[];
			$fhh_data['facility_id']=$facility->id;
			$fhh_data['hub_id']=$data['old_hub'];
			$fhh_data['start_date']=$data['created'];
			$fhh_data['end_date']=date('Y-m-d H:i:s');
			$fhh_data['created']=date('Y-m-d H:i:s');
			$fhh_data['createdby']=Session::get('username')?Session::get('username'):"system";
			FacilityHubHistory::create($fhh_data);
		} 

		return redirect('facilities/show/'.$id)->with('msge',trans('general.edit_success'));
	}

	private function _updateFacilityIPs($data){
		foreach ($data['f_ip_ids'] as $k => $f_ip_id) {
			$facility_ip=IPFacility::findOrFail($f_ip_id);
			$ip_facility=array();
			$ip_facility['ipID']=$data['facility_ips'][$k];
			$ip_facility['start_date']=$data['start_date_y'][$k]."-".$data['start_date_m'][$k]."-1";
			if(array_key_exists("stopped", $data)){
				if(array_key_exists($k, $data['stopped'])){
					$ip_facility['stopped']=$data['stopped'][$k];
					$ip_facility['stop_date']=$data['stop_date_y'][$k]."-".$data['stop_date_m'][$k]."-1";
				}
			} 
			$facility_ip->update($ip_facility);
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

	public function live_search($q){
		$facilities=Facility::searchFacilityByName($q);
		$list="";
		foreach($facilities as $f){
			$list.="<a href='/facilities/show/$f->id'>".$f->facility."</a><br>";
		}
		return $list;
	}

	public function results()
	{
		//
		session(['rtype'=>\Request::get('rtype')]);
		if(!empty(session('hub_limit'))){
			$cond = "f.hubID=".session('hub_limit');
		}elseif (!empty(session('facility_limit'))){
			$o_f = \Auth::user()->other_facilities;
			$o_facilities = !empty($o_f)?unserialize($o_f):[]; 
			$f_arr = array_merge([\Auth::User()->facilityID], $o_facilities);
			$f_str = implode(",", $f_arr);
			$cond = "facility_id in ($f_str)";
		}else{
			$cond = "1";
		}
		$facilities = Facility::facilityStats($cond);
		return view("facility_printing.facilities", compact("facilities"));
	}

}
