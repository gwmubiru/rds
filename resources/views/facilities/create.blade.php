@extends('Admin.app')
@section('admin-content')
<script type="text/javascript">
$(function(){
	$("#b").keyup(function(){
		var q = $(this).val();
		$.get("/facilities/live_search/"+q, function(data){
			if(q){
				$(".live_drpdwn").html(data);
			} else {
				$(".live_drpdwn").html("");
			}
		});
	});

	result= document.querySelector('.live_drpdwn');
	result.style.display = "block";
});
</script>
<div id='d5' class="panel panel-default">
	<div class="panel-heading"><b>Facilities</b></div>
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>'facilities/store','id'=>'form_id')) !!}

		<table class='table table-bordered'>
			<tr>
				<td class='td_label' width='20%'><label for='a'>Facility Code:</label></td>
				<td>
					{!! Form::text('facilityCode','',['class'=>'form-control','id'=>'a']) !!} 
					
				</td>
			</tr>
			<tr>
				<td class='td_label'><label for='b'>Facility Name:</label></td>
				<td  onmouseover="showClass('.live_drpdwn')" onmouseout="hideClass('.live_drpdwn')">
					{!! Form::text('facility','',['class'=>'form-control','id'=>'b','autocomplete'=>'off','required'=>'1']) !!}
					<div class='live_drpdwn' style='display:none'></div>
				</td>
			</tr>
			<tr>
				<td class='td_label'><label for='c'>Facility Level:</label></td>
				<td>{!! MyHTML::select('facilityLevelID',$facility_levels,"",'c') !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='d'>District:</label></td>
				<td>{!! MyHTML::select('districtID',$districts,"",'d') !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='e'>Hub:</label></td>
				<td>{!! MyHTML::select('hubID',$hubs,"",'e') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='f'>Phone:</label></td>
				<td>{!! MyHTML::text('phone','','form-control','f') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='g'>Email:</label></td>
				<td>{!! MyHTML::email('email','','form-control','g') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='h'>Physical Address:</label></td>
				<td>{!! MyHTML::text('physicalAddress','','form-control','h') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='i'>Return Address:</label></td>
				<td>{!! MyHTML::text('returnAddress','','form-control','i') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='j'>Contact Person:</label></td>
				<td>{!! MyHTML::text('contactPerson','','form-control','j') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='k'>Supporting IP(s):</label></td>
				<td>
					<div id='ips_input'></div>
					
					<?php echo "<label id='k' class='add_item' onClick='addIP()'>Add IP</label>" ?>
				</td>
			</tr>

			<tr><td/><td>{!! MyHTML::submit('Save') !!} </td></tr>
		</table>

		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
function addIP () {
	var stat_date='Start Date: <?php echo MyHTML::monthYear('start_date',1,date('Y')) ?> ';
	$("#ips_input").append('<p><?php echo MyHTML::select('ips[]',$ips) ?> '+stat_date+removeItemHTML()+'</p>');
	
}

</script>
@endsection

