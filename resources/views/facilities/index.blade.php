@extends('Admin.app')
@section('admin-content')
<div id='d5' class="panel panel-default">

	<div class="panel-body">
		{!! link_to('facilities/create','Create new facility',['class'=>'btn btn-primary btn-side']) !!}
		<br><br>
		<div class="well">
			{!! Form::open(array('url'=>'facilities/index','onsubmit'=>'return validate(this)')) !!}

			Region: {!! MyHTML::select("region_id",array(""=>"Select")+$regions,"") !!} {!! MyHTML::submit("Filter","btn btn-primary btn-xs","regions_filter") !!}
			&nbsp;&nbsp;|&nbsp;&nbsp;
			Districts: {!! MyHTML::select("district_id",array(""=>"Select")+$districts,"") !!} {!! MyHTML::submit("Filter","btn btn-primary btn-xs","districts_filter") !!}
			&nbsp;&nbsp;|&nbsp;&nbsp;
			Hubs: {!! MyHTML::select("hub_id",array(""=>"Select")+$hubs,"") !!} {!! MyHTML::submit("Filter","btn btn-primary btn-xs","hubs_filter") !!}
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="/excel">{!! MyHTML::tinyImg('excel.png',20,30) !!}Export to Excel</a>

			

			{!! Form::close() !!}
			
		</div>	
		<?php
		if(!empty($region_id)){
			echo "<b>Region:</b> ".$regions[$region_id];
		}else if(!empty($district_id)){
			echo "<b>District:</b> ".$districts[$district_id];
		}else if(!empty($hub_id)){
			echo "<b>Hub:</b>".$hubs[$hub_id];
		}

		$excel_data=array();
		?>	
		<table class='table table-striped table table-condensed' id='tab_id'>
		  <thead>
			<tr>
				<th>Facility Code</th>
				<th>Facility</th>
				<th>Level</th>
				<th>Hub</th>
				<th>District</th>
				<th>Region</th>
				<th width='8%' />
				
			</tr>
		  </thead>
		  <tbody>
			@foreach ($facilities AS $facility)		 
			<tr>
			<?php 
			echo "<td>$facility->facilityCode</td>";
			echo "<td>$facility->facility</td>";
			echo "<td>$facility->facility_level</td>";
			echo "<td>$facility->hub</td>";
			echo "<td>$facility->district</td>";
			echo "<td>$facility->region</td>";
			echo "<td>".link_to("facilities/show/$facility->id","View")."";
			echo " | ".link_to("facilities/edit/$facility->id","Edit")."</td>";

			$excel_data[]=array(
				"Facility Code"=>$facility->facilityCode,
				"Facility"=>$facility->facility,
				"Level"=>$facility->facility_level,
				"Hub"=>$facility->hub,
				"District"=>$facility->district,
				"Region"=>$facility->region);
			?>
			</tr>		 
			@endforeach
			</tbody>
		</table>
	</div>
</div>
<?php session(["excel_data"=>$excel_data,"excel_file_name"=>"facilities"]) ?>

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

