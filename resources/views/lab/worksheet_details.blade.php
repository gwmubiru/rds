@extends('layouts/layout')


@section('content')
<?php 

	$selected_worksheets = WorkSheetManager::getWorkSheets();
	$nWorksheets = count($selected_worksheets);

	$web_server = env('WEB_HOST', "http://localhost");
?>


    <!-- First load moment.js, then pikaday.js and its jQuery plugin -->
    <script src="{{$web_server}}/js/moment.js"></script>    
    <script src="{{$web_server}}/js/pikaday.js"></script>
    <script src="{{$web_server}}/js/plugins/pikaday.jquery.js"></script>
    <link 	rel="stylesheet"	href="{{$web_server}}/css/pikaday.css">

	<?php $dbs = Input::get('dbs', ''); ?>
	<?php $worksheet_id = empty($ws) ? Input::get('ws','') : $ws; /* $ws comes from LabController */?>
	<?php $this_worksheet = empty($worksheet_id) ? new App\Models\Worksheet : App\Models\Worksheet::findOrFail($worksheet_id); ?>

	<?php $edit_allowed = empty($edit) ? Input::get('edit', '') : $edit; /* $edit comes from LabController */  ?>
	<?php $edit_allowed = $this_worksheet->exists ? $edit_allowed : true /* so we can create */; ?>

	<?php $html_attr = $edit_allowed ? [] : array('readonly' => 'YES');  /* set html attribute */ ?>

	<?php $show_menu = empty($hide_menu)? true : false ; ?>

	<?php $show_results = empty($sr)? false : true ; ?>
	
<section id='s3' class='mm'></section>
@include("quick_access_menu")<br><br><br>


	@if( $show_menu )
		@include('lab.partials._worksheet_menu')
	@endif

	@include('lab.partials._worksheet_data', array('this_worksheet' => $this_worksheet, 'sr' => $show_results, 'dbs' => $dbs) )


	@if(Input::has('dummy'))
		<?php $test_num = Input::get('dummy') + 1; ?>
		<button type="submit" id="makeCSV" name="xx" value="YY" 
				style="float: right; margin: 10px 10px 10px 50px">CREATE RESULTS (CSV)</button>
		<div id="h" style="clear: both; float:right; background: #fad46a;display:none; padding:5px 10px">
			<a href="/ws?i=uploadFile&ws={{ $ws }}&dummy={{$test_num}}">Upload The Results File</a>
		</div>

		<script type="text/javascript">
		$(function () {
			$("#makeCSV").on('click', function (evt) {
				// location.href = "/dcsv/{{ $ws }}/0/?x=2";
				location.href = "/dcsv/{{ $ws }}/0/?x={{$test_num}}";
				$("#h").show();
				return false;				
			});
		});
		</script>
	@endif


	@if(Input::has('dummy'))
		<?php $test_num = Input::get('dummy') + 1; ?>
		<button type="submit" id="makeCSV" name="xx" value="YY" 
				style="float: right; margin: 10px 10px 10px 50px">CREATE RESULTS (CSV)</button>
		<div id="h" style="clear: both; float:right; background: #fad46a;display:none; padding:5px 10px">
			<a href="/ws?i=uploadFile&ws={{ $ws }}&dummy={{$test_num}}">Upload The Results File</a>
		</div>

		<script type="text/javascript">
		$(function () {
			$("#makeCSV").on('click', function (evt) {
				// location.href = "/dcsv/{{ $ws }}/0/?x=2";
				location.href = "/dcsv/{{ $ws }}/0/?x={{$test_num}}";
				$("#h").show();
				return false;				
			});
		});
		</script>
	@endif


@stop