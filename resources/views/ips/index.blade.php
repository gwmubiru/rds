@extends('Admin.app')
@section('admin-content')
<div id="d4" class="panel panel-default">
	<div class="panel-body">
		{!! link_to('ips/create','Create new IP',['class'=>'btn btn-primary btn-side']) !!}
		<br><br>
		
		<?php
		$excel_data=array();
		?>	
		<table class='table table-striped table table-condensed' id='tab_id'>
		  <thead>
			<tr>
				<th>IP</th>
				<th>Full Name</th>
				<th>Address</th>
				<th>Focus Person</th>
				<th>Contact</th>
				<th>Description</th>
				<th>Funding source</th>
				<th width='8%' />
				
			</tr>
		  </thead>
		  <tbody>
			@foreach ($ips AS $ip)		 
			<tr>
			<?php 
			echo "<td>$ip->ip</td>";
			echo "<td>$ip->full_name</td>";
			echo "<td>$ip->address</td>";
			echo "<td>$ip->focus_person</td>";
			echo "<td>$ip->focus_person_contact</td>";
			echo "<td>$ip->description</td>";
			echo "<td>$ip->funding_source</td>";
			echo "<td>".link_to("ips/show/$ip->id","View")."";
			echo " | ".link_to("ips/edit/$ip->id","Edit")."</td>";

			$excel_data[]=array(
				"IP"=>$ip->ip,
				"Full name"=>$ip->full_name,
				"Address"=>$ip->address,
				"Focus person"=>$ip->focus_person,
				"Contact"=>$ip->focus_person_contact,
				"Description"=>$ip->description,
				"Funding source"=>$ip->funding_source);
			?>
			</tr>		 
			@endforeach
			</tbody>
		</table>
	</div>
</div>
<?php session(["excel_data"=>$excel_data,"excel_file_name"=>"ips"]) ?>

<script type="text/javascript">
$(document).ready(function() {
  	$('#tab_id').DataTable();
  });

function validate(that){
	/*if(that.regions_filter.selected && that.region_id.value==""){
		alert("Please select a region  to use that filter");
		return false;
	}else if(that.districts_filter.selected && that.district_id.value==""){
		alert("Please select a district to use that filter");
		return false;
	}else if(that.hubs_filter.selected && that.hub_id.value==""){
		alert("Please select a hub to use that filter");
		return false;
	}else{
		return true;
	}*/
	return true;
}

</script>
@endsection

