@extends('Admin.app')
@section('admin-content')
<link   href="{{ asset('/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('/js/select2.min.js') }}" type="text/javascript"></script>
<div id="d2" class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(['enctype'=>'multipart/form-data','url'=>'users/update/'.$id,'id'=>'form_id','onsubmit'=>'return chkForm(this)']) !!}

		<table class='table table-bordered'>
			<tr>
				<td class='td_label' width='20%'><label for='a'>First Name:</label></td>
				<td>{!! Form::text('other_name',$user->other_name,['class'=>'form-control','id'=>'a','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='b'>Family Name:</label></td>
				<td>{!! Form::text('family_name',$user->family_name,['class'=>'form-control','id'=>'b','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='i'>Telephone:</label></td>
				<td>{!! Form::text('telephone',$user->telephone,['class'=>'form-control','id'=>'i','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='g'>Email:</label></td>
				<td>{!! Form::email('email',$user->email,['class'=>'form-control','id'=>'g','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='designation'>Cadre:</label></td>
				<td>{!! Form::text('designation',$user->designation,['class'=>'form-control','id'=>'designation','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='c'>User name:</label></td>
				<td>{!! Form::text('username',$user->username,['class'=>'form-control','id'=>'c','required'=>1]) !!} </td>
			</tr>
			
			<tr>
				<td class='td_label'><label for='user_r'>User role:</label></td>
				<td>{!! Form::select('type',[""=>""]+$user_roles,$user->type,['class'=>'form-control','id'=>'user_r','required'=>1]) !!} </td>
			</tr>
			@if(\MyHTML::is_eoc())
			{!!Form::hidden('type',23) !!}
			<tr id="district_row" class="">
			@else
			<tr id="district_row" class="hidden">
			@endif
				<td class='td_label'><label for='district'>District:</label></td>
				<td>{!! Form::select('district_id',[""=>""]+$distr_arr,$user->district_id,['id'=>'district_id']) !!} </td>
			</tr>
			<tr id="ref_lab_row" class="hidden">
				<td class='td_label'><label for='ref_lab'>Ref Lab:</label></td>
				<td>{!! Form::select('ref_lab',[""=>""]+$ref_lab_arr,$user->ref_lab,['id'=>'ref_lab']) !!} </td>
			</tr>
			<tr id="site_of_collection_row" class="hidden">
				<td class='td_label'><label for='site_of_collection'>Facility Name:</label></td>
				<td>{!! Form::select('facilityID',[""=>""]+$facilities,$user->facilityID,['id'=>'site_of_collection']) !!} </td>
			</tr>
			
			<tr class="hidden">
				<td class='td_label'><label for='h'>Signature:</label></td>
				<td>
					<input type='file' name='signature' value='' onchange="previewImage(this,'sign')"><br>
					<img id='sign' src="{{ asset($user->signature) }}" height="80" width="150" alt="signature_image"> </td>
			</tr>
			
			<tr class="hidden">
				<td class='td_label'><label for='k'>Limit by:</label></td>
				<td >
					<?php
					$chkt1=empty($user->facilityID)?"unchecked":"checked";
					$chkt2=empty($user->hubID)?"unchecked":"checked";
					$chkt3=empty($user->ipID)?"unchecked":"checked";

					$dsply1=empty($user->facilityID)?"none":"block";
					$dsply2=empty($user->hubID)?"none":"block";
					$dsply3=empty($user->ipID)?"none":"block";
					?>
					{!! Form::radio('limit_by','1','',['onchange'=>'showLimit(1)',"$chkt1"=>"1"]) !!} Facility
					{!! Form::radio('limit_by','2','',['onchange'=>'showLimit(2)',"$chkt2"=>"1"]) !!} Hub
					{!! Form::radio('limit_by','3','',['onchange'=>'showLimit(3)',"$chkt3"=>"1"]) !!} IP
					{!! Form::radio('limit_by','4','',['onchange'=>'showLimit(4)']) !!} None
					<br>
					<div class='limitby' style="display:{!! $dsply1 !!}" id='limit1'>
		                {!! Form::select('facility',[""=>""]+$facilities_arr,$user->facilityID,['id'=>'fclty']) !!}
		                 <br>
		                <div class="other_facilities"></div>
		                <br><a href="#" id="add_facility">Add Facility</a>
		            </div>
					
					<div class='limitby' style="display:{!! $dsply2 !!}" id='limit2'>{!! Form::select('hub',[""=>""]+$hubs_arr,$user->hubID,['id'=>'hb']) !!}</div>
					<div class='limitby' style="display:{!! $dsply3 !!}" id='limit3'>{!! Form::select('ip',[""=>""]+$ips_arr,$user->ipID,['id'=>'ip']) !!}</div>
				</td>
			</tr>
			<tr><td/><td>{!! MyHTML::submit('Update User') !!} </td></tr>
		</table>

		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
var facilities_json = {!! json_encode([""=>""]+$facilities) !!};
function showLimit(val){
 	$(".limitby").attr('style','display:none');
 	document.getElementById('limit'+val).style.display="block";
 }

 $(document).ready(function() {
 	//is a ref lab user is being added, show the ref labs
	$("#user_r").on("change",function(){
	 	var user_type = $(this).val();
	    if(user_type == 16 || user_type == 38 || user_type == 43 ){
	    	$("#ref_lab_row").removeClass('hidden');
	    	$("#site_of_collection_row").val('');
	    	$("#site_of_collection_row").addClass('hidden');
	    }else if(user_type == 27 || user_type == 34 || user_type == 33 || user_type == 46 || user_type == 47){
	    	$("#site_of_collection_row").removeClass('hidden');
	    	$("#ref_lab_row").val('');
	    	$("#ref_lab_row").addClass('hidden');
	    }else if(user_type == 23){
	    	$("#district_row").removeClass('hidden');
	    	$("#ref_lab_row").val('');
	    	$("#ref_lab_row").addClass('hidden');
	    }else if(user_type == 39){
	    	$("#district_row").removeClass('hidden');
	    	$("#site_of_collection_row").removeClass('hidden');
	    	$("#ref_lab_row").val('');
	    	$("#ref_lab_row").addClass('hidden');
	    }else{
	    	$("#site_of_collection_row").val('');
	    	$("#site_of_collection_row").addClass('hidden');
	    	$("#ref_lab_row").val('');
	    	$("#ref_lab_row").addClass('hidden');
	    	$("#district_id").val('');
	    	$("#district_row").addClass('hidden');
	    }
	 }).change();

 	$("#user_r").select2({	placeholder:"Select user role", allowClear:true, width: '40%' });
 	$("#fclty").select2({	placeholder:"Select facility", allowClear:true, width: '40%' });
 	$("#hb").select2({	placeholder:"Select hub", allowClear:true, width: '40%' });
 	$("#ip").select2({	placeholder:"Select IP", allowClear:true, width: '40%' });
 	$("#ref_lab").select2({	placeholder:"Select Ref lab", allowClear:true, width: '40%' });
 	$("#district_id").select2({	placeholder:"Select a district", allowClear:true, width: '40%' });
 	$("#site_of_collection").select2({	placeholder:"Select a facility", allowClear:true, width: '40%' });
 });

//is a ref lab user is being added, show the ref labs

 $("#fclty").on("change",function(){
    $("#facility_name").val($("#fclty option:selected").text());
    $("#hub_name").val("");
    $("#hb option:selected").remove();
    delete facilities_json[this.value];
    $("#add_facility").show();
})

$("#add_facility").on("click", function(){
    //select(name,items,"");
    var more = "class='other_facilities_select' onchange='setOtherFacilities(this)'";
    $(".other_facilities").append("<br>"+select("other_facilities[]",facilities_json, "", more)+"<br>");
    $(".other_facilities_select").select2({   placeholder:"Select facility", allowClear:true, width: '40%' });
 });

 function setOtherFacilities(that){
    delete facilities_json[that.value];
 }
 
$("#hb").on("change",function(){
  $("#hub_name").val($("#hb option:selected").text());
  $("#facility_name").val("");
  $("#fclty option:selected").remove();
})
</script>

<style type="text/css">
.limitby{
	display: none;
}
</style>
@endsection



