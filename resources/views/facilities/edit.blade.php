@extends('Admin.app')
@section('admin-content')
<div id='d5' class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>'facilities/update/'.$id,'id'=>'form_id')) !!}
		{!! MyHTML::hidden("created",$facility->created) !!}
		{!! MyHTML::hidden("old_hub",$facility->hubID) !!}
 
<!-- <table>
	<tr>
		<td>
			Date: <input type="text" id="ii1"></p>
			<script>$(function() { $( "#ii1" ).datepicker(); });</script>
		</td>
	</tr>
</table> -->

		

		<table class='table table-bordered'>
			<tr>
				<td class='td_label' width='20%'><label for='a'>Facility Code:</label></td>
				<td>{!! MyHTML::text('facilityCode',$facility->facilityCode,'form-control','a') !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='b'>Facility Name:</label></td>
				<td>{!! MyHTML::text('facility',$facility->facility,'form-control','b') !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='c'>Facility Level:</label></td>
				<td>{!! MyHTML::select('facilityLevelID',$facility_levels,$facility->facilityLevelID,'c') !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='d'>District:</label></td>
				<td>{!! MyHTML::select('districtID',$districts,$facility->districtID,'d') !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='e'>Hub:</label></td>
				<td>{!! MyHTML::select('hubID',$hubs,$facility->hubID,'e') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='f'>Phone:</label></td>
				<td>{!! MyHTML::text('phone',$facility->phone,'form-control','f') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='g'>Email:</label></td>
				<td>{!! MyHTML::email('email',$facility->email,'form-control','g') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='h'>Physical Address:</label></td>
				<td>{!! MyHTML::text('physicalAddress',$facility->physicalAddress,'form-control','h') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='i'>Return Address:</label></td>
				<td>{!! MyHTML::text('returnAddress',$facility->returnAddress,'form-control','i') !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='j'>Contact Person:</label></td>
				<td>{!! MyHTML::text('contactPerson',$facility->contactPerson,'form-control','j') !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='k'>Supporting IP(s)</label></td>
				<td>
					<div id='ips_input'>
						@foreach($facility_ips AS $f_ip)
						<p>
							{!! MyHTML::hidden("f_ip_ids[]",$f_ip->id) !!}
							{!! MyHTML::select("facility_ips[]",$ips,$f_ip->ipID) !!}
							Started on:

							<?php 
							$stat_date=explode("-", $f_ip->start_date);
							$stop_date=explode("-", $f_ip->stop_date)
							 ?>

							{!! MyHTML::monthYear('start_date',1,$stat_date[0],$stat_date[1]) !!}

							<?php 
							if($f_ip->stopped==1){
								echo "<label class='checkbox-inline'><input type='checkbox' name='stopped[]' checked='true' value='1' onclick="."stopped(this,'st_date".$f_ip->id."')"." > Stopped </label>";
								echo " On? <span id='st_date".$f_ip->id."'>".MyHTML::monthYear('stop_date',1,$stop_date[0],$stop_date[1])."</span>";
							}else{
								echo MyHTML::checkbox("stopped[]",1,'Stopped','st'.$f_ip->id,"stopped(this,'st_date".$f_ip->id."')");
								echo "<span id='st_date".$f_ip->id."'></span>";
							}
							?>
						</p>
						@endforeach
					</div>
					<?php echo "<label id='k' class='add_item' onClick='addIP()'>Add IP</label>" ?>					
				</td>
			</tr>
			<tr><td class='borderless'/><td>{!! MyHTML::submit('Save Facility Details') !!} </td></tr>
		</table>

		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
function addIP () {
	var stat_date='Start Date: <?php echo MyHTML::monthYear('start_date',1,date('Y')) ?> ';
	$("#ips_input").append('<p><?php echo MyHTML::select('ips[]',$ips) ?> '+stat_date+removeItemHTML()+'</p>');	
}

function stopped(that,id){
	var stop_date=' On? <?php echo MyHTML::monthYear('stop_date',1,date('Y')) ?> ';
	if(that.checked){
		getById(id).innerHTML=stop_date;
	}else{
		getById(id).innerHTML="";
	}
}
</script>
@endsection

