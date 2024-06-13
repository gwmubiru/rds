@extends('layouts/layout')

@section('content')

<?php
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;
	//echo "printintttt ".\Request::get("printed");
?>
<?php 
	$activ = "$('#results').addClass('active');";
	$print_url = "/results_list?printed=no&qced=YES";
	$printed_url = "/results_list?printed=yes&qced=YES";
	
	if(\Request::has('rj')){
		$activ = "$('#rejected_results').addClass('active');";
		$print_url = "/results_list?printed=no&qced=YES&rj=11";
		$printed_url = "/results_list?printed=yes&qced=YES&rj=11";
	}
	
	$printed=\Request::get("printed");

	if($printed=='yes'){
		$printed_actv="class=active";
		$print_actv="";
		$pr_label="already printed and dispatched";
	}else{
		$print_actv="class=active";
		$printed_actv="";
		$pr_label="print and dispatch";
	}
	

	$selected_batches = DispatchManager::getBatchesForDispatch();
	$nBatches = count($selected_batches);

// dd($selected_batches);

	if($nBatches == 0){
		
		if(Request::has('e404')) {
			$prev_page = Request::get('pg',1);

			if($prev_page > 1)
				dd('No more results -  Go Back');
			else
				dd(' Deep Search failed to find batch #: ' . Request::get('e404'));
		}
	}

	function make_nav_link($url, $title){
		$attributes = array('class' => 'test_type');
		return link_to($url, $title, $attributes);
	}


	function getRequestedTests($w)
	{
		if($w->tests_requested == "UNKNOWN"){

			$doPCR = $w->PCR_results_released == "YES" ? true : false;
			$doSCD = $w->SCD_results_released == "YES" ? true : false;

			if($doPCR && $doSCD) return "BOTH_PCR_AND_SCD";
			if($doPCR)	return "PCR";
			if($doSCD)	return "SCD";

			return "..";// should never be reached
		}

		else return $w->tests_requested;
	}
?>

<style type="text/css">
	.printed_row:hover{
		color: #468847;
		background-color: #dff0d8;
		border-color: #d6e9c6;
	}
	.not_printed:hover{
		color: #b94a48;
		background-color: #f2dede;
		border-color: #eed3d7;
	}


	th{
		text-align: center;
	}

</style>

<style type="text/css">
	.glyphicon-none:before {
	    content: "\2122";
	    color: transparent !important;
							
	    /* <i class="fa fa-fw">&nbsp;</i> */
	}
	.PCR_pending{
		color: brown;
	}
	.SCD_pending{
		color: brown;
	}

	.test_type{
		float: right; 
		display: inline-block;
	}

	.tab-content {
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    padding: 10px;
}

.nav-tabs {
    margin-bottom: 0;
}
</style>


	<div id="content">
	    <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
	        <li {{ $print_actv }}><a href="{{ $print_url }}#print" >Print</a></li>
	        <li {{ $printed_actv }}><a href="{{ $printed_url }}#printed" >Printed</a></li>
	    </ul>
	    <div id="my-tab-content" class="tab-content">
	        <div class="tab-pane active" id="print">   
	

	
	@if(\Request::has('rj'))
		<div style="float:left; font-weight: bold; font-size: larger">Rejected Results :: {{ $pr_label }}</div>		
		<img src="/REJECTED.png" style="height:1.85em; width: auto ; float:left; margin-left: 0.5em;">
	@elseif(\Request::has('scd'))
		<div style="float:left; font-weight: bold; font-size: larger">Sickle Cell Results :: {{ $pr_label }}</div>
		<img src="/SCD.png" style="height:2em; width: auto ; float:left; margin-left: 0.5em;">
	@else
		<div style="float:left; font-weight: bold; font-size: larger">EID Results :: {{ $pr_label }}</div>
		<img src="/EID.png" style="height:2em; width: auto ; float:left; margin-left: 0.5em;">
	@endif

	<table id='tab_id' class="table table-bordered" cellspacing="0" cellpadding="4" align="center" style="margin-top: 1em; border: 1px solid #ddd" >
		<thead>
			<tr>
				<th><small>Envelope Number</small></th>
				<th><small>Batch</small></th>
				<th><small>Facility | Hub</small>	</th>
				<th><small>Results Date</small>	</th>
				<th><small>Results Released? </small>	</th>
				<th><small>Print</small></th>
			</tr>
		</thead>
		<tbody>		

		<?php $first_batch_id = isset($selected_batches[0])?$selected_batches[0]->id:0; ?>

		@foreach($selected_batches as $w)

			<?php

				$last_batch_id = $w->id;
				$printed_PCR_results = json_decode($w->printed_PCR_results);
				$printed_SCD_results = json_decode($w->printed_SCD_results);
				$tests_requested = getRequestedTests($w);


				$tr_css = $printed_PCR_results ? "printed_row" : "not_printed ";
				$btn_class =  $printed_PCR_results ? "btn-default" : "btn-primary";
				// $envelope_btn_class =  $printed_SCD_results ? "btn-default" : "btn-primary";
				$icon_class =  $printed_PCR_results ? "glyphicon-ok" : "glyphicon-exclamation-sign";

				$PCR_results_released = $w->PCR_results_released == "YES" ? true : false;
				$SCD_results_released = $w->SCD_results_released == "YES" ? true : false;

				// dd($w);

				$PCR_test_requested = $tests_requested == "PCR" || $tests_requested == "BOTH_PCR_AND_SCD";
				$SCD_test_requested = $tests_requested == "SCD" || $tests_requested == "BOTH_PCR_AND_SCD";
				
				$PCR_status_icon = $PCR_test_requested && $PCR_results_released ? "glyphicon glyphicon-ok pull-right" : "glyphicon glyphicon-none pull-right";
				$SCD_status_icon = $SCD_test_requested && $SCD_results_released ? "glyphicon glyphicon-ok pull-right" : "glyphicon glyphicon-none pull-right";
				$separator_visibility = $PCR_test_requested && $SCD_test_requested ? "" : " display: none; ";
				$separator_visibility = ""; /* always on */

				$PCR_text_class = $PCR_test_requested && !$PCR_results_released ? "PCR_pending" : "";
				$SCD_text_class = $SCD_test_requested && !$SCD_results_released ? "SCD_pending" : "";

				$PCR_results_ready_to_print = $PCR_results_released; // was $PCR_test_requested && $PCR_results_released;
				$SCD_results_ready_to_print = $SCD_results_released; // was $SCD_test_requested && $SCD_results_released;

				$print_button_visibility = $PCR_results_ready_to_print || $SCD_results_ready_to_print ? "" : " display:none; ";
				$print_button_visibility = "";
			?>
			<tr class="{{$tr_css}}"  role="alert">

				<td> {{ substr($w->envelope_number, 0, 8) . "-" . substr($w->envelope_number, 8) }} </td>
				<td> {{ $w->batch_number }}</td>
				<td style="width: 22em;">
					<div style="float:left; ">
						{{ $w->facility }} 
					</div>
					<div style="float: right;">
						{{ $w->hubname }} 
					</div> 

				</td>
				<td>
					@if(Request::has('rj'))
						{{ $w->date_entered_in_DB }} 
					@elseif(Request::has('scd'))
						{{ $w->date_SCD_testing_completed }} 
					@else
						{{ $w->date_PCR_testing_completed }} 
					@endif

				</td>
				<td align="center">

					<div style="float:left">
						<a href="#" class="btn btn-default disabled" style="width: 5.8em; border: none">&nbsp;

							@if($PCR_test_requested)
									<i  class="{{ $PCR_status_icon }}"></i>&nbsp;
									<span class="{{ $PCR_text_class}}">EID</span>
							@endif

						</a>
					</div>

					<div style="float:left; padding-top: 4px; {{ $separator_visibility }} ">|</div>

					<div style="float:left;">
						<a href="#" class="btn btn-default disabled" style="width: 5.8em; border: none;">&nbsp;
							@if($SCD_test_requested)
								<i  class="{{ $SCD_status_icon }}"></i>&nbsp;
								<span  class="{{ $SCD_text_class }}">SCD</span>
							@endif
						</a>
					</div>

				</td>
				<td align="center">

					<div style="float:left; " >
						<a href="javascript:windPop('/facility_result?b={{$w->id}}&type=EID')" class="btn btn-primary btn-xs">Print EID</a>
						<a href="javascript:windPop('/facility_result?b={{$w->id}}&type=SCD')" class="btn btn-primary btn-xs">Print SCD</a>
					</div>	

					<div style="float:left; padding-left: 5px;">
						<a href="javascript:windPop('/hc_env/{{$w->id}}')" class="btn btn-primary btn-xs">Envelope</a>
					</div>

					<div style="float:left; padding-left: 5px;">
						<a href="javascript:windPop('/ai?fd=1&f={{$w->id}}&pp=1&t=1')" class="btn btn-primary btn-xs">Follow-up</a>
					</div>


				</td>
			</tr>


		@endforeach
	</tbody>

	</table>

	<?php 	$next_pg = 1;
			$prev_pg = 1;
			$url_params = "";
			$class_prev_link = "";
			$class_next_link = "";

			$pg = \Request::get('pg', 1);

			if($pg == 1){
				$class_prev_link = "disabled";
				$next_pg = 2;
			}else{
				$next_pg = $pg + 1;
				$prev_pg = $pg - 1;
			}

			$ff = Request::all();
			foreach ($ff as $key => $value) {
				if($key != "pg"){
					$url_params .= "&$key=$value";
				}
			}
	?>
	<p style="text-align: center">

		<a href="" id="deep_search" style="background: yellow; float: right"></a>


		<a href="/results_list?pg={{ $prev_pg }}{{ $url_params }}" class="btn {{ $class_prev_link }}">&laquo; Newer Batches</a>

		@if(Request::has('e404'))
			<a href="/results_list" id="reset_display" class="btn">[ Go To : Today's Batches ]</a>
		@else
			<a href="/results_list?{{ $url_params }}" id="reset_display" class="btn {{$class_prev_link}}">[ This is : Page {{ $pg }} ]</a>
		@endif

		<a href="/results_list?pg={{ $next_pg }}{{ $url_params }}" class="btn ">Older Batches &raquo; </a>

		&nbsp;
	</p>
	</div>
   </div>

</div>

<script type="text/javascript">
	{!! $activ !!}

	jQuery(document).ready(function ($) {
        $('#tabs').tab();
    });

	$(document).ready(function() { 
    	
    	var deep_search = $("#deep_search");
    	// var reset_button = $("#reset_display");
    	var search_box;
    	var batch_to_find = "";

		@if(Request::has('e404'))
			deep_search.hide();
			// reset_button.show();
		@else
			// loading for the first time:
			deep_search.hide();
			// reset_button.hide();
		@endif

		$('#tab_id').dataTable({
			"bSort" : false, 
			"bInfo" : true,
			"bPaginate": true,
			"iDisplayLength": 7,
			"deferRender": true,
    		"fnDrawCallback": function( oSettings ) {

    			deep_search.hide();
    			// reset_button.hide();

				if(!search_box){
					search_box = $("div#tab_id_filter  input[type=search]")[0];
				}

        		if ($(".dataTables_empty")[0]) {
    				
    				batch_to_find = search_box.value;
					deep_search.attr("href", "/results_list?&qced=YES&e404=" + batch_to_find)
					deep_search.empty().append("Do Deep Search for Batch #: " + batch_to_find);
    				deep_search.show();
		        }
    		}
  		});
	});

/*
	No data found? try:
	http://stackoverflow.com/questions/28221203/how-enable-button-when-no-results-found-datatables


*/	
function microtime(get_as_float)
{
var unixtime_ms = (new Date).getTime();
    var sec = Math.floor(unixtime_ms/1000);
    return get_as_float ? (unixtime_ms/1000) : (unixtime_ms - (sec * 1000))/1000 + ' ' + sec;
}

var t1 = {{ $time = microtime(true) }};
var t2 = microtime("as_float");

console.log(t1)
console.log(t2);
console.log(t2 - t1);

// alert(t2)

// alert( "Page generation took: " + (t2 - t1) )

</script>
<?php
// $time = microtime();
// $time = explode(' ', $time);
// $time = $time[1] + $time[0];
// $finish = $time;
// $total_time = round(($finish - $start), 4);
// echo 'Page generated in '.$total_time.' seconds.';
?>
@stop

