@extends('layouts/layout')


@section('content')


<?php 

	$p = "";
	$selected_worksheets = WorkSheetManager::getWorkSheets();
	$nWorksheets = count($selected_worksheets);


	function ftat($t){// format turn around time
		
		if($t < 0)	return "??? days";
		if($t == 0)	return "-";
		if($t == 1)	return "1 day";
		if($t > 1)	return "<b style='color:red'>$t days</b>";
	}


	function wStatus($worksheet_has_results){

		if($worksheet_has_results === "YES")
			return "completed";
		else
			return "<b style='color:blue'>in process</b>";
	}

	function linkToHTML($this_worksheet){
		return "<a href='#'>View Details</a>";
	}
	function linkToPDF($this_worksheet){
		return "<a href='#'>Print Worksheet</a>";
	}

	function dws()// returns the current dummy ws, if any
	{
		$sql = "SELECT DISTINCT worksheet_number  FROM pcr_dummy_results, dbs_samples " .
				" WHERE pcr_dummy_results.sample_id = dbs_samples.id AND accepted_result IS NULL ";
		
		$dummy_worksheets = \DB::select($sql);
		$nDummyWorksheets = count($dummy_worksheets);// should be 1 or none (i.e. 0)

		return ($nDummyWorksheets == 0) ? "" : $dummy_worksheets[0]->worksheet_number;
	}

$initial_state = \Session::get('ws_type', 'PCR') == 'SCD' ? "" : "checked";
$i=1;

?>

<style type="text/css">
	#highlight{
		background-color: #eeaa00;
	}
	.ws_row:hover{
		background-color: #fcf8e3;
	}
	th{
		text-align: center;
	}
</style>

<link href="/css/bootstrap-switch.css" rel="stylesheet">
<script src="/js/bootstrap-switch.min.js"></script>
@include("quick_access_menu")

<input type="checkbox" name="my-checkbox" {{ $initial_state }}>
<span style="color:#777;  font-weight: 500; margin-left:0.5em;" id="ws_label">PCR Worksheets</span>
<section id='s3' class='mm'></section>

<div id='pcr_div'>
@include('lab.partials._worksheet_menu')

	<table id='tab_id'  class="table table-bordered table-striped table-condensed" cellspacing="0" cellpadding="4" align="center" style="margin-top: 1em; border: 1px solid #ddd" >
		<thead>
			<tr>
				<th>No.</th>
				<th>Worksheet No.</th>
				<th>HIQ CAP No</th>
				<th>Spek Kit No</th>
				<th>Created By</th>
				<th>Date Created</th>
				<th>Review</th>
				<th>Date Reviewed</th>
				<th>Turn Around Time</th>
				<th>Completed?</th>
				<th style="text-align: center">Task</th>
			</tr>
		</thead>
		<tbody>
			<?php $row_to_highlight = \Request::has('h') ? \Request::get('h') : "_NONE_";   ?>

		@foreach($selected_worksheets as $w)
			<?php 	
				$highlight_this_row = ($row_to_highlight == $w->id) ? true : false;
				$row_id = "";

				if($highlight_this_row){
					$row_id = " id=highlight ";
				}
			?>

			<tr class="ws_row" {{ $row_id }}>
				<td> {{ $i++ }}</td>
				<td> {{ $w->id }} </td>
				<td> {{ $w->Kit_LotNumber }} </td>
				<td> {{ $w->Kit_Number }} </td>
				<td> {{ $w->CreatedBy }} </td>
				<td> {{ $w->DateCreated }} </td>
				<td> 
					@if($w->HasResults=='YES' && $w->PassedReview=='NOT_YET_REVIEWED') <a href="/eid_review/{{ $w->id }}" class="btn btn-info">Review</a> 
					@elseif($w->PassedReview=='YES') Reviewed 
					@endif
				</td>
				<td> {{ $w->DateReviewed }} </td>
				<td> {{ $w->lab_turnaround_time ?: "--" }} </td>
				<td> {{ $w->is_completed }} </td>
				<td align="center">
					<div class="btn-group">

						<button type="button" 
								class="btn btn-primary btn-xs dropdown-toggle"
								style="font-size: 1.1em; float:right" 
								data-toggle="dropdown" aria-expanded="false">Action<span class="caret"></span>						
						</button>

						<ul class="dropdown-menu pull-right worksheet_actions" 	role="menu">

							<li><a 	action="view"	worksheet="{{ $w->id }}"	href="#"><span class="glyphicon glyphicon-th pull-left">&nbsp;</span>View</a></li>
							<li><a 	action="toPDF" target="_blank" 	worksheet="{{ $w->id }}"	href="#"><span class="glyphicon glyphicon-save-file pull-left">&nbsp;</span>Print PDF</a></li>							
							
							<?php 	$dws_id = dws();
									if( $dws_id ){
										$p = "dwx=$dws_id";// this will be interpreted as "dummy worksheet exists" 
									}else{
										$p = "";
										$dws_id = $w->id; 
									}
							?>
															
							<li class="divider"></li>
							<li><a 	action="edit" 	worksheet="{{ $w->id }}"	href="#"><span class="glyphicon glyphicon-pencil pull-left">&nbsp;</span>Edit</a></li>
							<li><a 	action="uploadFile" worksheet="{{ $w->id }}"	href="#"><span class="glyphicon glyphicon-open-file pull-left">&nbsp;</span>Upload Results</a></li>
								
								<li class="divider"></li>
							<li><a 	action="delete"	worksheet="{{ $w->id }}"	href="#"><span class="glyphicon glyphicon-remove pull-left" style="color:red;" >&nbsp;</span>Delete</a></li>
						</ul>
					<div class="btn-group">
				</td>

			</tr>
		@endforeach
		</tbody>

	</table>

	</div>
	<div id='scd_div' style="display:none">
		@include('_scd_list')
	</div>
	<script type="text/javascript">
		$(function (){

			var worksheet_from_menu = document.getElementById('cx2');

			$('.worksheet_actions li > a').click(function () {

				var __this = $(this);
				var action = __this.attr("action");
				var target = __this.attr("worksheet");


				if(	target === undefined ){// via menu
					
					target = parseInt(worksheet_from_menu.value);
					
					if(isNaN(target)){
						alert("Please select a worksheet");
						return false;
					}
				} 

				doWorksheetAction(action, target);
			});

			function doWorksheetAction(action, target){

				switch(action){
					case "create" : location = "/w";  return;
					case "edit"	: 	location = "/ws?i=edit&ws="+target;  return;
					case "view"	: 	location = "/ws?i=view&ws="+target;  return;
					case "dummy": 	location = "/dummy_ws/"+target+"?{{$p}}";  return;
					case "toPDF": 	location = "/ws?i=toPDF&ws="+target; return;
					case "uploadFile" :	location = "/ws?i=uploadFile&ws="+target; return;
					case "saveFile":location = "/ws?i=saveFile&ws="+target; return;	
					case "delete" : location = "/ws?i=del&ws="+target; return;

				}
			}

			$("#make_new_worksheet").click(function (evt) {
				doWorksheetAction("create");
			});

			
			$("[name='my-checkbox']").bootstrapSwitch();

			@if(\Session::get('ws_type', 'PCR') == 'SCD')
				showSCD();
			@else
				showPCR();			
			@endif



			$('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {
				var PCR = true;

				if(state === PCR){
					showPCR();
				}else{
					showSCD()
				}

			});

			function setState(new_state) {
				$.get("/ss/"+new_state, function (d){
					console.log(d);
				},
				"text");
			}

			function showPCR(){

				setState('PCR');

				$("#ws_label").text('PCR Worksheets');
				$("#pcr_div").show();
			    $("#scd_div").hide();
			}

			function showSCD (){

				setState('SCD');

				$("#ws_label").text('Sickle Cell Worksheets');
			    $("#pcr_div").hide();
			    $("#scd_div").show();
			}

		});

	$(document).ready(function() {
  		$('#tab_id').DataTable();
  	});
	</script>

@stop