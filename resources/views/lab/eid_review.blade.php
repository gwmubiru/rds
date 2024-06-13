@extends('layouts/layout')

@section('content')

<?php 

	/*$sql = "SELECT 	batch_id, 
					batch_number, 
					envelope_number, 
					COUNT(dbs_samples.id) AS nSamples, 
					SUM(CASE WHEN accepted_result IS NULL THEN 1 ELSE 0 END) AS testing_incomplete,
			   		SUM(CASE WHEN accepted_result IS NOT NULL THEN 1 ELSE 0 END) AS testing_completed,
					PCR_results_released, 
					facility_name  
			FROM dbs_samples 
			LEFT JOIN batches ON batches.id = batch_id  
			WHERE dbs_samples.id in (SELECT sample_id FROM worksheet_index 
										WHERE worksheet_number = '$ws_id') 
			GROUP BY batch_id";*/

	$sql = "SELECT 	batch_id, 
					batch_number, 
					envelope_number, 
					COUNT(dbs_samples.id) AS nSamples, 
					SUM(CASE WHEN accepted_result IS NULL THEN 1 ELSE 0 END) AS testing_incomplete,
			   		SUM(CASE WHEN accepted_result IS NOT NULL THEN 1 ELSE 0 END) AS testing_completed,
					PCR_results_released, 
					facility_name  
			FROM dbs_samples 
			LEFT JOIN batches ON batches.id = batch_id 
			WHERE batch_id IN (
					SELECT batch_id FROM worksheet_index AS w
					LEFT JOIN dbs_samples AS s ON w.sample_id=s.id
					WHERE worksheet_number = $ws_id
					)
			GROUP BY batch_id";

	$selected_batches = \DB::select($sql);
	$nBatches = count($selected_batches);

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

<section id='s3' class='mm'></section>


	<a href="/rejects" style="float: right; display: inline-block;">&nbsp; Rejected Results </a> 
	<span style="float:right; display: block;">&nbsp;EID Results | </span>
	<a href="/dispatch_scd" style="float: right; display: inline-block;"> &nbsp; Sickle Cell Results | </a>
	<span style="float:right; display: block;">Go To &raquo;</span>
	<h2 align="center">Reviewing batches for worksheet [ <b>{{ $ws_id }}</b> ]</h2>

	<form>
		<input type="hidden" value="{{ $ws_id }}" id="wksht_id">
	<table 	id='tab_id' class="table table-bordered table-striped table-condensed" 
				cellspacing="0" cellpadding="4" align="center" 
					style="margin-top: 1em; border: 1px solid #ddd" >
		<thead>
			<tr>
				<th>Envelope Number</th>
				<th>Batch</th>
				<th>Facility</th>
				<th>( #No. of Samples, <span class='status_danger'>#Incomplete</span>, <span class='status_ok'>#Completed</span> )</th>
				<th>All Samples Tested?</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($selected_batches as $w)
			<tr class="ws_row">
				<td> {{ substr($w->envelope_number, 0, 8) . " - " . substr($w->envelope_number, 8) }} </td>				
				<td> {{ $w->batch_number }} </td>
				<td> {{ $w->facility_name }} </td>			
				<td align="center"> {!! "( ".$w->nSamples." , <span class='status_danger'> ".$w->testing_incomplete."</span>, <span class='status_ok'>".$w->testing_completed."</span> )" !!}</td>
				<td>{!! $w->nSamples==$w->testing_completed?"<span class='status_ok'>Yes</span>":"<span class='status_danger'>No</span>"; !!}</td>
				<td id='status{{ $w->batch_id }}'><?php echo $w->PCR_results_released == 'YES'?"<span class='status_ok'>Released</span>":"<span class='status_danger'>Not released</span>";  ?></td>
				<td align="center"> 
					<input type="hidden" class="batches" id="release_{{ $w->batch_id }}" 
							key="{{ $w->batch_id }}" value="{{$w->PCR_results_released}}">
							
					@if($w->PCR_results_released == 'YES')
						<a href="/eid_review/{{ $w->batch_id }}" 
							batch="{{ $w->batch_id }}" class="btn btn-danger trigger_button click_to_retain"
							alt="Click to retain" title="Click to retain">Retain</a>
					@else
						<a href="/eid_review/{{ $w->batch_id }}" 
							batch="{{ $w->batch_id }}" class="btn btn-default trigger_button click_to_release"
							alt="Click to release" title="Click to release">Release</a>
					@endif
				</td>
			</tr>
		@endforeach
	</tbody>
	</table>
	
	<a href="#" class="btn btn-primary" id="release_all" style="float:right; margin-top: 1em">RELEASE THE BATCHES</a>

	<p style="margin-bottom: 10em;"> &nbsp; </p>
	<script type="text/javascript" src="/js/eid_review.js"></script>
@stop