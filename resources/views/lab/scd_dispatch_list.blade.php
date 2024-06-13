@extends('layouts/layout')

@section('content')

<?php 


	$selected_batches = DispatchManager::getBatchesForDispatch_SC_only();
// dd($selected_batches);
	$nBatches = count($selected_batches);

// dd($selected_batches);

	if($nBatches == 0) dd('No batches to dispatch - Please go back');

?>

<style type="text/css">
	.ws_row:hover{
		background-color: #fcf8e3;
	}
	th{
		text-align: center;
	}

</style>

<section id='s4' class='mm'></section>

	<a href="/rejects" style="float: right; display: inline-block;">&nbsp; Rejected Results </a> 
	<a href="/dispatch" style="float: right; display: inline-block;"> &nbsp;EID Results | </a>
	<span style="float:right; display: block;">&nbsp; Sickle Cell Results | </span>
	<span style="float:right; display: block;">Go To &raquo;</span>

	<span style="float:left; display: block;"><a target="_blank" href="/eron?scd=y">Sickle Cell Backlog (Results)</a>  | </span>
	<span style="float:left; display: block;">&nbsp;<a target="_blank" href="/eron_envelopes">Envelopes</a></span>

	<h3 align="center" style="color:red">Sickle Cell - Print &amp; Dispatch Results</h3>

	<table id='tab_id' class="table table-bordered" cellspacing="0" cellpadding="4" align="center" style="margin-top: 1em; border: 1px solid #ddd" >
		<thead>
			<tr>
				<th><small>Envelope Number</small></th>
				<th><small>Batch</small></th>
				<th><small>Facility</small>	</th>
				<th><small>Hub</small></th>
				<th><small>Results Date</small>	</th>
				<th><small>Actions</small></th>
			</tr>
		</thead>
		<tbody>		

		@foreach($selected_batches as $w)

			<tr class="ws_row">

				<td> {{ substr($w->envelope_number, 0, 8) . " - " . substr($w->envelope_number, 8) }} </td>				
				<td> {{ $w->batch_number }} </td>
				<td> {{ $w->facility }} </td>			
				<td>&nbsp;&nbsp;{{ $w->hubname }} </td>
				<td> {{ $w->date_PCR_testing_completed }} </td>

				<td align="center">
					<div class="btn-group">

						<button type="button" 
								class="btn btn-primary btn-xs dropdown-toggle"
								style="font-size: 1.1em; float:right" 
								data-toggle="dropdown" aria-expanded="false">

							Action <span class="caret"></span>						
						</button>

						<ul class="dropdown-menu xworksheet_actions" 	role="menu" style="width:17em;">

							<!-- <li><a 	worksheet="{{ $w->id }}"	href="sc_result_slips?b={{$w->id}}"><span class="glyphicon glyphicon-envelope">&nbsp;</span>View Results</a></li> -->
							<li><a 	worksheet="{{ $w->id }}"	href="eid_results?b={{$w->id}}&scd=y&pcr=y"><span class="glyphicon glyphicon-envelope">&nbsp;</span>View BOTH Results</a></li>
							<li><a 	worksheet="{{ $w->id }}"	href="eid_results?b={{$w->id}}&scd=y"><span class="glyphicon glyphicon-envelope">&nbsp;</span>View Sickle Cell Only</a></li>

							<li class="divider"></li>
							<li><a 	worksheet="{{ $w->id }}" target="_blank"	href="eid_results?b={{$w->id}}&scd=y&pcr=y"><span class="glyphicon glyphicon-print">&nbsp;</span><span class="glyphicon glyphicon-envelope">&nbsp;</span>Print BOTH results</a></li>
							<li><a 	worksheet="{{ $w->id }}" target="_blank"	href="eid_results?b={{$w->id}}&scd=y"><span class="glyphicon glyphicon-print">&nbsp;</span><span class="glyphicon glyphicon-envelope">&nbsp;</span>Print Sickle Cell Only</a></li>

							<li class="divider"></li>

							<li><a 	worksheet="{{ $w->id }}" target="_blank" 	href="/hc_env/{{$w->id}}?&pp=1"><span class="glyphicon glyphicon-print">&nbsp;</span><span class="glyphicon glyphicon-list-alt">&nbsp;</span>Print Envelope</a></li>

						</ul>
					<div class="btn-group">
				</td>
			</tr>


		@endforeach
	</tbody>

	</table>
<script type="text/javascript">

	$(document).ready(function() { $('#tab_id').DataTable({"bSort" : false, "deferRender": true}); });
	
</script>
@stop

