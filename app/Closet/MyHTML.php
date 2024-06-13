<?php namespace App\Closet;
use Form;
use \App\Models\Facility;
use Spatie\Permission\Traits\HasRoles;
class MyHTML{
	public static function getImageData($file){
		$ret = "";
		$path = base_path('public/'.$file);
		if(file_exists($path)){
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$ret = 'data:image/' . $type . ';base64,' . base64_encode($data);
		}
		return $ret;
	}

	public static function text($name='',$val='',$clss='input_md',$id=null){
		return Form::text($name,$val,array('class'=>$clss,'id'=>$id));
	}

	public static function email($name='',$val='',$clss='input_md',$id=null){
		return Form::email($name,$val,array('class'=>$clss,'id'=>$id));
	}

	public static function hidden($name='',$val='',$id=null,$clss=null){
		return Form::hidden($name,$val,array('id'=>$id,'class'=>$clss));
	}

	public static function select($name,$arr,$default='',$id=null,$clss=null,$onchange=null){
		return Form::select($name,$arr,$default,array('id'=>$id,'class'=>$clss,'onchange'=>$onchange));
	}

	public static function submit($label='Submit',$clss='btn btn-primary',$name=null){
		return Form::submit($label,array('class'=>$clss,'name'=>$name));
	}

	public static function link_to($url='/',$label='link',$clss=null,$onclick=null){
		return link_to($url,$label,array('class'=>$clss,'onclick'=>$onclick));
	}

	public static function checkbox($name="",$value="",$label="",$id=null,$onclick=null){
		$checkbox=Form::checkbox($name,$value,0,['id'=>$id,'onclick'=>$onclick]);
		return "<label class='checkbox-inline'> $checkbox $label</label>";
	}

	public static function datepicker($name,$value,$id){
		$txt=MyHTMl::text($name,$value,null,$id);
		$script="<script> $(function() { $( \"#$id\" ).datepicker(); }); </script>";
		return "$txt $script";
	}

    public static function datepicker2($name,$value,$id){
		$txt=MyHTMl::text($name,$value,null,$id);
		$script="<script> $(function() { $( \"#$id\" ).datepicker(); });";
		return "$txt $script";
	}

	public static function tinyImg($img,$hite=25,$wdth=25){
		return "<img src='/images/$img' height='$hite' width='$wdth'>";
	}

	public static function radio($name="name",$value="1",$fld_value="",$label="",$clss="",$id="",$onchange=""){
		$sChecked=$value==$fld_value?'checked':'';
		$sClss=!empty($clss)?"class='$clss'":"";
		$sId=!empty($id)?"id='$id'":"";
		$sOnChange=!empty($onchange)?"onchange='$onchange'":"";
		return "<label><input type=radio name='$name' value='$value' $sChecked $sClss $sId $sOnChange > $label</label>";
	}

	public static function localiseDate($date,$format='m/d/Y'){
		return !empty($date) && $date!='0000-00-00 00:00:00' && $date!='0000-00-00'?date($format,strtotime($date)):"";
	}

	public static function formatDate2STD($date){
		$date_arr=explode("/", $date);
		if(count($date_arr)==3) return $date_arr[2]."-".$date_arr[1]."-".$date_arr[0];
		else return "";

	}

	public static function monthYear($name,$is_arr,$y=null,$m=''){
		$y_name=$is_arr==1?$name."_y[]":$name."_y";
		$m_name=$is_arr==1?$name."_m[]":$name."_m";
		if(empty($y) || $y==0) $y=date('Y');
		return MyHTML::selectMonth($m_name,$m)." ".Form::text($y_name,$y,array('class'=>'input_tn','place_holder'=>'YYYY','maxlength'=>'4'));
	}

	public static function selectMonth($name,$val,$id=null){
		$months=[1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sept',10=>'Oct',11=>'Nov',12=>'Dec'];
		return MyHTML::select($name,$months,$val,$id);
	}

	//to be used under javascript
	public static function text2($name,$val){
		return "<input type='text' name='$name' value='$val'>";
	}

	public static function select2($name='',$arr=array(),$default=""){
		$ret="<select name='$name'>";
		foreach ($arr as $k => $v) {
			$slcted=$k==$default?'selected':'';
			$ret.="<option $slcted value='$k'>$v</option>";
		}
		return $ret."</select>";
	}

	public static function getFileExt($file_name){
		$arrr=explode('.', $file_name);
		return array_pop($arrr);
	}

	public static function anchor($url="",$label="",$permission=" ",$attributes=[]){
		$lnk="";
		if(\Auth::check()){

			if(\Auth::user()->can($permission)){
				//dd('heer');
				$attr_str="";
				foreach ($attributes as $k => $v)  $attr_str.=" $k='$v' ";
				$lnk="<a $attr_str href='$url'>".$label."</a>";
				//$lnk=Form::link_to($url,$label,$attributes);
			}
			//dd('not working');
			return $lnk;
		}
	}

	public static function permit($permission){
		if(\Auth::check()){
			if(\Auth::user()->hasPermissionTo($permission)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public static function datatableParams($cols){
    	$order = \Request::get('order');
    	$orderby = "$cols[0] asc";
		if(isset($order[0])){
			$col = $cols[$order[0]['column']];

			$dir = $order[0]['dir'];
			$orderby = "$col $dir";
		}

		$search = \Request::has('search')?\Request::get('search')['value']:"";
		$search = trim($search);

		$start = \Request::get('start');
		$length = \Request::get('length');

		return compact('orderby', 'start', 'length', 'search');
    }

	public static function lowNumberMsg($nSamples, $nSamplesNeeded = 22){
		$ret="";
		if($nSamples==0){
			$ret="<p class='alert alert-danger'>Sorry no samples approved for worksheet creation</p>";
		}elseif($nSamples<$nSamplesNeeded){
			$x=$nSamplesNeeded-$nSamples;
			$ret="<p class='alert alert-danger'>Sorry you need more $x samples for worksheet creation</p>";
		}else{
			$ret="";
		}
		return $ret;
	}

	public static function months(){
		return [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sept',10=>'Oct',11=>'Nov',12=>'Dec'];
	}

	public static function initMonths(){
		$ret=[];
		for($i=1;$i<=12;$i++){
			$ret[$i]=0;
		}
		return $ret;
	}

	public static function years($min="",$max=""){
		if(empty($min)) $min=1900;
		if(empty($max)) $max=date('Y');
		if($max<$min) return [];
		$yrs_arr=[];
		for($i=$max;$i>=$min;$i--) $yrs_arr[$i]=$i;
		return $yrs_arr;
	}

	public static function cleanAge($age=0){
		$ret=0;
		$age_arr=explode(" ", $age);
		$years=0;$months=0;$weeks=0;$days=0;
		foreach ($age_arr as $k => $val) {
			if($val=='year'||$val=='years'){
				$years=str_replace(" ", "",$age_arr[($k-1)]);
			}elseif($val=='months'||$val=='month'){
				$months=str_replace(" ", "",$age_arr[($k-1)]);
			}elseif($val=='weeks'||$val=='week'){
				$weeks=str_replace(" ", "",$age_arr[($k-1)]);
			}elseif($val=='days'||$val=='day'){
				$days=str_replace(" ", "",$age_arr[($k-1)]);
			}else{
				$months=$val;
			}
		}
		$ret= ($years*12)+$months+($weeks/4)+($days/30);
		return round($ret,2);
	}

	public static function dropdownLinks($label="Options",$links=[]){
		$ret = "<span class='dropdown'><button class='btn btn-xs btn-primary dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>";
		$ret .= "$label <span class='caret'></span></button>";
		$ret .= "<ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>";
		foreach ($links as $k => $v) {
			$ret .= "<li role='presentation'><a role='menuitem' class='link' href=\"$v\">$k</a></li>";
		}
		$ret .= "</ul></span>";
		return $ret;
	}


	public static function specialDropdownLinks($links=[]){
		$ret = "<div class='dropdown'><button class='btn btn-xs btn-primary dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>";
		$ret .= "Options <span class='caret'></span></button>";
		$ret .= "<ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>";
		foreach ($links as $k => $v) {
			$ret .= "<li role='presentation'><a role='menuitem' href=\"$v\">$k</a></li>";
		}
		$ret .= "</ul></div>";
		return $ret;
	}

	public static function SCDResult($result){
	  $scd_arr = [
	    'SICKLER' => 'HB SS (Sickle Cell Disease)',
	    'CARRIER' => 'HB AS (Sickle Cell Trait)',
	    'VARIANT' => 'HB AV (Variant Hemoglobin)',
	    'NORMAL' => 'HB AA (Normal Hemoglobin)',
	    'FAILED' => 'INVALID',
	    'INVALID' => 'INVALID'
	  ];

	  if(array_key_exists($result, $scd_arr)) return $scd_arr[$result];
	  else return "";

	}

	public static function SCDExplanation($result){
	  $scd_notes_arr = [
	    'SICKLER' => "The child has sickle cell disease and needs to receive medical care at a health facility. Sickle cell disease causes abnormally shaped red blood cells and numerous health complications, including infection and anaemia. The child should receive the full series of pneumococcal vaccinations promptly and also penicillin prophylaxis until the age of 5 years",
	    'CARRIER' => "The child is a sickle cell carrier, but does not have sickle cell disease. The child will not have symptoms or health complications of the sickle cell desease",
	    'VARIANT' => "The child does not have sickle cell trait or sickle cell disease, but has unusual finding of unclear significance.",
	    'NORMAL' => "The child has normal hemoglobin and does not have sickle cell trait or sickle cell disease.",
	    'FAILED' => "Invalid test due to poor sample integrity. A new sample should be sent",
	    'INVALID' => "Invalid test due to poor sample integrity. A new sample should be sent"
	  ];

	  if(array_key_exists($result, $scd_notes_arr)) return $scd_notes_arr[$result];
	  else return "";

	}

	public static function dateDiff($date1, $date2){
		$date1 = !empty($date1)?$date1:date("Y-m-d");
		$date2 = !empty($date2)?$date2:date("Y-m-d");
		$days = round((strtotime($date1)-strtotime($date2))/86400);
		return abs($days);
	}

	public static function getHepBFacilityID(){
		$eid_facility_id = \Auth::User()->facilityID;
		$hepb_facility_id = '';
		if($eid_facility_id){
			$query = "SELECT facility FROM facilities WHERE id = ".trim($eid_facility_id);
			$eid_facility_name = \DB::select($query);
			//now get the hepb facility id based on the eid facility name
			$sql = 'SELECT id FROM backend_facilities WHERE facility LIKE "%'.trim($eid_facility_name[0]->facility).'%"';

			$idresult =  \DB::connection('mysql_hepb')->select($sql);
			$hepb_facility_id = $idresult[0]->id;
		}
		return $hepb_facility_id;/**/
	}
	public static function getHepBHubID(){
		$eid_hub_id = \Auth::User()->hubID;
		$hepb_hub_id = '';
		if($eid_hub_id){
			$query = "SELECT hub FROM hubs WHERE id = ".$eid_hub_id;
			$eid_hub_name = \DB::select($query);
			//now get the hepb facility id based on the eid facility name
			$sql = 'SELECT id FROM backend_hubs WHERE hub LIKE "%'.trim($eid_hub_name[0]->hub).'%"';
			$idresult =  \DB::connection('mysql_hepb')->select($sql);
			$hepb_hub_id = $idresult[0]->id;
		}
		return $hepb_hub_id;
	}

	public static function getHepFacilityIDs($facility_ids){
		$hepb_facilities = [];
		foreach($facility_ids as $key=>$vaule){
			$query = "SELECT facility FROM facilities WHERE id = ".$vaule;
			$eid_facility_name = \DB::select($query);

			//now get the hepb facility id based on the eid facility name
			$sql = 'SELECT id FROM backend_facilities WHERE facility LIKE "%'.trim($eid_facility_name[0]->facility).'%"';
			$idresult =  \DB::connection('mysql_hepb')->select($sql);
			$hepb_facility_id = $idresult[0]->id;
			array_push($hepb_facilities, $hepb_facility_id);
		}
		return $hepb_facilities;
	}

	public static function getNumericalResult($result=""){
		$numericVLResult = 0;
		$numericVLResult = preg_replace("/Copies \/ mL/s","",$result);
		$numericVLResult = preg_replace("/,/s","",$numericVLResult);
		$numericVLResult = preg_replace("/\</s","",$numericVLResult);
		$numericVLResult = preg_replace("/\&lt;/s","",$numericVLResult);
		$numericVLResult = preg_replace("/\&gt;/s","",$numericVLResult);
		$numericVLResult = trim($numericVLResult);
		return $numericVLResult;
	}

	public static function isSuppressed2($result,$sample_type="",$test_date=""){
		$ret="";
		$valid = self::isResultValid($result);
		$test_date_str=strtotime($test_date);
		if($valid=='YES'){
			if(empty($sample_type) && empty($test_date)){
				$ret = $result<=1000?"YES":"NO";
				return $ret;
			}
			if($test_date_str<1459458000){//use previous suppression criteria if before 2016-04-01 00:00:00
				if($sample_type=="DBS"){
					$ret=$result>5000?"NO":"YES";
				}else{
					$ret=$result>1000?"NO":"YES";
				}
			}else{
				$ret=$result<=1000?"YES":"NO";
			}
		}else{
			$ret="UNKNOWN";
		}
		return $ret;
	}

	private static function isResultValid($result){
		$ret="";
		$invalid_cases=array(
			"Failed","Failed.","Invalid",
			"Invalid test result. There is insufficient sample to repeat the assay.",
			"There is No Result Given. The Test Failed the Quality Control Criteria. We advise you send a new sample.",
			"There is No Result Given. The Test Failed the Quality Control Criteria. We advise you send a new sample.");

		if(in_array($result, $invalid_cases)) $ret="NO";
		else $ret="YES";
		return $ret;
	}

	public static function getRecommendation($suppressed,$test_date,$sample_type, $dob){
		$today = date('Y-m-d');
		$ret="";
		$nxt_date = "";
		$rec_suppressed_adults="Below 1,000 copies/mL: Patient is suppressing their viral load. <br>Please continue adherence counseling. Do another viral load after 12 months.";
		$rec_suppressed_kids="Below 1,000 copies/mL: Patient is suppressing their viral load. <br>Please continue adherence counseling. Do another viral load after 6 months.";
		$rec_unsuppressed="Above 1,000 copies/mL: Patient has elevated viral load. <br>Please initiate intensive adherence counseling and conduct a repeat viral load test within 4-6 months.";
		//$rec_unsuppressed="&ge; 1,000 copies/mL. Patient has unsuppressed viral load.";
		/*$rec_unsuppressed.="<ul>";
		$rec_unsuppressed.="<li>Please screen/test  for OI- crag and ";
		$rec_unsuppressed.="initiate intensive adherence counseling</li> ";
		$rec_unsuppressed.="<li>Repeat viral load test within 4­ - 6 months. </li>";
		$rec_unsuppressed.="<li>Next VL test Expected in Oct, 2016. Send 2 samples. One for VL test. One for HIVDR test</li>";
		$rec_unsuppressed.="</ul>";
		$ret = $rec_unsuppressed;*/

		$msg = "Expected in ";
		if($suppressed=='NO'){
			$ret = $rec_unsuppressed;
			$nxt_date =  " ($msg ".date('M, Y', strtotime($test_date)+(121*24*3600)).")";
		}elseif($suppressed == 'YES'){
			$yrs = (strtotime($today)-strtotime($dob))/(3600*24*365);
			if($yrs<20){
				$ret = $rec_suppressed_kids;
				$nxt_date =  " ($msg ".date('M, Y', strtotime($test_date)+(182*24*3600)).")";
			}else{
				$ret = $rec_suppressed_adults;
				$nxt_date =  " ($msg ".date('M, Y', strtotime($test_date)+(365*24*3600)).")";
			}
		}else{
			$ret = "";
			$nxt_date = "";
		}

		return $ret.$nxt_date;
	}

	public static function unsuppressedRecomm($next_date, $tx_line=""){
		$rec_unsuppressed="&ge; 1,000 copies/mL. Patient has unsuppressed viral load.";
		$rec_unsuppressed.="<ul>";
		if ($tx_line==2){
			$rec_unsuppressed.="<li>Screen for OIs - Do CrAg screening if client has a new WHO stage III or IV event";
			$rec_unsuppressed.=" and initiate intensive adherence counseling</li> ";
			$two_samples = "Send 2 venous samples. One for VL test. One for HIVDR test";
		}else{
			$rec_unsuppressed.="<li>Please initiate intensive adherence counseling</li> ";
			$two_samples = "";
		}

		$rec_unsuppressed.="<li>Repeat viral load test within 4­ - 6 months. </li>";
		$rec_unsuppressed.="<li>Next VL test Expected in $next_date. $two_samples</li>";
		$rec_unsuppressed.="</ul>";
		return $rec_unsuppressed;
	}

	public static function getRecommendation2($suppressed, $date_collected, $dob, $tx_line=""){
		$today = date('Y-m-d');
		$rec_suppressed_adults="< 1,000 copies/mL: Patient is suppressing their viral load. <br>Please continue adherence counseling. Do another viral load after 12 months.";
		$rec_suppressed_kids="< 1,000 copies/mL: Patient is suppressing their viral load. <br>Please continue adherence counseling. Do another viral load after 6 months.";

		$date_collected = empty($date_collected)?$today:$date_collected;
		if($suppressed==1){
			$yrs = (strtotime($date_collected)-strtotime($dob))/(3600*24*365);
			if($yrs<20){
				$ret =  "$rec_suppressed_kids (Expected in ".date('M, Y', strtotime($date_collected)+(182*24*3600)).")";
			}else{
				$ret =  "$rec_suppressed_adults (Expected in ".date('M, Y', strtotime($date_collected)+(365*24*3600)).")";
			}
		}elseif($suppressed==2){
			$nxt_date =  date('M, Y', strtotime($date_collected)+(121*24*3600));
			$ret = MyHTML::unsuppressedRecomm($nxt_date, $tx_line);
		}
		return $ret;
	}

	public static function get_arr_pair($result, $name=''){
		$ret = [];
		foreach ($result as $res) {
			$ret[$res->id] = $res->$name;
		}
		return $ret;
	}

	public static function boolean_draw($arr,$val){
		$ret="";
		// $checked="<span class='glyphicon glyphicon-check print-check'></span>";
		// $unchecked="<span class='glyphicon glyphicon-unchecked print-uncheck'></span>";
		// $checked="<input type='checkbox' checked disabled readonly>";
		// $unchecked="<input type='checkbox' disabled readonly>";
		$checked = "<img src='".MyHTML::getImageData("images/chkbox_chk.gif")."'>";
		$unchecked = "<img src='".MyHTML::getImageData("images/chkbox_blk.gif")."'>";
		foreach ($arr as $x => $label) {
			$prefix = $x==$val?$checked:$unchecked;
			$ret .= "$prefix $label&nbsp;&nbsp;";
		}

		return substr($ret,0,-12);
	}


	public static function getVLNumericResult($result,$machineType,$factor) {
	//check machine types
		if($machineType=="roche" || $machineType=="abbott") {
			//check conditions
			if($result=="Not detected" || $result=="Target Not Detected" || $result=="Failed" || $result=="Invalid") {
				return $result;
			} elseif(substr(trim($result),0,1)=="<") {
				//clean the result remove "Copies / mL" and "," from $result
				$result=preg_replace("/Copies \/ mL/s","",$result);
				$result=preg_replace("/,/s","",$result);
				$result=preg_replace("/\</s","",$result);
				$result=trim($result);
				/*
				* do not multiply by factor, based on a 17/Sept/14 discussion
				* with Christine at the CPHL Viral Load Lab
				* $result*=$factor;
				*/

				//return
				return "&lt; ".number_format((float)$result,2)." Copies / mL";
			} elseif(substr(trim($result),0,1)==">")
{				//clean the result remove "Copies / mL" and "," from $result
				$result=preg_replace("/Copies \/ mL/s","",$result);
				$result=preg_replace("/,/s","",$result);
				$result=preg_replace("/\>/s","",$result);
				$result=trim($result);
				//factor
				$result*=$factor;

				//return
				return "&gt; ".number_format((float)$result,2)." Copies / mL";
			} else {
				//clean the result remove "Copies / mL" and "," from $result
				$result=preg_replace("/Copies \/ mL/s","",$result);
				$result=preg_replace("/,/s","",$result);
				$result=preg_replace("/\</s","",$result);
				$result=preg_replace("/\>/s","",$result);
				$result=trim($result);
				//factor
				$result*=$factor;

				//return
				return number_format((float)$result)." Copies / mL";
			}
		}
	}

	public static function isResultFailed($machineType,$result,$flag, $interpretation=""){
		$check = 0;

		if($machineType=='abbott'){
			$abbott_flags = array(
				"4442 Internal control cycle number is too high.",
				"4450 Normalized fluorescence too low.",
				"4447 Insufficient level of Assay reference dye.",
				"4457 Internal control failed.",
				"3153 There is insufficient volume in the vessel to perform an aspirate or dispense operation.",
				"3109 A no liquid detected error was encountered by the Liquid Handler.",
				"A no liquid detected error was encountered by the Liquid Handler.",
				"Unable to process result, instrument response is invalid.",
				"3118 A clot limit passed error was encountered by the Liquid Handler.",
				"3119 A no clot exit detected error was encountered by the Liquid Handler.",
				"3130 A less liquid than expected error was encountered by the Liquid Handler.",
				"3131 A more liquid than expected error was encountered by the Liquid Handler.",
				"3152 The specified submerge position for the requested liquid volume exceeds the calibrated Z bottom",
				"4455 Unable to process result, instrument response is invalid.",
				"A no liquid detected error was encountered by the Liquid Handler.",
				"Failed          Internal control cycle number is too high. Valid range is [18.48, 22.48].",
				"Failed          Failed            Internal control cycle number is too high. Valid range is [18.48,",
				"Failed          Failed          Internal control cycle number is too high. Valid range is [18.48, 2",
				"There is insufficient volume in the vessel to perform an aspirate or dispense operation.",
				"Unable to process result, instrument response is invalid.",
			);

			$abbott_result_fails = array_merge($abbott_flags, array( "-1","-1.00","OPEN"));

			if(empty($result) || in_array($result, $abbott_result_fails) || in_array($flag, $abbott_flags)){
				$check = 1;
			}
			/*if($flag != 'OPEN' && $interpretation != 'OPEN'){
				$check = 1;
			}*/
		}elseif($machineType=='roche'){
			if(trim($result) == 'Failed' || trim($result) == 'Invalid'){
				$check = 1;
			}
		}
		return $check;
	}

	public static function abbott_fail_sql(){
		$abbott_result_fails = array(
			"-1.00",
			"3153 There is insufficient volume in the vessel to perform an aspirate or dispense operation.",
			"3109 A no liquid detected error was encountered by the Liquid Handler.",
			"A no liquid detected error was encountered by the Liquid Handler.",
			"Unable to process result, instrument response is invalid.",
			"3118 A clot limit passed error was encountered by the Liquid Handler.",
			"3119 A no clot exit detected error was encountered by the Liquid Handler.",
			"3130 A less liquid than expected error was encountered by the Liquid Handler.",
			"3131 A more liquid than expected error was encountered by the Liquid Handler.",
			"3152 The specified submerge position for the requested liquid volume exceeds the calibrated Z bottom",
			"4455 Unable to process result, instrument response is invalid.",
			"A no liquid detected error was encountered by the Liquid Handler.",
			"Failed          Internal control cycle number is too high. Valid range is [18.48, 22.48].",
			"Failed          Failed            Internal control cycle number is too high. Valid range is [18.48,",
			"Failed          Failed          Internal control cycle number is too high. Valid range is [18.48, 2",
			"OPEN",
			"There is insufficient volume in the vessel to perform an aspirate or dispense operation.",
			"Unable to process result, instrument response is invalid.",
			);
		$abbott_flags = array(
			"4442 Internal control cycle number is too high.",
			"4450 Normalized fluorescence too low.",
			"4447 Insufficient level of Assay reference dye.",
			"4457 Internal control failed.",
		);
		return 1;
		//return " (a.result IN (".implode(", ", $abbott_result_fails).") OR a.flags IN (".implode(",", $abbott_flags)."))";
	}

	public static function methodUsed($type){
		$types = ['A'=> 'Abbott Real Time HIV-1 PCR test', 'R'=> 'Cobas Ampliprep/Taqman HBV Quantitative Test Version 2.0', 'C'=> 'Cobas HIV-1 Test'];
		return isset($types[$type])?$types[$type]:"";
	}


	public static function month_end($year, $month){
	    if($month==2){
	        $ret = $year%4==0?29:28;
	    }else if($month<=7){
	        $ret = $month%2==0?30:31;
	    }else{
	        $ret = $month%2==0?31:30;
	    }
	    return "$year-$month-$ret";
	}

	public static function valid_phone($phone=""){
		$phone_code = env('PHONE_COUNTRY_CODE', 256);
		$tail_len = env('PHONE_TAIL_LENGTH', 9);
		$first_chars = env('PHONE_FIRST_CHARS','2,3,4,7');
		$phone = preg_replace('/\s/', '', $phone);
		$head = substr($phone, 0, -$tail_len);
		$tail = substr($phone, -$tail_len);
		$vhead = $head=="+$phone_code" || $head=='$phone_code' || empty($head);
		$vtail = is_numeric($tail) && strpos($first_chars, substr($tail, 0,1)) && strlen($tail)==$tail_len;
		return $vhead && $vtail?"+$phone_code$tail":"invalid";
	}

	public static function is_eoc(){
		if(\Auth::user()->type == 19){
			return true;
		}else{
			return false;
		}

	}
	public static function is_regional_referral_director(){
		if(\Auth::user()->type == 47){
			return true;
		}else{
			return false;
		}

	}

	public static function is_evd_user(){
		$evd_users = [757,5019,5018,5017,945,941,932,897,925,958,1120,901,968,1072,833,5029,5028,914,5030,924,876,4321,5031,4618];
		if(in_array(\Auth::user()->id, $evd_users) || MyHTMl::is_case_manager()){
			return true;
		}else{
			return false;
		}
	}

	public static function is_facility_admin(){
		if(\Auth::user()->type == 36){
			return true;
		}else{
			return false;
		}

	}
	public static function data_analyst(){
		if(\Auth::user()->type == 25 || \Auth::user()->id == 977 || \Auth::user()->id == 945 || \Auth::user()->id == 4618 ){
			return true;
		}else{
			return false;
		}

	}
	public static function is_sample_archival_user(){
		if(\Auth::user()->type == 29){
			return true;
		}else{
			return false;
		}

	}
	public static function is_lab_manager(){
		if(\Auth::user()->type == 18){
			return true;
		}else{
			return false;
		}

	}
	public static function is_ref_lab(){
		if(\Auth::user()->type == 16 || \Auth::user()->type == 38){
			return true;
		}else{
			return false;
		}
	}
	public static function is_integrated(){
		//$ref_lab_ids = [2901,3013,2920,3012,3015,3018,1313,3021,164,2925];2918,2904
		$ref_lab_ids = [2906,2901,3013,2920,3012,3018,1313,3021,164,2925,2890,2919,2427,2896,2995,2898,3022,3023,2997,3018,3024,3010,3028,3034];
		if(in_array(\Auth::user()->ref_lab, $ref_lab_ids)){
			return true;
		}else{
			return false;
		}
	}
	public static function is_cphl_lab(){
		if(\Auth::user()->type == 35){
			return true;
		}else{
			return false;
		}

	}
	public static function is_incident_commander(){
		if(\Auth::user()->type == 17){
			return true;
		}else{
			return false;
		}

	}
	public static function is_general_user(){
		if(\Auth::user()->type == 42){
			return true;
		}else{
			return false;
		}

	}
	public static function is_port_health_user(){
		if(\Auth::user()->type == 41){
			return true;
		}else{
			return false;
		}

	}
	public static function is_case_manager(){
		if(\Auth::user()->type == 24){
			return true;
		}else{
			return false;
		}

	}
	public static function is_district_user(){
		if(\Auth::user()->type == 23){
			return true;
		}else{
			return false;
		}

	}
	public static function is_classified_user(){
		/*if(\Auth::user()->type == 28){
			return true;
		}else{
			return false;
		}*/
		//Allow ID, ED, DG and Isaac to access these kind of results
		$user_id = [897,936,941,925,913,2094];
		//$type = [1,40];
		if(in_array(\Auth::user()->id, $user_id)){
			return true;
		}else{
			return false;
		}

	}
	public static function is_ec_user(){
		if(\Auth::user()->type == 30){
			return true;
		}else{
			return false;
		}

	}
	public static function is_site_of_collection_user(){
		if(\Auth::user()->type == 27){
			return true;
		}else{
			return false;
		}

	}
	public static function is_rdt_site_user(){
		if(\Auth::user()->type == 34 && MyHTML::restrictedAccess() != true){
			return true;
		}else{
			return false;
		}

	}
	public static function is_facility_dlfp_user(){
		if(\Auth::user()->type == 39){
			return true;
		}else{
			return false;
		}

	}
	public static function is_site_of_collection_editor(){
		if(\Auth::user()->type == 33){
			return true;
		}else{
			return false;
		}

	}
	public static function sero_survey_user(){
		// if(\Auth::user()->id == 777 || \Auth::user()->id == 775 || \Auth::user()->id == 817 || \Auth::user()->id == 818){
		if(\Auth::user()->id == 1329 || \Auth::user()->id == 1328){
			return true;
		}else{
			return false;
		}

	}
	public static function is_moh(){
		if(\Auth::user()->id == 901 || \Auth::user()->id == 844){
			return true;
		}else{
			return false;
		}
	}

	public static function survailance(){

		if(\Auth::user()->id == 901 || \Auth::user()->id == 844 || \Auth::user()->id == 936 || \Auth::user()->id == 945){
			return true;
		}else{
			return false;
		}
	}

		public static function results_manager_editor(){
		if(\Auth::user()->id == 901 || \Auth::user()->id == 844 || \Auth::user()->id == 936 || \Auth::user()->id == 945){
			return true;
		}else{
			return false;
		}
	}

	public static function getUserByID($user_id){
		if($user_id){
			$user =  \DB::select('Select family_name, other_name FROM users WHERE id = '.$user_id);
			return  $user[0]->family_name.' '.$user[0]->other_name;
		}else{
			return 'System';
		}
	}
	public static function getUserDistrict(){
		//Auth::user()->district_id
		$district = \DB::select("SELECT district FROM districts where id = '".\Auth::user()->district_id."'");
		return $district[0]->district;
	}
	public static function getUserSiteOfCollection(){
		$id = \Auth::user()->facilityID;
		if($id == ''){
			$id = \Auth::user()->ref_lab;
		}
		$facility = \DB::select("SELECT facility, key_word FROM facilities where id = ".$id);
		$keyw = $facility[0]->key_word == ''? 'not given' : $facility[0]->key_word;
		return ['facility_name' => $facility[0]->facility, 'key_word' => $keyw];
	}
	public static function DistrictOfCollection(){
		//Auth::user()->district_id
		$district = \DB::select("SELECT district, key_word FROM districts where id = ".\Auth::user()->district_id);
		$keyw = $district[0]->key_word == ''? 'not given' : $district[0]->key_word;
		return ['district_name' => $district[0]->district, 'key_word' => $keyw];
	}
	public static function getRefLabName($ref_lab){
		$lab_name = \DB::select("SELECT facility FROM facilities where id =".$ref_lab." ");
		return $lab_name[0]->facility;
	}

	public static function reconstructDate($date_string,$return_date_only = 0){

		if (str_contains($date_string, '/')) {
		    $date_arr = explode("/", $date_string);
		    //dd( $date_arr);
		    if($return_date_only){
		    	return $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
		    }
		    $year_time = explode(" ", $date_arr[2]);
		    //dd($year_time);

		}else{
			$date_arr = explode("-", $date_string);
			$year_time = explode(" ", $date_arr[2]);
		}
		$reconstructed_date = $year_time[0].'-'.$date_arr[1].'-'.$date_arr[0].' '.$year_time[1];
		//dd($reconstructed_date);
		return $reconstructed_date;
	}

	public static function validateDates($collection_date,$receipt_date, $test_date){
		//dd($collection_date.' , '.$receipt_date.', '.$test_date);
		$collection_secs = MyHTMl::convertDatetoSecs($collection_date);
		$receipt_secs = MyHTMl::convertDatetoSecs($receipt_date);
		$test_secs = MyHTMl::convertDatetoSecs($test_date);
		$now_secs = MyHTMl::convertDatetoSecs(date('Y-m-d H:i:s'));
		/*if($collection_secs > $now_secs || $receipt_secs > $now_secs || $test_secs > $now_secs){
			return 0;
		}else*/
		if($collection_secs && $receipt_secs && $receipt_secs < $collection_secs){
			return 0;
		}elseif($test_secs && $receipt_secs && $test_secs < $receipt_secs){
			return 0;
		}elseif($test_secs && $collection_secs && $test_secs < $collection_secs){
			return 0;
		}elseif(!$test_secs || $test_secs > $now_secs){
			return 0;
		}else{
			return 1;
		}
	}

	public static function validateResult($result_val,$s_district,$patient_id,$who_is_being_tested, $receipt_number){
		//dd($result_val.','.$s_district.','.$patient_id);
		if(strtolower($who_is_being_tested) == ''){
			return false;
		}
		if(strtolower($who_is_being_tested) == 'traveller' && $receipt_number == ''){
			return false;
		}

		if(($result_val == 'positive' || $result_val == 'negative') && $s_district != '' && $patient_id != ''){
			return true;
		}else{
			return false;
		}
	}
	public static function convertDatetoSecs($date){
		if($date != ''){
			return strtotime($date);
		}else{
			return 0;
		}
	}

	public static function isSpecialUser(){
		$special_user_ids = [897,941,952,4782,945,984,2094,844,936,5022,948,925,5030];
		if(in_array(\Auth::user()->id, $special_user_ids)){
			return true;
		}else{
			return false;
		}
	}

	public static function isAuditUser(){
		$user_id = [897,936,984,1323,1037,4102];
		$type = [1,40];
		if(in_array(\Auth::user()->id, $user_id) || in_array(\Auth::user()->type, $type)){
			return true;
		}else{
			return false;
		}
	}
	//restrict users from uploading,viewing or assigning results
	public static function restrictedAccess(){
		$user_id = [3924,3925,3926,3927,3928,3929,3930,3931];
		//$ref_lab = [2901, 2906];

		if(in_array(\Auth::user()->id, $user_id)){
			return true;
		}else{
			return false;
		}

	}
	    //restrict users from uploading CSV results
        public static function noCsvUpload(){
                $ref_lab = [2901,2906];

                if(in_array(\Auth::user()->ref_lab, $ref_lab)){
                        return true;
                }else{
                        return false;
                }

        }

	public static function getRefLabs(){
		$arr=array();
		/*$facilities = \DB::select("SELECT id, facility FROM facilities where facilityLevelID = 20");
		foreach ($facilities as $fac) {
			$arr[$lab->id]=$lab->facility;
		}*/
		foreach(Facility::where('facilityLevelID', '=',20)
			->orWhere('is_ref_lab','=',1)->get() AS $lab){
			$arr[$lab->id]=$lab->facility;
		}
		return $arr;
	}

	public static function getIPAddresses(){
		exec('netstat -ie', $result);
	   if(is_array($result)) {
	    $iface = array();
	    foreach($result as $key => $line) {
	      if($key > 0) {
	        $tmp = str_replace(" ", "", substr($line, 0, 10));
	        if($tmp <> "") {
	          $macpos = strpos($line, "HWaddr");
	          if($macpos !== false) {
	            $iface[] = array('iface' => $tmp, 'mac' => strtolower(substr($line, $macpos+7, 17)));
	          }
	        }
	      }
	    }
	    return $iface;
	  } else {
	    return "notfound";
	  }
	}

	/**
 *  Merge the arrays passed to the function and keep the keys intact.
 *  If two keys overlap then it is the last added key that takes precedence.
 * 
 * @return Array the merged array
 */
public static function array_merge_maintain_keys() {
	$args = func_get_args();
	$result = array();
	foreach ( $args as &$array ) {
		foreach ( $array as $key => &$value ) {
			$result[$key] = $value;
		}
	}
	return $result;
}
}
