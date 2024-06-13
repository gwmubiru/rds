@extends('layouts/layout')

@section('content')

<?php

use EID\Lib\scwsMaker;
use App\Http\Controllers\LabController;

	$s = new scwsMaker(new LabController);
	$scws_data = $s->getData();
	
?>

<style type="text/css">
	.ws_row:hover{
		background-color: #fcf8e3;
	}
	th{
		text-align: center;
	}
	.result_error{
		background-color: yellow;
		color: red;
	}

	th.tda{
		display:block;
		text-decoration:none;
	}
	th.tda:hover {
		background-color:#CCFF00
	}
	
	
/* CSS modifications for wizard */
	div.content.clearfix{
		overflow: scroll;
	}
	.steps{
		overflow:scroll;
		height:35em;
	}
	.wizard .content {
    min-height: 100px;
	}
	.wizard .content > .body {
	    width: 100%;
	    height: auto;
	    padding: 15px;
	    position: relative;
	}
	#progress_bar{
		font-size:1em; display: table-cell; vertical-align: middle;
	}

</style>

<link rel="stylesheet" href="/css/normalize.css">
<link rel="stylesheet" href="/css/main.css">
<link rel="stylesheet" href="/css/jquery.steps.css">
<script src="/js/modernizr-2.6.2.min.js"></script>
<script src="/js/jquery.cookie-1.3.1.js"></script>
<script src="/js/plugins/jquery.steps.js"></script>

<section id='s3' class='mm'></section>
{!! Form::open(array('url' => '')) !!}
	<h2 align="center">
		Make Sickle Cell Worksheet 
		<button id="x" style="float:right; font-size:0.7em">SAVE WORKSHEET</button>
	</h2>

	<center>

		<div class="progress" 
				style="margin: 1em 0 1em 0; width:100%; height:2em; display: table; overflow: hidden;">
	  		<div class="progress-bar progress-bar-danger progress-bar-striped active" id="progress_bar"
	  				aria-valuemin="0" aria-valuemax="100" role="progressbar" aria-valuenow="1" 
	  				style="width:1%;" >
	    		<div style="line-height: 2em" id="progress_msg">&nbsp;</div>
	  		</div>
		</div>
	</center>



	<div style="clear:both"></div>

	<?php $k=1; ?>

	<div id="wizard" style="text-align: center">

	@foreach($scws_data as $this_mg)
	<!-- <h3>{!! $this_mg->header->physical_location !!}</h3> -->
	<h3></h3>
	<table  align="center" 
			class="table table-bordered" 
			cellspacing="0" cellpadding="4" 
			style="margin: 1em; border: 1px solid #ddd; width:90%" >
			
			<tr>
				<td colspan="5" align="right"><a href="#" class="go_home">Go To Home Page</a></td>
			</tr>

			<tr style="background-color: #eee">
				<th>#</th>
				<th>Accession No.</th>
				<th>Name</th>
				<th>Exp ID</th>
				<th class="tda">
					{!! Form::checkbox("grp_".$this_mg->header->id, "YES", null, array("id"=>"grp_".$this_mg->header->id) ) !!}
					<a href="#" class="add_all_samples" grp="{{ $this_mg->header->id }}"
						style="text-transform:none; color:white">Add Sample?</a>
				</th>
			</tr>
			<?php $i=0 ?>

			@foreach($this_mg->samples as $this_sample)
			<?php $parent = $this_sample->group_id; $i++;?>
			<tr style="background-color: #eee">
				<td align="right">{{ $i }}.&nbsp;&nbsp;</td>
				<td align="center">{{ $this_sample->id }}</td>
				<td>{{ $this_sample->infant_name }}</td>
				<td align="center">{{ $this_sample->infant_exp_id }}</td>
				<td align="center">
					{!! Form::checkbox("src_grp_".$parent, "YES", null, 
							array("class"=>"cbox src_grp_".$parent, "id" => "dbs_".$this_sample->id) ) !!}
					{{ $parent }}/{{ $i}}
				</td>
			</tr>
			@endforeach
	</table>
	@endforeach

	<h3>Home</h3>
	<table  align="center" id="home"
			class="table table-bordered" 
			cellspacing="0" cellpadding="4" 
			style="margin: 1em; border: 1px solid #ddd; width:90%" >

			<tr style="background-color: #eee">
				<td colspan="5" align="right">Home Page</td>
			</tr>
			<tr style="background-color: #eee">
				<th>#</th>
				<th>Source</th>
				<th>Physical Location</th>
				<th>Samples</th>
				<th>Action</th>
			</tr>

		<tbody>
		<?php $nSamplesAvailable = 0; ?>
		@foreach($scws_data as $mg)
		<?php $nSamples = count($mg->samples); $nSamplesAvailable += $nSamples; ?>
			<tr style="background-color: #eee">
				<td>{!! $mg->header->id !!}</td>
				<td>{!! trim($mg->header->source) !!}</td>
				<td>{!! $mg->header->physical_location !!}</td>
				<td>{!! $nSamples !!} </td>
				<td> <a href="#{{ reset($mg->samples)->group_id }}" step="{{ reset($mg->samples)->group_id }}" class="show_details">Show Samples</a></td>
			</tr>

		@endforeach

			<tr style="background-color: #eee">
				<td colspan="3" align="right"><b>Total Samples Available:</b></td>
				<td><b>{!!  number_format($nSamplesAvailable) !!}</b></td>
				<td> &nbsp; </td>
			</tr>

		</tbody>
	</table>
	</div>
{!! Form::close() !!}

	<script type="text/javascript">
		$(function (){

            $("#wizard").steps({
                headerTag: "h3",
                bodyTag: "table",
                transitionEffect: "slideLeft",
                stepsOrientation: "vertical",
                enableAllSteps: true,
                startIndex: {{ count($scws_data) }}
            });

            $(".show_details").on("click", function () {
            	var new_step = $(this).attr("step");
            	$("#wizard").steps("setStep", new_step-1);
            });

            $(".go_home").on("click", function () {
            	var home = {{ count($scws_data) }};
            	$("#wizard").steps("setStep", home);
            });

            $('#home').DataTable();


        	var nBoxesChecked = 0;

        	$("input.cbox").on('change', function (argument) {
        		if(this.checked) 
        			nBoxesChecked++;
        		else 
        			nBoxesChecked--;

        		updateProgressBar();
        	});

        	function updateProgressBar (nv) {

        		var newVal = nv || nBoxesChecked;
        		var maxValue = {{ \SCManager::getNumSamplesPerWorksheet() }};
        		var newValue = parseInt((newVal > maxValue) ? maxValue : newVal);
        		var newWidth = parseInt((newValue/maxValue)*100);
        		var msg = "";

				if(newWidth < 35 || newWidth == 100)
					msg = newWidth + "%";
				else
					msg = newWidth + "% complete (add " + (maxValue - newValue) + " samples)";

    			$("#progress_msg").text(msg);
				$("#progress_bar").attr("style", "width: " + newWidth + "%");
				$("#progress_bar").attr("aria-valuenow", newValue);
        	}

            $("#x").on("click", function () {
            	
            	var data = "";
            	var str_data = "";
            	var nSamplesNeeded = {{ \SCManager::getNumSamplesPerWorksheet() }};
            	var nSamplesAdded = 0;

            	
            	$("input.cbox:checked").each(function () {
            		if(nSamplesAdded < nSamplesNeeded){
	            		data = data + this.id + ",";
            			nSamplesAdded++;
            		}
            	});


        		if(nSamplesAdded < nSamplesNeeded){
            		alert("Not enough samples to create a Worksheet:\n\n " + 
            				"You need " + (nSamplesNeeded - nSamplesAdded) + " more samples" );
        			return false;
        		}

            	str_data = JSON.stringify(data);
            	console.log(str_data);
		        var out = {};
		            out.q = str_data;

		        $(this).attr('disabled','disabled');
            	$.get("/scws_store/", out, function (d) {
            		if(d < 0){// should never happen
            			alert("Not enough samples:\n\nYou need " + (-1*d) + " samples to create a Worksheet" );
            		}else{
	            		console.log(d);
	            		location.href = "/scws/"+d;        			
            		}
            	}, "text");

            	return false;
            });

            $(".add_all_samples").on("click", function () {

            	var group_id = $(this).attr("grp");
            	var selected_group = "." + "src_grp_" + group_id;
            	var selector = "#grp_" + group_id;

            	var old_state = $(selector).prop("checked");
            	var new_state = !old_state;

            	$(selector).prop("checked", new_state);

            	$(selected_group).each(function () {
            		$(this).prop("checked", new_state);
	
	        		if(new_state == true) 
	        			nBoxesChecked++;
	        		else
	        			nBoxesChecked--;
            	});
            	updateProgressBar();
            })
		});
	</script>

@stop