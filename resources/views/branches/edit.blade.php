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
				<td class='td_label'><label for='c'>User name:</label></td>
				<td>{!! Form::text('username',$user->username,['class'=>'form-control','id'=>'c','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='user_r'>User role:</label></td>
				<td>{!! Form::select('type',[""=>""]+$user_roles,$user->type,['class'=>'form-control','id'=>'user_r','required'=>1]) !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='branch_id'>User role:</label></td>
				<td>{!! Form::select('brach_id',[""=>""]+$branches_arr,$user->branch_id,['class'=>'form-control','id'=>'branch_id','required'=>1]) !!} </td>
			</tr>
			
			<tr>
				<td class='td_label'><label for='g'>Email:</label></td>
				<td>{!! Form::email('email',$user->email,['class'=>'form-control','id'=>'g','required'=>1]) !!} 

				{!! Form::hidden('ref_lab',env('LAB_ID'))!!}</td>
			</tr>
			<tr>
				<td class='td_label'><label for='h'>Signature:</label></td>
				<td>
					<input type='file' name='signature' value='' onchange="previewImage(this,'sign')"><br>
					<img id='sign' src="{{ asset($user->signature) }}" height="80" width="150" alt="signature_image"> </td>
			</tr>
			<tr>
				<td class='td_label'><label for='i'>Telephone:</label></td>
				<td>{!! Form::text('telephone',$user->telephone,['class'=>'form-control','id'=>'i','required'=>1]) !!} </td>
			</tr>
			<tr class="hidden">
				<td class='td_label' ><label for='k'>Limit by:</label></td>
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
 	$("#user_r").select2({	placeholder:"Select user role", allowClear:true, width: '40%' });
 	$("#branch_id").select2({	placeholder:"Select a branch", allowClear:true, width: '40%' });
 	$("#fclty").select2({	placeholder:"Select facility", allowClear:true, width: '40%' });
 	$("#hb").select2({	placeholder:"Select hub", allowClear:true, width: '40%' });
 	$("#ip").select2({	placeholder:"Select IP", allowClear:true, width: '40%' });
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



