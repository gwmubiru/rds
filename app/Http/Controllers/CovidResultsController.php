<?php 
namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Covid;
use App\Models\Facility;
use App\Models\District;
use App\Models\Location\Hub;
use App\Models\Appendix;
use App\Models\CovidResultHistory;
use Auth;
use Validator;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Closet\MyHTML as MyHTML;
class CovidResultsController extends Controller {

    public function index(){
        /*$rec = \DB::select("Select case_id,form_serial_number,date_of_case_report,patient_surname,patient_firstname,age,age_units,sex,patient_phone_number, 
phone_owner,patient_status, deathDate,household_head,patient_village,   patient_parish,patient_subcounty,patient_district,country_of_residence, patient_occupation,
type_of_business,
        type_of_transporter,
        health_worker_position,
        health_worker_facility,
        village_where_patient_fell_ill_from,
        subcounty_where_patient_fell_ill_from,
        district_where_patient_fell_ill_from,


        patient_home_gps_coordinates,
        resided_at_residence_from,
        resided_at_residence_to
        symptom_onset_date,
        symptoms,
        temperature_reading,
        source_of_temperature_reading,
        has_unexplained_bleeding,
        bleeding_symptoms,
        other_hemorrhagic_symptoms,
        other_nonhemorrhagic_symptoms,
        is_patient_admitted,
        hospital_admission_date,
        facility_admitted_at,
        facility_town,
        facility_subcounty,
        facility_district,
        is_patient_isolated,
        date_isolated,
        patient_previously_hospitalized ,
        previous_hospitalization_date,
        previously_hospitalized_at,
        previous_village_of_hospitalization,
        previous_district_of_hospitalization,
        patient_isolated_at_previous_hospitalization,
        did_patient_contact_known_suspect,
        name_of_patient_contact,
        patient_contact_relationship,
        date_of_exposure,
        village_of_contact,
        district_of_contact,
        status_of_contact,
        contact_death_date,
        contact_type,
        did_patient_attend_funeral,
        name_of_deceased,
        deceased_relation_to_patient,
        funeral_dates,
        village_of_funeral,
        district_of_funeral,
        did_patient_participate,
        did_patient_travel_outside_home,
        village_traveled_to,
        district_traveled_to,
        dates_of_travel,
        paid_visit_or_hospitalized_before_illness,
        name_of_patient_visited,
        patient_visit_dates,
        facility_where_patient_visited ,
        village_where_patient_visited,
        district_where_patient_visited,
        patient_visited_healer,
        name_of_healer,
        village_of_healer,
        district_of_healer,
        healer_visit_date,
        had_animal_contact,
        type_of_animal,
        animal_condition,
        patient_bitten_by_tick,
        sample_type,
        sample_collection_date,
        interviewer_name,
        interviewer_phone,
        interviewer_email,
        interviewer_position,
        interviewer_district,
        interviewer_facility,
        info_provided_by,
        proxy_name,
        proxy_relation_to_patient,
        result,
        organism,
        test_date,
        tested_by,
        test_type ,
        results_approver,
        approval_date,
        reviewed_by,
        review_date,
        lab_number,
        sample_reception_date,
        testing_lab,
        ct_value,
        ref_lab  from ebola_results WHERE id = 7785");
        //$json_str = json_encode($rec->toArray());
        dd(json_encode($rec[0]));*/
        if(\Auth::user()->id == 874){
            dd('Oops, an error has occured!');

        }
        //\Log::info((MyHTML::getIPAddresses()));
        $samples = \Request::get("samples");
        $page_type = base64_decode(\Request::get("type"));
        $printed = \Request::get("printed");
        $fro = \Request::get("fro");
        $to = \Request::get("to");
        $test_fro = \Request::get("test_fro");
        $test_to = \Request::get("test_to");
        $ref_lab = \Request::get("ref_lab");
        $patient_id = \Request::get("patient_id");
        $district = \Request::get("district");
        $test_result = \Request::get("test_result");
        $is_update = \Request::get("is_update");
        $ref_labs = [''=>''] + MyHTML::getRefLabs();
        if(!empty($_POST)){
            $fro = \Request::get("fro");
            $to = \Request::get("to");
            $p_type = \Request::get("p_type");
            $is_printed = \Request::get("printed");

            return \Redirect::Intended('/outbreaks/list?type='.base64_encode($p_type).'&printed='.$printed.'&fro='.$fro.'&to='.$to.'&patient_id='.$patient_id.'&district='.$district.'&test_result='.$test_result.'&test_fro='.$test_fro.'&test_to='.$test_to.'&ref_lab='.$ref_lab.'&is_update='.$is_update);
        }else{
            return view("covid_results.results", compact('page_type','printed','fro','to','patient_id','district','test_result','test_fro','test_to','ref_lab','ref_labs','printed','is_update'));
        }
        
    }

    public function list_data(){
        //if it is a post, then process
        $status_type = \Request::get("type");
        $printed = \Request::get("printed");
        $fro = \Request::get("fro");
        $to = \Request::get("to");
        $patient_id = \Request::get("patient_id");
        $district = \Request::get("district");
        $test_result = \Request::get("test_result");
        $test_fro = \Request::get("test_fro");
        $test_to = \Request::get("test_to");
        $ref_lab = \Request::get("ref_lab");
        $is_update = \Request::get("is_update");
        //enable searching
        $cols = ['','patient_id', 'district', 'patient_district','sentinel_site', 'date_of_collection', 'case_name',
        'age_years', 'sex', 'test_date','result','ref_lab_name','',''];
        $params = MyHTML::datatableParams($cols);
        $districts_arr = MyHTML::array_merge_maintain_keys([''=>''], District::where('id', '>', '0')->pluck('district', 'district'));
        extract($params);
        //\Log::info($orderby);
        $district_cond = '';
        //For district users, limit them to only data for their districts
        /*if(MyHTML::is_district_user()){
        //$district_cond = " AND district LIKE '%".MyHTML::getUserDistrict()."%'";
    }*/

    $search_cond ='';
    if(!empty($search)){
        if(MyHTML::is_district_user() || MyHTML::is_facility_dlfp_user()){
            $search_cond .= " AND (sentinel_site LIKE '%$search%' OR patient_district LIKE '%$search%' OR ref_lab_name LIKE '%$search%' OR patient_id LIKE '%$search%' OR specimen_ulin LIKE '%$search%' OR patient_id LIKE '%$search%' OR test_method LIKE '%$search%'";
        }elseif(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user()){
            $search_cond .= " AND (patient_district LIKE '%$search%' OR ref_lab_name LIKE '%$search%' OR patient_id LIKE '%$search%' OR specimen_ulin LIKE '%$search%' OR patient_id LIKE '%$search%' OR test_method LIKE '%$search%'";
        }else{
            $search_cond .= " AND (district LIKE '%$search%'";
            $search_cond .= " OR sentinel_site LIKE '%$search%' OR patient_district LIKE '%$search%' OR ref_lab_name LIKE '%$search%'  OR patient_id LIKE '%$search%' OR specimen_ulin LIKE '%$search%' OR patient_id LIKE '%$search%' OR test_method LIKE '%$search%'";
        }

        // " AND sentinel_site NOT LIKE '%Cabinet%'"
        //\Log::info($ref_lab);
        //\Log::info($search_cond);
        $arr =  explode(" ", $search);
        $arr_Size = count($arr);
        if($arr_Size == 1){
            $search_cond .= " OR case_name LIKE '%$arr[0]%')";
        }elseif($arr_Size == 2){
            $sear_str = $arr[0].' '.$arr[1];
            $search_cond .= "   OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[1].' '.$arr[0];
            $search_cond .= " OR case_name LIKE '%$sear_str%')";
        }elseif($arr_Size == 3){
            $sear_str = $arr[0].' '.$arr[1];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[1].' '.$arr[0];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[0].' '.$arr[2];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[2].' '.$arr[0];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[1].' '.$arr[2];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[2].' '.$arr[1];
            $search_cond .= " OR case_name LIKE '%$sear_str%')";
        }else{
            $sear_str = $arr[0].' '.$arr[1];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[1].' '.$arr[0];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[0].' '.$arr[2];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[2].' '.$arr[0];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[1].' '.$arr[2];
            $search_cond .= " OR case_name LIKE '%$sear_str%'";
            $sear_str = $arr[2].' '.$arr[1];
            $search_cond .= " OR case_name LIKE '%$sear_str%')";
        }
    }

    //where page_type is 1, and tab is pending,
    // stec users should have access to all results - distict id 139
    // || \Auth::user()->district_id == 139
    if(MyHTML::is_district_user() || MyHTML::isSpecialUser() || MyHTML::is_facility_dlfp_user()){
        $and_cond = " ";
    }else{
        //Don't show ST house results for elections
        $and_cond = " AND district NOT LIKE '%STEC%' AND sentinel_site NOT LIKE '%STEC%' ";
    }
    //turn off cabinet results for other users
    if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user()){
        //$user_site = MyHTML::getUserSiteOfCollection();
        //$and_cond .= " OR ref_lab_name LIKE '".$user_site."'";
    }elseif (MyHTML::isSpecialUser()) {
        //do nothing
    }else{
        //Don't show ST house results for elections
        $and_cond .= " AND sentinel_site NOT LIKE '%Cabinet%'";
    }
    //steck should not access cabinet results
    if(\Auth::user()->district_id == 139){
        $and_cond .= " AND sentinel_site NOT LIKE '%Cabinet%'";
    }
    if($status_type == 1){
        //$and_cond .= ' AND is_printed = '.$printed;
    }
    if(MyHTML::is_eoc()){
        $and_cond .= " AND  result = 'Negative'";
    }

    if(MyHTML::is_district_user() || MyHTML::is_facility_dlfp_user()){
        if(\Auth::user()->district_id != 139){
            $and_cond .= " AND  district LIKE '%".trim(MyHTML::getUserDistrict())."%'";
        }
        
    }

    if(MyHTML::is_classified_user()){
        //$and_cond .= " AND is_classified = 1";
        $search_cond .= '';
    }elseif (MyHTML::isSpecialUser()) {
        # code...
        // do nothing
        $and_cond .= " AND is_classified = 0";
    }else{
        $and_cond .= " AND is_classified = 0";
    }

    if(MyHTML::is_ec_user()){
        $and_cond .= " AND (patient_id  LIKE '%ELEC-COM%' OR patient_id  LIKE '%electro-com%' OR patient_district  LIKE '%electro-com%' OR patient_district  LIKE '%ELEC-COM%' OR sentinel_site LIKE '%ELECTRAL COMMISSION%' OR sentinel_site LIKE '%electro-com%' OR sentinel_site LIKE '%ELEC-COM%')";
        $search_cond .= '';
    }else{
        $and_cond .= " ";
    }
    if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user()){;
        $site_details = MyHTML::getUserSiteOfCollection();
        $and_cond .= " AND (sentinel_site LIKE '%".trim($site_details['facility_name'])."%' OR sentinel_site LIKE '%".trim($site_details['key_word'])."%' OR ref_lab_name LIKE '%".trim($site_details['facility_name'])."%')";
    }
    if(MyHTML::is_general_user()){
        $and_cond = " AND district NOT LIKE '%STEC%' AND sentinel_site NOT LIKE '%STEC%' AND sentinel_site NOT LIKE '%Cabinet%' ";

    }
    if(MyHTML::is_case_manager()){
        $and_cond .= " AND  result = 'Positive'";
    }
    if(MyHTML::is_ref_lab()){
        $and_cond .= " AND  ref_lab = ".\Auth::user()->ref_lab;
    }

    if(!empty($fro) && !empty($to)){
        $and_cond .= " AND DATE(date_of_collection) BETWEEN '$fro' AND '$to'";
    }
    if(!empty($test_fro) && !empty($test_to)){
        $and_cond .= " AND DATE(test_date) BETWEEN '$test_fro' AND '$test_to'";
    }

    if(!empty($patient_id)){
        $and_cond .= " AND patient_id LIKE '%$patient_id%' ";
    }
    if(!empty($district)){
        $and_cond .= " AND district LIKE '%$district%' ";
    }
    if(!empty($test_result)){
        $and_cond .= " AND result LIKE '%$test_result%' ";
    }
    if(!empty($ref_lab)){
        $and_cond .= " AND ref_lab = ".$ref_lab." ";
    }
    if(!empty($is_update)){
        //if update, show only results withoout swabb district
        $and_cond .= " AND (district = '' OR  district IS NULL)";
    }
    if(!empty($printed)){
        if($printed == 'undefined'){
            $printed = 0;
        }
        if($printed == 2){
            $and_cond .= " AND (is_printed = 0 OR is_printed = 1)";
        }else if($printed == 1){
            $and_cond .= " AND is_printed = 1 ";
        }else{
            $and_cond .= " AND is_printed = 0 ";
        }

    }else{
        $and_cond .= " AND is_printed = 0 ";
    }
    //\Log::info($and_cond);
    //\Log::info($search_cond);
    //make a general query to which other conditions will be added in case of filters and search
    $cphl_cond = '';

    if(\Auth::user()->id == 984){
        //$and_cond .= " AND receipt_number IS NOT NULL AND receipt_number <> '' ";
    }

    $query_main = "SELECT * FROM results WHERE sample_type <> 'Moore Swab' ".$cphl_cond.$and_cond.$search_cond.$district_cond;

    //$query_all_filters = $query_main.$and_cond.$district_cond.$search_cond.' ORDER BY '.$orderby.' LIMIT '.$length;
    //\Log::info($query_main);
    $results = \DB::select($query_main.$cphl_cond." ORDER BY $orderby LIMIT $start, $length");

    $recordsTotal = collect(\DB::select("select count(id) as num FROM results WHERE sample_type <> 'Moore Swab' ".$cphl_cond.$and_cond.$district_cond))->first()->num;
    $recordsFiltered = empty($search)?$recordsTotal:collect(\DB::select("select count(id) as num FROM results WHERE sample_type <> 'Moore Swab' ".$cphl_cond.$and_cond.$search_cond.$district_cond))->first()->num;
    $data = [];
    foreach ($results as $result) {
        $select_str = "<input type='checkbox' class='samples' name='samples[]' value='$result->id'>";
        $url = "/outbreaks/result/$result->id/?tab=".\Request::get('tab');
        $approve_edit_links = "/outbreaks/release_retain";
        $links = [];
        if($result->is_released==1){
            $links['Print'] = "javascript:windPop('$url')";
            $links['Download'] = "$url&pdf=1";
        }

        /*if($result->is_released==0 && MyHTML::permit(22)){
        $links['Release'] = "$approve_edit_links?type=approve&id=$result->id";
        $links['Edit'] = "$approve_edit_links?type=edit&id=$result->id";
    }*/
    if(MyHTML::permit(22)){
        //$links['Release'] = "$approve_edit_links?type=approve&id=$result->id";
        $links['Edit'] = "$approve_edit_links?type=edit&id=$result->id";
    }
    //if results is empty, add  inpput field
    //i.e, page is update....
    $district_input = '';
    if($is_update && MyHTML::permit(22)){
        //$district_input = "<input type='text' class='districts' name='districts[$result->id]'>";
        $district_input = MyHTML::select('districts['.$result->id.']',$districts_arr,$default='','res_'.$result->id,'rest_dr form-control input-sm');
    }else{
        $district_input = $result->district;
    }
    $sentinel_site = $result->sentinel_site;
    if(strtolower($sentinel_site) == 'Other'){
        $sentinel_site = $result->sentinel_other;
    }
    $lab_no = $result->specimen_ulin == ''?$result->serial_number_batch : $result->specimen_ulin;
    $data[] = [
    $select_str,
    $result->patient_id,
    $lab_no,
    $district_input,
    //$result->patient_district,
    $sentinel_site,
    MyHTML::localiseDate($result->date_of_collection,'d M Y'),
    $result->case_name,
    $result->age_years?$result->age_years.' Years':'',
    $result->sex,
    MyHTML::localiseDate($result->test_date,'d M Y'),
    MyHTML::localiseDate($result->created_at,'d M Y'),
    $result->result,
    $result->test_method,
    $result->ref_lab_name,
    MyHTML::specialDropdownLinks($links),
    $result->is_printed,
    ];

}
//the total number of records filtered -  based on search and filter conditions

return compact( "recordsTotal", "recordsFiltered", "data");
}
public function result($id=""){
    $vldbresult = [];
    if(!empty($id)){
        $samples = [$id];
    }else{
        $samples = \Request::get("samples");
        if(count($samples)==0){
            return "please select at least one sample";
        }
    }
    $vldbresult = $this->fetch_result($samples);
    $tab = \Request::get('tab');
    $print_version = "1.0";
    if(\Request::has('pdf')){

        $pdf = \PDF::loadView('covid_results.result_slip', compact('vldbresult', 'print_version'));
        return $pdf->download('covid_results_'.\Request::get('facility').'.pdf');


    }

    return view('covid_results.result_slip', compact('vldbresult', 'print_version'));

}

private function fetch_result($patients_ids, $f=0){

    $patient_ids_str = implode(",", $patients_ids);
    //mark each printed
    $result_slip_details = [];
    foreach($patients_ids as $patient_id){

        //get all patients for the patient_ids passed
        $sql = "SELECT r.*, r.id, a.`id` as ref_lab, a.facility as lab  FROM results r
        INNER JOIN facilities a ON (a.id = r.ref_lab)
        WHERE r.id in ($patient_ids_str)";

        $results = \DB::select($sql);

        //For each returned patient record, get child/parent patients based on district and facility.
        //Also return details of their samples and results

        foreach($results as $result){

            $condition = "  r.id = $result->id";
            $query = "SELECT r.*,  a.`id` as ref_lab, a.`facility` as lab FROM results r
            INNER JOIN facilities a ON (a.id = r.ref_lab)
            WHERE $condition GROUP BY r.id ORDER BY date_of_collection LIMIT 3";

            $results = \DB::select($query);
            // dd($results);
            $results_arr = [];
            //process the only 3 samples, where there is no data, use N/A
            for($i=0; $i < 3; $i++){
                $sample_result_array = ['collection_date' => 'N/A','test_date'=>'N/A','result' => 'N/A','signature'=>''];
                if(array_key_exists($i, $results)){
                    $sample_result_array = [
                    'collection_date' => MyHTML::localiseDate($results[$i]->date_of_collection,'d M Y'),
                    'specimen_collection_time' => date("h:i:s a",strtotime($results[$i]->date_of_collection)),
                    //'test_date'=>MyHTML::localiseDate($results[$i]->test_date,'d M Y'),
                    //'test_date'=> strtolower($results[$i]->test_method) == 'rdt' ? MyHTML::localiseDate($results[$i]->test_date,'Y-m-d H:i:s') : MyHTML::localiseDate($results[$i]->test_date,'d M Y') ,
                    'test_date'=> MyHTML::localiseDate($results[$i]->test_date,'d M Y'),
                    'test_time'=> date("h:i:s a",strtotime($results[$i]->test_date)),
                    'result' => $results[$i]->result,
                    'signature' => '',
                    'lab' => $results[$i]->lab
                    ];

                    $lab = \Request::get('lab');

                    //mark each result as printed
                    $result = Result::findOrFail($results[$i]->id);
                    $result->is_printed == 0 ? $result->download_counter = 1 : $result->download_counter =  $result->download_counter +1;
                    isset($lab) ? $result->printed_by = "CPHL/MoH" : $result->printed_by = Auth::user()->family_name . ' ' . Auth::user()->other_name;
                    $result->is_printed = 1;
                    $result->last_printed_on = date('Y-m-d H:i:s');
                    // dd($result);
                    $result->save();

                }
                $results_arr[$i] = $sample_result_array;
            }

            //generate the array to be used in the result slip
            $patient_arr = [];
            $patient_arr['patient_id'] = $result->patient_id;
            $patient_arr['district'] = $result->district;
            $patient_arr['patient_district'] = $result->patient_district;
            $patient_arr['specimen_ulin'] = $result->specimen_ulin;
            $patient_arr['specimen_type'] = $result->sample_type;
            $patient_arr['specimen_collection_date'] = $result->date_of_collection;
            $patient_arr['specimen_collection_time'] = date("h:i:s a",strtotime($result->date_of_collection));
            $patient_arr['sentinel_site'] = $result->sentinel_site;
            $patient_arr['sentinel_other'] = $result->sentinel_site_other;
            $patient_arr['case_name'] = $result->case_name;
            $patient_arr['passport_number'] = $result->passport_number;
            $patient_arr['case_phone'] = $result->case_contact;
            $patient_arr['interviewer_name'] = $result->requested_by;
            $patient_arr['interviewer_phone'] = $result->requester_contact;
            $patient_arr['test_date'] = MyHTML::localiseDate($result->test_date,'d M Y');
            $patient_arr['test_time']= date("h:i:s a",strtotime($result->test_date));
            $patient_arr['sex'] = $results[0]->sex;
            $patient_arr['age_years'] = $results[0]->age_years;
            $patient_arr['date_of_birth'] = $results[0]->date_of_birth;
            $patient_arr['type_of_site'] = $results[0]->type_of_site;
            $patient_arr['results'] = $results_arr;
            $patient_arr['ref_lab'] = $results[0]->ref_lab;
            $patient_arr['epidNo'] = $results[0]->patient_id;
            $patient_arr['case_id'] = $results[0]->case_id;
            $patient_arr['ulin'] = $results[0]->ulin;
            $patient_arr['result_id'] = $results[0]->id;
            $patient_arr['download_counter'] = $result->download_counter;
            $patient_arr['who_is_being_tested'] = $result->who_is_being_tested;
            $patient_arr['receipt_number'] = $result->receipt_number;
            $patient_arr['ref_lab_name'] = $result->ref_lab_name;
            $patient_arr['ct_value'] = $result->ct_value;
            $patient_arr['platform_range'] = $result->platform_range;
            $patient_arr['test_method'] = $results[0]->test_method;
            $patient_arr['testing_platform'] = $result->testing_platform;
            $patient_arr['printed_by'] = $result->printed_by;
            $patient_arr['email_address'] = $result->email_address;
            $patient_arr['email_sent'] = $result->email_sent;
            $result_slip_details[] = $patient_arr;
        }

        if(isset($lab)){

        $result_obj = array(
            'result_obj' => array(
                "patient_id" => $result_slip_details[0]["patient_id"],
                "district" => $result_slip_details[0]["district"],
                "patient_district" => $result_slip_details[0]["patient_district"],
                "specimen_ulin" => $result_slip_details[0]["specimen_ulin"],
                "specimen_type" => $result_slip_details[0]["specimen_type"],
                "specimen_collection_date" => $result_slip_details[0]["specimen_collection_date"],
                "specimen_collection_time" => $result_slip_details[0]["specimen_collection_time"],
                "sentinel_site" => $result_slip_details[0]["sentinel_site"],
                "sentinel_other" => $result_slip_details[0]["sentinel_other"],
                "case_name" => $result_slip_details[0]["case_name"],
                "passport_number" => $result_slip_details[0]["passport_number"],
                "case_phone" => $result_slip_details[0]["case_phone"],
                "interviewer_name" => $result_slip_details[0]["interviewer_name"],
                "interviewer_phone" => $result_slip_details[0]["interviewer_phone"],
                "test_date" => $result_slip_details[0]["test_date"],
                "test_time" => $result_slip_details[0]["test_time"],
                "sex" => $result_slip_details[0]["sex"],
                "age_years" => $result_slip_details[0]["age_years"],
                "type_of_site" => $result_slip_details[0]["type_of_site"],
                'results' => array("result" => $result_slip_details[0]['results'][0]["result"],
                "lab" => $result_slip_details[0]['results'][0]["lab"],
                "collection_date" => $result_slip_details[0]['results'][0]["collection_date"],
                "specimen_collection_time" => $result_slip_details[0]['results'][0]["specimen_collection_time"],
                "test_date" => $result_slip_details[0]['results'][0]["test_date"],
                "test_time" => $result_slip_details[0]['results'][0]["test_time"]),
                "signature" => $result_slip_details[0]['results'][0]["signature"],
                "ref_lab" => $result_slip_details[0]["ref_lab"],
                "epidNo" => $result_slip_details[0]["epidNo"],
                "case_id" => $result_slip_details[0]["case_id"],
                "ulin" => $result_slip_details[0]["ulin"],
                "result_id" => $result_slip_details[0]["result_id"],
                "download_counter" => $result_slip_details[0]["download_counter"],
                "who_is_being_tested" => $result_slip_details[0]["who_is_being_tested"],
                "receipt_number" => $result_slip_details[0]["receipt_number"],
                "ref_lab_name" => $result_slip_details[0]["ref_lab_name"],
                "ct_value" => $result_slip_details[0]["ct_value"],
                "platform_range" => $result_slip_details[0]["platform_range"],
                "test_method" => $result_slip_details[0]["test_method"],
                "testing_platform" => $result_slip_details[0]["testing_platform"],
                "printed_by" => $result_slip_details[0]["printed_by"]
            ));

            //check if client has email b4 sending result
            if($result_slip_details[0]['email_sent'] != 2 && isset($result_slip_details[0]['email_address'])){
                if($result_slip_details[0]["ref_lab"] == 2906 ){
                    $bcc_emails = ['bo5285710@gmail.com','abl@ancabiotech.com','results@ancabiotech.com'];
                    $client_email = $result_slip_details[0]['email_address'];
                }else{
                    $bcc_emails = [];
                    $client_email = $result_slip_details[0]['email_address'];

                }

                $pdf = \PDF::loadView('covid_results.email_result_slip_css', $result_obj);

                \Mail::send('covid_results.result_email_body',$result_obj,function ($message) use ($result_obj, $pdf,$bcc_emails,$client_email){
                    $message->to(explode(',',$client_email))
                    ->bcc($bcc_emails,'MoH')
                    ->subject('Covid-19 Result');
                    $message->attachData($pdf->output(),'covid-19_result.pdf');
                });

                \DB::select("update results set email_sent = 2 where id = ".$result_slip_details[0]["result_id"]." OR email_address = '$client_email' ");
                \Log::info($result_slip_details[0]['email_address']);
                return "email sent";
                }
            else{
                dd('no email');
            }
}


            return $result_slip_details;
        }
    }

    public function pendingEmailing(){
        //$pe = \DB::select("select id,ref_lab, email_address,email_sent from results where created_at > '2021-11-10' AND email_sent != 2 AND result like '%negative%' AND length(email_address)>10 AND ref_lab in (3013, 2906)");
        $pe = \DB::select("select id,ref_lab,email_address,email_sent,test_date, created_at from results  where email_sent != 2 AND email_address is not null AND  length(email_address) >= 10 AND created_at >= '2021-11-10' AND ref_lab in (3013)  group by email_address");
        return $pe;
    }

    public function emailResult(){
        $id = $this->pendingEmailing();

        for ($i=0; $i <sizeof($id) ; $i++) {
            return redirect("/outbreaks/result/".$id[$i]->id."/?tab=&amp;pdf=1&lab=1");
        }
    }

public function store_out(){

    $page_type = \Request::get('type');
    if($page_type == 'form'){
        return view("covid_results.upload");

    }else{
        //process form
        $fileInput_field = "csv";
        if( \Request::hasFile($fileInput_field) == false ){
            return "Upload Failed: No File was found - please select the file to upload";
        }

        if( \Request::file($fileInput_field)->isValid() == false ){
            return "File upload failed";
        }
        $file_name =  \Request::file($fileInput_field)->getClientOriginalName();
        $extension =  '.'.\Request::file($fileInput_field)->getClientOriginalExtension();

        $dest_folder = public_path().'/uploads/results';
        $dest_fileName = time(). $extension;
        $uploaded_file =  \Request::file($fileInput_field)->move($dest_folder, $dest_fileName);

        $uploaded_file = $dest_folder . "/" . $dest_fileName;
        //now save the file to db
        //dd($uploaded_file);
        $query = "select original_file_name from covid_results";
        $results = \DB::select($query);
        //check that the file being uploaded has not been yet uploaded
        $existing_file = \DB::select("SELECT id FROM results WHERE original_file_name LIKE '%$file_name%'");


        $unsaved_results = $this->save_file_results($uploaded_file,$file_name,$dest_fileName);
        $message = 'Results uploaded successfully';
        $message_type = 'success';
        if(count($unsaved_results)) {
            $message = "the case details for the following ids have not yet been captured, so their results have not been uploaded
            <br> ".implode(',', $unsaved_results);
            $message_type = 'danger';
        }
        return \Redirect::Intended('/outbreaks/list?type='.base64_encode(1))->with($message_type,$message);
    }
}
private function save_file_results($file_path,$original_file_name,$used_file_name){
    $file = fopen($file_path, 'r');
    //dd(\DB::getDatabaseName());
    $count = 0;
    while(($row = fgetcsv($file)) !== FALSE){
        $patient_not_found = [];
        if($count > 3){
            $epid_no = trim($row[1]);
            if($epid_no === 'PTC'){
                break;
            }else{
                //get the patient
                //$patient = Covid::where('epidNo', '=', $epid_no);
                $patient = \DB::select("SELECT id FROM covid_patients where epidNo = '".$epid_no."'");
                if(count($patient)){
                    if(trim($row[2]) == ''){
                        //negative result
                        $rslt = 0;
                    }else{
                        $rslt = 1;
                    }
                    $create_update = [
                    'test_result' => $rslt,
                    'patient_id' => $patient[0]->id,
                    'test_date' => date('Y-m-d'),
                    'uploaded_by' => \Auth::user()->id,
                    'original_file_name' => $original_file_name,
                    'used_file_name' => $used_file_name
                    ];
                    //dd($create_update);
                    //Result::updateOrCreate(['patient_id'=>$patient[0]->id],$create_update);
                    Result::updateOrCreate(['patient_id' => $patient[0]->id],$create_update);
                    /*$result = new Result;
                    $result->test_result = $rslt;
                    $result->patient_id = $patient[0]->id;
                    $result->test_date = date('Y-m-d');
                    $result->uploaded_by = \Auth::user()->id;
                    $result->original_file_name = $original_file_name;
                    $result->used_file_name = $used_file_name;
                    $result->save();*/
                }else{
                    //store the unstored patients
                    $patient_not_found[] = $epid_no;
                }
            }
        }
        $count++;
    }
    return $patient_not_found;
}

public function release_retain(){
    $page_type = \Request::get("type");
    $id = \Request::get("id");
    if($page_type == 'approve'){
        $query = "UPDATE results SET is_released = 1 WHERE id=$id";
        \DB::unprepared($query);
        $success_message = "Result approved successfully";
    }
    if($page_type == 'retain'){
        $query = "UPDATE results SET is_released = 2 WHERE id=$id";
        \DB::unprepared($query);
        $success_message = "Result retained successfully";
    }

    //if post, means user selected many for approval, so approve the samples
    if(\Request::isMethod('post')){
        $samples = \Request::get("samples");

        if(count($samples)==0){
            return "please select at least one sample";
        }else{
            $samples_str = implode(",", $samples);
            $query = "UPDATE results SET is_released = 1 WHERE id IN($samples_str)";
            \DB::unprepared($query);
            $success_message = "Results retained successfully";
        }
    }

    //return \Redirect::back()->withInput()->withFlashMessage('Login Failed');
    if($page_type == 'edit'){

        $result = Result::findOrFail($id);
        $genders = ['Male'=>'Male', 'Female'=>'Female'];
        $result_values = ['Positive'=>'Positive', 'Negative'=>'Negative'];
        $result_status = ['0'=>'Not Released', '1'=>'Released'];
        $districts = MyHTML::array_merge_maintain_keys([''=>'Select swabing district'], District::where('id', '>', '0')->pluck('district', 'district'));

        return View('outbreaks.edit_result',compact('page_type','result','genders','result_values','result_status','districts'));
    }
    return \Redirect::back()->with('success',$success_message);
}

public function update_outbreak_result(){

    //update the object
    $result = Result::findOrFail(\Request::get('id'));
    //now save the updates
    if(MyHTML::is_site_of_collection_editor()){
        $result->case_name = \Request::get("case_name");
        $result->patient_id = \Request::get("original_case_name");
    }else{
        $result->date_of_collection = \Request::get("date_of_collection");
        $result->sentinel_site = \Request::get("sentinel_site");
        $result->sentinel_site_other = \Request::get("sentinel_site_other");
        $result->patient_id = \Request::get("patient_id");
        $result->case_name = \Request::get("case_name");
        $result->who_is_being_tested = \Request::get('who_is_being_tested');
        $result->receipt_number = \Request::get('receipt_number');
        $result->passport_number = \Request::get("passport_number");
        $result->district = \Request::get("district");
        $result->patient_district = \Request::get("patient_district");
        $age = \Request::get("age_years");
        $result->age_years = $age?$age:0;
        $result->sex = \Request::get("sex");
    }
    if(MyHTML::isSpecialUser()){
        $result->is_classified = \Request::get("is_classified");
    }

    $result->last_updated_by = \Auth::user()->id;
    $result->save();

    $page_type = MyHTML::is_eoc()?1:0;
    return \Redirect::Intended('/outbreaks/list?type=MQ==&printed=2')->with('success','Result updated successfully');
}

public function sync_results($lab_id){
    $return_url = base64_decode(\Request::get("return"));
    $ref_lab = base64_decode($lab_id);


    //if($ref_lab == 23 || $ref_lab == 24 || $ref_lab ==16 || $ref_lab ==25 || $ref_lab ==22 || $ref_lab ==26 || $ref_lab ==27){
    if($ref_lab == 2909 || $ref_lab == 2910 || $ref_lab ==16 || $ref_lab ==2911 || $ref_lab ==2908 || $ref_lab ==187 || $ref_lab == 50
        || $ref_lab ==451 || $ref_lab ==460 || $ref_lab ==199 || $ref_lab ==257){
        $db_handle = \DB::connection('poe');
    }elseif ($ref_lab == 37) {
        $db_handle = \DB::connection('kazuri');
    }elseif ($ref_lab == 38) {
        $db_handle = \DB::connection('sameday');
    }elseif ($ref_lab == 34) {
        $db_handle = \DB::connection('case');
    }elseif($ref_lab == 39){
        //put cphl handle here\
        $db_handle = \DB::connection('maia');
    }elseif($ref_lab == 3014){
        //put cphl handle here\
        $db_handle = \DB::connection('jowa_lab');
    }elseif($ref_lab == 2918){
        //put cphl handle here\
        $db_handle = \DB::connection('city_medicals');
    }else{

    }

    $branch_cond = '';
    $b_id = \Request::get("branch_id");
    if($b_id){
        $b_id = ' AND s.branch_id='.$b_id;
    }
    /*if($ref_lab == 2892 || $ref_lab == 2893 || $ref_lab ==2890 || $ref_lab ==2894 || $ref_lab ==2891){
        $db_handle = \DB::connection('poe');
    }elseif ($ref_lab == 2907) {
        $db_handle = \DB::connection('kazuri');
    }elseif($ref_lab == 2904){
        $db_handle = \DB::connection('case');
    }else{
        //put cphl handle here
    }*/
    //p.last_vaccination_date,p.doses_received
    //AND patient_surname is not null AND p.swabing_district IS NOT NULL AND p.where_sample_collected_from IS NOT NULL
    $error_messages = '';
    $query = "SELECT p.epidNo,p.caseID,p.patient_surname,p.patient_firstname, p.age,p.age_units,p.sex,p.patient_contact,
    p.nationality,p.where_sample_collected_from,p.nameWhere_sample_collected_from,p.swabing_district,p.receipt_number,p.who_being_tested,
    s.specimen_collection_date, p.request_date, p.serial_number,p.foreignDistrict,pd.district as patientDistrict
    ,p.dataEntryDate,cr.test_result as test_result, cr.test_date as testing_date, p.ref_lab, cr.uploaded_by, d.district as swab_district,
    p.interviewer_name,p.interviewer_phone, s.specimen_type, cr.id  as result_id,s.specimen_ulin,s.ulin, p.is_classified, p.email_address,
    p.passportNo,cr.ct_value,cr.platform_range,cr.testing_platform, cr.test_method,p.eac_pass_id,p.is_vaccinated,p.ever_tested_positive,
    p.vaccine_type, p.date_of_birth

    FROM covid_samples s
    LEFT JOIN  covid_results cr ON s.id = cr.sample_id
    LEFT JOIN users u ON u.id = cr.uploaded_by
    LEFT JOIN covid_patients p ON p.id = s.patient_id
    LEFT JOIN districts pd ON (pd.id = p.patient_district)
    LEFT JOIN districts d ON(p.facility_district = d.id)
    WHERE cr.test_method <> 'RDT Antibody' AND cr.sample_id not in (select sample_id from covid_results rr left join covid_samples css on rr.sample_id = css.id where rr.is_synced = 1)
    AND cr.is_synced = 0".$b_id." AND p.ref_lab = ".$ref_lab." AND cr.is_released = 1  LIMIT 350 ";

    //\Log::info($query);
    $results = $db_handle->select($query);
    $exists_array = [];
    foreach($results as $result){
        $age_val = $result->age == '' ? 0 : $result->age;

        $recipt_date = $result->dataEntryDate == ''?$result->specimen_collection_date:$result->dataEntryDate;
        $s_district = $result->swabing_district !="" ? $result->swabing_district : $result->swab_district;
        if($result->ref_lab == 2908){
            $ref_l = 2891;
        }elseif($result->ref_lab == 2909){
            $ref_l = 2892;
        }elseif($result->ref_lab == 2910){
            $ref_l = 2893;
        }elseif($result->ref_lab == 2911){
            $ref_l = 2894;
        }elseif($result->ref_lab == 34){
            //case 
            $ref_l = 2904;
        }elseif($result->ref_lab == 37){
            //test exp - safari express
            $ref_l = 2919;
        }elseif($result->ref_lab == 38){
            //test exp - safari express
            $ref_l = 2920;
        }elseif($result->ref_lab == 39){
            //test exp - safari express
            $ref_l = 2925;
        }elseif($result->ref_lab == 187){
            //test exp - safari express
            $ref_l = 187;
        }elseif($result->ref_lab == 451){
            //Mbale RRH
            $ref_l = 451;
        }elseif($result->ref_lab == 460){
            //Mbale RRH
            $ref_l = 460;
        }elseif($result->ref_lab == 199){
            //Hoima RRH
            $ref_l = 199;
        }elseif($result->ref_lab == 3014){
            //Hoima RRH
            $ref_l = 3014;
        }elseif($result->ref_lab == 2918){
            //City Mdicals RRH
            $ref_l = 2918;
        }elseif($result->ref_lab == 50){
            $ref_l = 50;
        }elseif($result->ref_lab == 257){
            $ref_l = 257;
        }
        




        $create_update = [

        'date_of_collection' => $result->specimen_collection_date,
        'requester_contact' => $result->interviewer_phone,
        'requested_by' => $result->interviewer_name,
        'sample_type' => $result->specimen_type,
        'sample_received_on' => $recipt_date,
        'sentinel_site' => $result->nameWhere_sample_collected_from,
        'serial_number_batch' => $result->epidNo,
        'patient_id' => $result->epidNo == "" ? $result->specimen_ulin : $result->epidNo,
        'case_name' => $result->patient_surname.' '.$result->patient_firstname,
        'district' => $s_district,
        'patient_district' => $result->patientDistrict == "" ? $result->foreignDistrict : $result->patientDistrict,
        'age_years' =>$age_val,
        'sex' => $result->sex,
        'nationality' => $result->nationality,
        'case_contact' => $result->patient_contact,
        'email_address' => $result->email_address,
        'passport_number' => $result->passportNo,
        'result' => $result->test_result,
        'case_id' => $result->caseID,
        'test_date' => $result->testing_date,
        'ref_lab' => $ref_l,
        'ref_lab_name' =>  MyHTML::getRefLabName($ref_l),
        'uploaded_by' =>  $result->uploaded_by,
        'is_released' => 1,
        'specimen_ulin' => $result->specimen_ulin,
        'ulin' => $result->ulin,
        'is_classified' => $result->is_classified,
        'who_is_being_tested' => $result->who_being_tested,
        'receipt_number' => $result->receipt_number,
        'ct_value' => $result->ct_value,
        'platform_range' => $result->platform_range,
        'testing_platform' => $result->testing_platform,
        'test_method' => $result->test_method,
        'eacpass_id' => $result->eac_pass_id,
        'is_vaccinated' => $result->is_vaccinated,
        'ever_tested_positive' => $result->ever_tested_positive,
        //'last_vaccination_date' => $result->last_vaccination_date,
        //'doses_received' => $result->doses_received,
        'vaccine_type' => $result->vaccine_type,
        ];
        if(!empty($result->date_of_birth)){
            $create_update['date_of_birth'] = $result->date_of_birth;
        }
        //add custom date rules
        Validator::extend('before_or_equal', function($attribute, $value, $parameters) {
            //$attribute - collection date;
            //$value - value of collection date
            //value being compared - reception date;
            return strtotime($parameters[0]) >= strtotime($value);
        });
        $this->accepted_results = array('Positive', 'Negative', 'POSITIVE','NEGATIVE');
        $validator = Validator::make($create_update, [
            'specimen_ulin' => 'required|unique:results',
            'case_name' => 'required',
            'district' => 'required',
            //'date_of_collection'=>"required|date|before_or_equal:".$create_update['sample_received_on']."|before_or_equal:".date('Y-m-d H:i:s'),
            //'sample_received_on'=>"required|date|before_or_equal:".date('Y-m-d H:m:s',strtotime($create_update['test_date']))."|before_or_equal:".date('Y-m-d H:i:s'),
            'test_date'=>'required|date|before_or_equal:'.date('Y-m-d H:i:s'),
            'result' => 'required|in:' . implode(',', $this->accepted_results), 
        ],
        [
            'specimen_ulin.required' => 'The patient Id is required for row '.$result->specimen_ulin,
            'specimen_ulin.unique' => 'The lab number is already used '.$result->specimen_ulin,
            'district.required' => 'The swabbing district is required for row '.$result->specimen_ulin,
            'case_name.required' => 'The name is required for row '.$result->specimen_ulin,
           // 'date_of_collection.before_or_equal' => "The collection date cannot be greater than reception date or today's date for row ".$result->specimen_ulin,
           // 'sample_received_on.before_or_equal' => "The reception date cannot be greater than the test date or today's date for row ",
            'test_date.before_or_equal' => "The test date cannot be greater today's date for row ".$result->specimen_ulin,
            'result.in' => 'The only accepted values for result are: Positive, Negative. Check row '.$result->specimen_ulin,
        ]);
        if($validator->fails()) {
             $messages = $validator->errors();
             $return_msg = array();
             foreach ($messages->all() as $message) {
                 array_push($return_msg, $message);
             }
            // dd($return_msg);
             $error_messages[] = $return_msg;
         }else{
            //dd('created object');
            $result_obj = Result::Create($create_update);
            //now mark result as synced in the poe datatabase, if result is created
            $db_handle->statement("UPDATE covid_results set is_synced = 1 WHERE id = ".$result->result_id);
         }

     }
     \Log::info($error_messages);
        /*$is_date_valid = MyHTML::validateDates($result->specimen_collection_date,$recipt_date,$result->testing_date);

        $is_valid_result = MyHTML::validateResult(strtolower($result->test_result),$s_district, $result->specimen_ulin,$result->who_being_tested, $result->receipt_number);

        if(!Result::where('specimen_ulin', '=', $result->specimen_ulin)->where('ref_lab','=',$result->ref_lab)->first()){

                $result_obj = Result::Create($create_update);

                //now mark result as synced in the poe datatabase, if result is created
                    $db_handle->statement("UPDATE covid_results set is_synced = 1 WHERE id = ".$result->result_id);
            }
            else{
                $exists_array[] = $result->specimen_ulin;
            }
        }*/
        //\Log::info($exists_array);
        return \Redirect::to($return_url.'?redirected=1');
    }

    /*
    * Export results so that they can be used in mTrack to send notifications to result owners
    */

    public function exportToCsv(){
        $fro = \Request::get('exp_fro');
        $to = \Request::get('exp_to');
        $query = "SELECT patient_id, date_of_collection, case_name, case_contact, result
        FROM results
        where date_of_collection between '$fro' and '$to' AND (case_contact <> '' or case_contact is not null) AND result = 'Negative' AND sample_type <> 'Moore Swab'";

        $patients = \DB::select($query);


        header('Content-Type: text/csv; charset=utf-8');

        header("Content-Disposition: attachment; filename=Covid_data_$fro"."_$to.csv");

        $output = fopen('php://output', 'w');
        $headers = array(
        'LabID',
        'Sample Collection Date',
        'Name',
        'Telephone',
        'Results'

        );

        fputcsv($output, $headers);
        foreach ($patients as $patient) {
            $row=array(
            $patient->patient_id,
            $patient->date_of_collection,
            $patient->case_name,
            $patient->case_contact,
            $patient->result
            );
            fputcsv($output, $row);

        }

        fclose($output);
    }

    /*
    *mas update results with district where the sample was taken from -  swabbing district
    */
    public function massUpdateResults(){
        $districts = \Request::get('districts');
        //dd($districts);
        foreach($districts as $key =>$value){

            //update the result with the district value
            \DB::statement("UPDATE results SET district = '".$value."' WHERE id = ".$key);
        }
        return \Redirect::Intended('/outbreaks/list?type=MQ==&printed=2&is_update=1')->with('success','Results updated successfully');
    }

    //used for survailance CSV download
        public function exportData(){


        \Request::has('surv_fro') ? $fro = \Request::get('surv_fro') :  $fro = \Request::get('exp_fro');
        \Request::has('surv_to') ? $to = \Request::get('surv_to') : $to = \Request::get('exp_to');

            $and_cond = '';
            if(MyHTML::is_sample_archival_user()){
                $and_cond .= " AND result = 'Positive' AND ref_lab = 2891";
            }elseif(\Auth::user()->id == 1327){
                $and_cond .= " AND ref_lab = 2894";
            }elseif(MyHTML::sero_survey_user()){
                $and_cond .= " AND sentinel_site = 'Sero Survey'";
            }elseif(MyHTML::is_site_of_collection_user() || MyHTML::is_rdt_site_user() || MyHTML::is_site_of_collection_editor()){
                $and_cond .= " AND ref_lab = ".\Auth::user()->ref_lab;
            }elseif(MyHTML::is_site_of_collection_user() || MyHTML::is_rdt_site_user() || MyHTML::is_site_of_collection_editor()){
                $and_cond .= " AND ref_lab = ".\Auth::user()->ref_lab;
            }elseif(MyHTML::is_facility_dlfp_user() || MyHTML::is_district_user()){
                $and_cond .= " AND district = ".MyHTML::getUserDistrict();
                //Dr. Wordria needs data from only 3 districts
            }elseif(\Auth::user()->id == 4567){
                $and_cond .= " AND (district = 'kampala' or district = 'mukono' or district = 'wakiso')";
            }else{
            }

            $query = "SELECT * FROM results
            where sample_type <> 'Moore Swab' AND test_date between '$fro' and '$to' AND id NOT IN (30463,31897,31898) AND result IS NOT NULL AND result <> ''".$and_cond;

            $patients = \DB::select($query);


            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=Covid_data_$fro"."_$to.csv");

            $output = fopen('php://output', 'w');
            $headers = array(
            'Client ID',
            'Sample ID',
            'Client Name',
            'Client Contact',
            'Date of Collection',
            'Site of Collection',
            'Other ID',
            'Swabbing District',
            'Patient District',
            'Age',
            'Sex',
            'Reason For Testing',
            'Nationality',
            'Sample Receipt Date',
            'Test Date',
            'Testing Lab',
            'Result',
            'Test Method used',
            'Client ever tested Positive',
            'Client Vaccinated for Covid-19',
            'Vaccine Type',
            'Doses Received',
            'Last Vaccination Date'
            );

            fputcsv($output, $headers);
            foreach ($patients as $patient) {
                $row=array(
                $patient->patient_id,
                $patient->specimen_ulin,
                $patient->case_name,
                $patient->case_contact,
                $patient->date_of_collection,
                $patient->sentinel_site,
                $patient->serial_number_batch,
                $patient->district,
                $patient->patient_district,
                $patient->age_years,
                $patient->sex,
                $patient->who_is_being_tested,
                $patient->nationality,
                $patient->sample_received_on,
                $patient->test_date,
                $patient->ref_lab_name,
                $patient->result,
                $patient->test_method,
                $patient->ever_tested_positive,
                $patient->is_vaccinated,
                $patient->vaccine_type,
                $patient->doses_received,
                $patient->last_vaccination_date
                );
                fputcsv($output, $row);

            }

            fclose($output);
    }

}
