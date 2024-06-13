
@extends('layouts/layout')

@section('content')

<section id='s3' class='mm'></section>
<h2 align="center">Upload Results</h2>
{!! Form::model('', array('action' => 'ManageResultsController@store_out', 'files' => true, 'method'=>'post')) !!}
	<table border='1' class='data-table' align="center" cellpadding="5" cellspacing="0">

		{!! Form::hidden('i', "saveFile") !!}

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