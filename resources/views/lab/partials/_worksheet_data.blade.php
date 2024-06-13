
<?php 

		$csv_error = false;
		$show_results = empty($show_results) ? false : true;
		$worksheet = Input::get('ws', '0');

		if( $show_results == false ){
			if(Input::has('sr')) $show_results = true;
		}


		if(Input::has('ws')){// use an existing worksheet.

			$instruction = Input::get('i', '');
			$main_button_text = ($instruction === "view" || $instruction === "toPDF") ? "Save As PDF" : "Update Worksheet";
			$show_main_button = true;
			$show_delete_button = ($instruction === "del") ? true : false;


		}else{// make a new worksheet

			$instruction = "create";
			$main_button_text = "Create Worksheet";
			$show_main_button = true;
			$show_delete_button = false;
		}

		if ($instruction === "edit") {	/* edit displays an editable form */
			$instruction = "update"; 	/* update tells LabController to save the form's data */
		}
		if ($instruction === "view") {	/* view displays an read-only form */
			$instruction = "toPDF"; 	/* toPDF tells LabController to turn worksheet into a PDF document */
		}
		if ($instruction === "del") {	/* del displays the form */
			$instruction = "delete"; 	/* delete tells LabController to delete the form's data */
			$show_main_button = false;
		}



		function getTestResult($this_sample, $n)
		{
			if(empty($this_sample["test_".$n."_result"])) 
				return null;
			else
				return $this_sample["test_".$n."_result"];
		}

		function printMostRecentResult($most_recent_result, $sample)
		{

			$star = "*";
			$accepted_result = empty($sample["accepted_result"]) ? "" : $sample["accepted_result"];

			if($accepted_result == NEGATIVE)
				return $accepted_result;

			if($accepted_result != null) // i.e. accepted_result == (POSITIVE or INVALID)
				return "<b style='color:teal;'>" . $accepted_result . " </b>";

		// No result accepted yet. Return most recent result:
			if($most_recent_result == null)
				return "<b style='color:red; background-color: yellow'>CSV ERR or BLANK</b>";
			else
				return "<b style='color:red'>" .$most_recent_result . $star . "</b>";
		}

		function eid_shorten($result) /* returns 4-letter version */
		{
			$result = strtoupper($result);

			if($result == "NEGATIVE") return "NEG-";
			if($result == "POSITIVE") return "POS+";
			if($result == "LOW_POSITIVE") return "LOW+";

			return $result; /* do not change others */
			
		}

		function printTestHistory($most_recent_result, $sample)
		{

			$nTestsDone = $sample["nTestsDone"];
			$most_recent_test = $nTestsDone;
			$selected_test = \Request::get('t', $most_recent_test);

			$output = "";

			for ($i=1; $i <= $nTestsDone; $i++) { 
				$this_result = $sample["test_". $i ."_result"];
				$this_result = eid_shorten($this_result);
				if($i == $selected_test)
					$output .= "<b>$this_result</b>, ";
				else
					$output .= "$this_result, ";
			}
			return $output;
		}

		function getMostRecentResult($result)
		{
			if($result == null){				
				return "<b style='color:red; background-color: yellow'>CSV ERROR</b>";
			}
			
			if($result == NEGATIVE)
				return $result;
			else
				return "<b style='color:red'>" . $result . "</b>";
		}


		function numTestsDone($sample)
		{
			$test_1_result = $sample["test_1_result"];
			$test_2_result = $sample["test_2_result"];
			$test_3_result = $sample["test_3_result"];
			$test_4_result = $sample["test_4_result"];
			$test_5_result = $sample["test_5_result"];

			if($test_1_result == null)
				return 1;
			
			if($test_2_result == null)
				return 1;// the if() above guarantees that $test_1_result is NOT null. Repeat till 5

			if($test_3_result == null)
				return 2;

			if($test_4_result == null)
				return 3;

			if($test_5_result == null)
				return 4;
			else
				return 5;
		}


?>


	{!! Form::model($this_worksheet, array('action'=>'LabController@store_worksheet', 'method'=>'get') ) !!}

	<table border="0" class="data-table" cellspacing="4" align="center">

		{!! Form::hidden('i', $instruction) !!}
		{!! Form::hidden('ws', Input::get('ws', '') ) !!}
		{!! Form::hidden('makePDF', 'no', array('id'=>'makePDF')) !!}


		<?php $data = []; ?>

		@include('lab.partials._worksheet_header', $html_attr)
		

		<tr style='background:#dddddd;'>

		@include('lab.partials._control_samples', $data)

		<!-- display samples -->
		<?php $wm = new WorkSheetManager( $this_worksheet ); ?>

		<?php $samples = $wm->getSamples();  ?>
		<?php $nSamples = count($samples); ?>
		<?php $maxSamplesPerRow = 6; ?>
		<?php $nSamplesAddedToRow = 2; /* the control samples */ ?>
		<?php $nSamplesAddedToWorksheet = $nSamplesAddedToRow; ?>
		<?php $undo_samples = []; ?>
				
		{!! MyHTML::lowNumberMsg($nSamples, SAMPLES_PER_WORKSHEET) !!}


		@foreach($samples as $this_sample)

			<?php 	$nSamplesAddedToRow++;  ?>
			<?php 	$nSamplesAddedToWorksheet++; ?>

			<?php 	$nTestsDone = numTestsDone($this_sample);
					$result = getTestResult($this_sample, $nTestsDone);
					if($result == null) $csv_error = true;

					$testID = $this_sample["current_test_id"];
					$axnNo = $this_sample["accession_number"];
					$undo_samples[$axnNo] = $nTestsDone;

					$bgcolor = $this_sample["accession_number"] == $dbs ? "yellow": "white";
			?>

			<td width='178px' bgcolor={{ $bgcolor }} style="border: 1px solid #eee">
	 			<div align='right' style="margin: 0.1em;  padding-right:0.5em;">
	 				<small>{{ $nSamplesAddedToWorksheet }}</small>
	 			</div>

				<div align="center">
					<div style="font-size: smaller">Envelope: {{ $this_sample["envelope_number"] }}</div>
					<div style="font-size: smaller"> Batch:   {{ $this_sample["batch_number"] }}</div>
					<div style="font-size: smaller"> Patient ID:   {{ $this_sample["infant_exp_id"] }}</div>

					<div style="font-size: smaller"> Test no: {{ $this_sample["current_test_id"] }}</div>

					@if($this_sample["physical_location"])
						<div style="font-size: smaller"> Zip-lock bag: {{ $this_sample["physical_location"] }}</div>
					@endif


					@if($show_results)
						@if(\Request::get('t', false))
							<div style="margin:0.25em;">{!! printTestHistory($result, $this_sample) !!}</div>
						@else
							<div style="margin:0.25em;">{!! printMostRecentResult($result, $this_sample) !!}</div>
						@endif
					@else
						<div style="margin:0.25em;">
							<?php $lab_no = $this_sample["current_test_id"] ?>
							<?php echo \DNS1D::getBarcodeHTML($lab_no, "C128A", 1, 35);?>
						</div>
					@endif

				</div>
			</td>
			
			@if($nSamplesAddedToRow === $maxSamplesPerRow)
				</tr>
				<tr style='background:#dddddd;'>
				<?php $nSamplesAddedToRow = 0; ?>
			@endif
		@endforeach
		<!-- end sample -->
			     
		</tr>

		@if($nSamples == SAMPLES_PER_WORKSHEET)
		<tr bgcolor="#999999">
			<td colspan="6"  align="center">		
			@if($show_main_button)
				<a href="#"><img 	id="download_pdf" 
							src="{{$web_server}}/images/pdf-download.png"
							style="height:3.5em; width:auto" 
							alt="Click to enable/disable PDF download" 
							title="Download worksheet as PDF (click to enable/disable)"
				></a>
				
				<button class="greenButtonRound" value="pws"> {{ $main_button_text }}</button>

				@if($instruction === "view" || $instruction === "toPDF")
					<a href="/eid_review/{{ $worksheet }}" class="btn btn-primary">Review Batches</a>
				@endif
			@endif

			@if($show_results && $csv_error)
				<a href="/csvreload/{{ Input::get('ws', '0') }}">Reload the CSV </a>
				<!-- <button class="xx" value="redoCSV"> RELOAD THE CSV</button>			 -->
			@endif

			@if($show_delete_button)
				<button type="submit" style="margin-left: 0px;" class="btn btn-danger" value="x" id="delete_button">
					Delete This Worksheet
				</button>
			@endif

			</td>
		</tr>
		@endif
	</table>
	{!! Form::close() !!}

				<style type="text/css">
					button.greenButtonRound {
					    border: 2px solid #ccc;
					    border-radius: 5px;
					    background-color: brown;
					    font-family: helvetica, tahoma, arial;
					    color: #ffffff;
					    font-weight: 400;
					    text-decoration: none;
					    margin : 20px;
					    padding: 10px 20px 10px 20px;
					    display: inline-block;
					    font-size: 1em;
					}
					button.greenButtonRound:hover {
					    border: 2px solid #ccc;
					    background-color: green;
					}

					img#download_pdf {
						-webkit-filter: grayscale(100%);
						filter: grayscale(100%);
						filter: url(#greyscale);
						filter: gray;
						transition: 1s;
					}
					img#download_pdf:hover {
						-webkit-filter: grayscale(0);
						filter: grayscale(0);
						filter: none;
					}					

				</style>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg">
					<filter id="greyscale">
						<feColorMatrix 	type="matrix" 
										values="0.3333 	0.3333 	0.3333 	0	0
												0.3333 	0.3333 	0.3333 	0 	0
												0.3333 	0.3333 	0.3333 	0 	0
												0 		0 		0 		1 	0" />
					</filter>
				</svg>
    <script type="text/javascript">

        $(function(){


		    var disablePDF = {
				"-webkit-filter": "grayscale(100%)",
				"filter": "grayscale(100%)",
				"filter": "url(#greyscale)",
				"filter": "gray",
				"transition": "1s"
		    
		    };
		    
		    var enablePDF = {
				"-webkit-filter": "grayscale(0)",
				"filter": "grayscale(0)",
				"filter": "none"
		    };

		    var downloadAsPDF = {{ session('downloadAsPDF') ?: true }};
		    var style = downloadAsPDF ? enablePDF : disablePDF;
		    var toPDF = downloadAsPDF ? "YES" : "NO";
		    			

		    $("#makePDF").val(toPDF);
  			$("img#download_pdf").css(style);


			$("img#download_pdf")
			  	.on( "click", function() {
			  		downloadAsPDF = !downloadAsPDF; // toggle it
			  	})
			  	.on( "mouseenter", function() {
			  		if(downloadAsPDF){
			  			$( this ).css(disablePDF);
			  		}else{
			  			$( this ).css(enablePDF);
			  		}
			  	})
			  	.on( "mouseleave", function() {
			  		if(downloadAsPDF){
			  			$( this ).css(enablePDF);
			  		}else{
			  			$( this ).css(disablePDF);
			  		}
			  	});

			$("#delete_button").on("click", function (evt) {

				var reply = confirm("Really Delete this worksheet?");				
				if (reply === false)	return false;// cancel the delete

			})


			$("#redoCSV").on("click", function(){

				location.href = '/ws?i=uploadFile&ws={{ Request::get('ws', 0)}}';
				
			});

		    $('.datepicker').pikaday({
		        format: 'YYYY-MM-DD'
		    });


            
            function format_dates(){
            
                var formatted_date;
                var dateFields = $(".datepicker");
                var nDateFields = dateFields.length;
                var current_dateField;

                for(var i=0; i<nDateFields; i++){
                    current_dateField = dateFields[i];

                    if(current_dateField.defaultValue === "") continue;
                    formatted_date = moment(current_dateField.defaultValue).format("Do MMM YYYY");
                    current_dateField.value = formatted_date;
                }                
            }

            // format_dates();
        });

    </script>
