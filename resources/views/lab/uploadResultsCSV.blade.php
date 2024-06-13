@extends('layouts/layout')

@section('content')

<?php 

	$selected_worksheets = WorkSheetManager::getWorkSheets();
	$nWorksheets = count($selected_worksheets);

?>
<?php 
	$worksheet = $ws; /* $ws comes from LabController */

	$ws_id = $worksheet->id;

?>
<section id='s3' class='mm'></section>
<h2 align="center">Upload Results</h2>
{!! Form::model($ws, array('action' => 'LabController@store_worksheet', 'files' => true, 'method'=>'post')) !!}
	<table border='1' class='data-table' align="center" cellpadding="5" cellspacing="0">

		{!! Form::hidden('i', "saveFile") !!}

		{!! Form::hidden('ws', $ws_id ?: Input::get('ws', '') ) !!}

		
		@if(\Request::has('dummy')) 
			{!! Form::hidden('dummy', Input::get('dummy') ) !!} 
		@endif

		<tr class='even'>
			<td colspan='1'>Worksheet No		</td>
			<td colspan='2'>{{ $worksheet->id }}</td>
		</tr>
			
		<tr class='even'>
			<td colspan='1'>Date Created</td>
			<td colspan='2'>{{ $worksheet->DateCreated }}</td>
		</tr>
		<tr class='even'>
			<td colspan='1'>Created by </td>
			<td colspan='2'>{{ $worksheet->CreatedBy }}</td>
		</tr>
		<tr class='even'>
			<td colspan='1'>Select Results file:</td>
	     	<td colspan='2'>{!! Form::file("csv", $attributes = array()) !!}</td>
		</tr>	
		<tr class='odd'>
			<td colspan='3' align="right" style="padding:1em;">
				{!! Form::submit('Upload the Test Results') !!} 
			</td>
		</tr>
	</table>
{!! Form::close() !!}

@stop