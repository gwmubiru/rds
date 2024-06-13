
@extends('layouts/layout')

@section('content')

<section id='s3' class='mm'></section>
<h2 align="center">Upload Results</h2>
<div class="row">
	<div class="col-md-12" style="text-align: center; margin-bottom: 19px;">
		@if(MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user())
		<a href="{{asset('downloads/RDT_Results_upload_template_31Dec21.csv')}}" class="btn btn-primary">Download the Results Upload Template</a>
		@else
			@if(\Auth::user()->ref_lab==2896)
		<a href="{{asset('downloads/CSVTemplateforUploadingResultsMild_may_31Dec21.csv')}}" class="btn btn-primary">Download the Results Upload Template</a>
			@else
			<a href="{{asset('downloads/CSVTemplateforUploadingResults31Dec21.csv')}}" class="btn btn-primary">Download the Results Upload Template</a>
			@endif
		@endif
</div>
</div>
@if(MyHTML::is_ref_lab() || MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user())
@if(Session::has('danger'))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
        <strong>{!! Session::get('danger') !!}</strong>
</div>
@else
@if(!MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user())
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<strong>Please note the:</strong>
	Based on the new travel guidelines (in some countries), all PCR results should bear a time component. So going forward, all your dates (date of sample collection, date of sample recept and date of testing) should be of the form dd/mm/YYYY HH:mm:ss e.g., 13/03/2021 14:03:00. 
		
	The time is in 24 hour clock format. <br><br>If you do not include time, the system will note be able to process the data for upload (you will only see Ooops). 

	<b>Additional mandatory columns about vaccination have been introduced, please fill them.</b>
	
</div>
@else
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	<strong>Please note the:</strong>
	<ol>
		<li>If you have only RDT results, fill the results in the "RDT TEST RESULT" column only.</li>
		<li>If you have only PCR results, fill the results in the "PCR RESULTS(If available)" column only.</li>
		<li>If you have both RDT and PCR results for the same patient, both the "RDT TEST RESULT" column and the "PCR RESULTS(If available)" column. This is mainly used where the patient tested negative but you carried out a confirmatory PCR test.</li>
		<li style="color:red">New 3 columns have been added, please download the latest template! Vaccination information is required</li>
	</ol> 
	N.B. You can as well use eLIF or the web version to submit one record at time
</div>
@endif
@endif


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
@else
	You do not have permission to perform this action
@endif

@stop