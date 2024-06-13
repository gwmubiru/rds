<?php namespace App\Http\Controllers;

use App\Models\Worksheet;
use EID\Lib\WorkSheetManager;

define('NUM_DUMMY_RESULTS', 8);

?>

@extends('layouts/layout')


@section('content')

<?php 

	// dd(\Request::all());

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
	


    function PCR_results_selector($sample_id, $selected_result='', $final=false) {

        $select = "";
	    $options = "";

	    if($final)
        	$possible_results = ['NEGATIVE', 'POSITIVE', 'INVALID'];
        else
        	$possible_results = ['', 'NEGATIVE', 'POSITIVE', 'LOW_POSITIVE', 'FAIL'];

        foreach ($possible_results as $key => $this_result) {
        	$selected = $this_result === $selected_result ? "selected" : "";
	        $options .= "<option $selected>$this_result</option>";
        }
        $select = "<select id='$sample_id' >" . $options . "\n</select>";

        return "\n\n" . $select . "\n\n";
    }


?>

<style type="text/css">
	.ws_row:hover{
		background-color: #fcf8e3;
	}
	th{
		text-align: center;
	}
</style>

<?php 	
$ws = Worksheet::findOrFail($ws_id);
		$this->worksheet_manager = new WorkSheetManager( $ws );
		$ws_samples = $this->worksheet_manager->getSamples();
		$nSamples_in_worksheet = count($ws_samples);
		$sample_IDs = array_keys($ws_samples);
?>

<section id='s3' class='mm'></section>
	<form id="f1" name="f1" method="get">
	<table  class="table table-bordered" cellspacing="0" cellpadding="4" align="center" style="margin-top: 1em; border: 1px solid #ddd" >
			
			<h1 align="center">Enter Dummy Results</h1>

			<tr>
				<th><small>Accession No.</small></th>
				<th><small>Infant Name</small>	</th>
				<th><small>Test # 1</small>	</th>
				<th style="background: brown"><small>Test # 2</small></th>
				<th><small>Test # 3</small></th>
 				<th><small>Expected Result</small></th>
			</tr>					

			<tr class="ws_row">
			<?php 	$id = $sample_IDs[0]; 
					$pid = $ws_samples[$id]; ?>

				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE', true) !!}</td>
			</tr>

			<tr class="ws_row">
			<?php 	$id = $sample_IDs[1]; 
					$pid = $ws_samples[$id]; ?>

				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, 'POSITIVE', true) !!}</td>
			</tr>
			<tr class="ws_row">
			<?php 	$id = $sample_IDs[2]; 
					$pid = $ws_samples[$id]; ?>


				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'FAIL') !!}</td>
				<td>{!! PCR_results_selector($id, 'POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'POSITIVE', true) !!}</td>
			</tr>
			<tr class="ws_row">
			<?php 	$id = $sample_IDs[3]; 
					$pid = $ws_samples[$id]; ?>


				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'FAIL') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE', true) !!}</td>
			</tr>
			<tr class="ws_row">
			<?php 	$id = $sample_IDs[4]; 
					$pid = $ws_samples[$id]; ?>


				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'POSITIVE', true) !!}</td>
			</tr>
			<tr class="ws_row">
			<?php 	$id = $sample_IDs[5]; 
					$pid = $ws_samples[$id]; ?>


				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE', true) !!}</td>
			</tr>
			<tr class="ws_row">
			<?php 	$id = $sample_IDs[6]; 
					$pid = $ws_samples[$id]; ?>


				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'INVALID', true) !!}</td>
			</tr>
			<tr class="ws_row">
			<?php 	$id = $sample_IDs[7]; 
					$pid = $ws_samples[$id]; ?>


				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'LOW_POSITIVE') !!}</td>
				<td>{!! PCR_results_selector($id, 'INVALID', true) !!}</td>
			</tr>
			<tr class="ws_row">
			<?php 	$id = $sample_IDs[8]; 
					$pid = $ws_samples[$id]; ?>


				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE', true) !!}</td>
			</tr>

			<tr class="ws_row">
			<?php 	$id = $sample_IDs[9]; 
					$pid = $ws_samples[$id]; ?>

				<td> {{ $id }} </td>
				<td> {{ $pid["infant_name"] }} </td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, '') !!}</td>
				<td>{!! PCR_results_selector($id, 'NEGATIVE', true) !!}</td>
			</tr>


	</table>

		<button type="submit" id="makeCSV" name="xx" value="YY" style="float: right; margin: -10px 0 10px 0">CREATE RESULTS (CSV) for Test #1</button>
		<div id="h" style="clear: both; float:right; background: #fad46a;display:none; padding:5px 10px">
			<a href="/ws?i=uploadFile&ws={{ $ws_id }}&dummy=yes">Upload The Results File</a>
		</div>
	</form>

<script type="text/javascript">
$(function () {


    $.fn.serializePanel = function(kv_sep) {
        //
        //  @Author: Richard K. Obore, Tornado Unit, The Better Data Company
        //
        //  3rd implementation I made based on uncle Tobias's code.
        //      2nd verion = serializeAllObjects() = I modified it to include radio buttons, un-ticked checkboxes, etc...
        //      3rd version = serializePanel() = It returns an object, p, with the following fields
        //
        //          p.o = the exact same object as would be returned by serializeAllObjects()
        //          p.k = an array containing the field names (i.e. keys) in the object
        //          p.v = an array containing the values of each field in the object, in the same order as their keys (as per p.k) 
        //          p.kv = a string containing key-value pairs, adjusted for use in an SQL update query
        //
        //
        //  You can convert all the input fields inside "this" jQuery object (which is usually a form) into a JSON object
        //  as follows:
        //      json_object = $('form_id').serializePanel().o;
        //
        //  You can then stringify() it for database storage or AJAX transmission:
        //      json_string = JSON.stringify(json_object);
        //


        var o = {};
        var p = {};
        var keys = [];
        var values = [];
        var quoted_values = [];
        var kv = [];//

        var a = this.find(":input");
        

        $.each(a, function() {

            var field_name = this.id || this.name;
            var field_value = this.value;


            if (o[field_name] !== undefined) {
                if (!o[field_name].push) {
                    o[field_name] = [o[field_name]];
                }
                o[field_name].push(field_value || "");
            } else {
                o[field_name] = field_value || "";
            }

            var this_key = field_name;
            var this_value = "'"+ replaceAll(field_value, "'", "`") + "'";// modified for use in DB (note the single quotes) 
            
            kv_sep = kv_sep || " = ";

            kv.push(this_key + kv_sep + this_value);
            keys.push(this_key);
            quoted_values.push(this_value);
            values.push(field_value);// the unmodified value        
        });
         
        p.o = o;
        p.k = keys;
        p.v = values;
        p.kv = kv;
        p.qv = quoted_values;   

        return p;


    // helper functions:
        function escapeRegExp(str) {
            return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        }

        function replaceAll(haystack, needle, replace) {
            // This function handles many cases correctly - but test with your data before you use e.g.  XML or anything with $$ fails 
            return haystack.replace(new RegExp(escapeRegExp(needle), "g"), replace);
        }
    };

    function dummy_ws_exists() {
    	
    	@if(\Request::has('dwx'))
    		{{ "return true;" }}
    	@else
    		{{ "return false;" }}
    	@endif

    }

	$("#makeCSV").on("click", function (){

		// alert('make sure x=a number');
		// return false;


		if( dummy_ws_exists() ){

			window.location = "/dcsv/{{ $ws_id }}/0/?x=1";// download it's CSV
			$("#h").show();
			return false;
		}


		var ff, dest, json_data;

	        ff = $("#f1").serializePanel("=");	        
	        delete ff.o["makeCSV"];// remove button data 
	        json_data = JSON.stringify(ff.o);

		dest = "/dcsv/{{ $ws_id }}/"+json_data;

		$.ajax({
		    url: dest + "?t=y",// trigger CSV creation
		    type: 'GET',
		    success: function(dd) {
		        window.location = dest + "?x=1";// download the created CSV
				$("#h").show();
		    }
		});

		return false;
	});
});

</script>
@stop