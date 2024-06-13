@extends('layouts/layout')


@section('content')

<?php 
	$selected_worksheets = WorkSheetManager::getWorkSheets();
	$nWorksheets = count($selected_worksheets);

?>

<?php 

	function checkForErrors($dummy_result, $tn)// tn = test number
	{
		$css_class = "";
		$dummy_run = (array) $dummy_result;

		if($tn === "FINAL"){
			$actual_result = $dummy_run["actual_final_result"];
			$expected_result = $dummy_run["expected_final_result"];		
		}else{
			$actual_result = $dummy_run["test_". $tn ."_result"];
			$expected_result = $dummy_run["expected_test_". $tn ."_result"];		
		}

		if(empty($actual_result)){
			return $css_class = "result_ok";// test not yet done	
		}
		

		if($actual_result == $expected_result)
			return $css_class = "result_ok";
		else
			return $css_class = "result_error";
	}


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

    function PCR_results_selector($selected_result) {
        
        $select = "";
        $params = "";
	    $options = "<option></option>";


        $results = ['NEGATIVE', 'POSITIVE', 'LOW_POSITIVE', 'FAIL', 'INVALID'];

        foreach ($results as $key => $this_result) {
        	$selected = $this_result === $selected_result ? "selected" : "";
	        $options .= "<option $selected>$this_result</option>";
        }
        $select = "<select " . $params . ">" . $options . "\n</select>";

        return "\n\n" . $select . "\n\n";
    }




	$sql = "SELECT 	id, 
					infant_name, 
					test_1_result, expected_test_1_result, 
					test_2_result, expected_test_2_result, 
					test_3_result , expected_test_3_result ,  
					
					if(accepted_result is not null, 'YES', 'NO') as all_tests_completed, 

					expected_final_result , 
					accepted_result as actual_final_result   

			FROM 	dbs_samples, pcr_dummy_results 
			WHERE 	pcr_dummy_results.worksheet_number = '$ws_id' AND  pcr_dummy_results.sample_id = dbs_samples.id ";

	$db_rows = \DB::select($sql);



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
</style>

<section id='s3' class='mm'></section>
	<table  class="table table-bordered" cellspacing="0" cellpadding="4" align="center" style="margin-top: 1em; border: 1px solid #ddd" >
			
			<h1 align="center">Dummy Results for Worksheet # {{ $ws_id }}</h1>

			<tr style="background-color: #eee">
				<th colspan="2">INFANT DATA</th>
				<th colspan="2">TEST #1</th>
				<th colspan="2">TEST #2</th>
				<th colspan="2">TEST #3</th>
				<th rowspan="2">All tests<br> completed?</th>
				<th colspan="2">FINAL RESULTS</th>
			</tr>					
			<tr style="background-color: #eee">
				<th><small>Accession No.</small></th>
				<th><small>Infant Name</small>	</th>
				<th><small>Expected</small>	</th>
				<th><small>Actual</small></th>
				<th><small>Expected</small>	</th>
				<th><small>Actual</small></th>
				<th><small>Expected</small>	</th>
				<th><small>Actual</small></th>
				<th><small>Expected</small>	</th>
				<th><small>Actual</small>	</th>
			</tr>					
			<?php $test_num = 0; ?>

			@foreach($db_rows as $dummy_result)

			<tr class="ws_row">
				<td> {{ $dummy_result->id }} </td>
				<td> {{ $dummy_result->infant_name }} </td>
				<?php $t = 0; ?>
				<?php $rcss = checkForErrors($dummy_result, 1) ?>
				<td class="{{ $rcss }}" align="right"> {{ $dummy_result->expected_test_1_result }} </td>
				<td class="{{ $rcss }}"> {{ $dummy_result->test_1_result }} </td>
				<?php $t = empty($dummy_result->test_1_result) ? $t :  $t+1; ?>

				<?php $rcss = checkForErrors($dummy_result, 2) ?>
				<td class="{{ $rcss }}" align="right"> {{ $dummy_result->expected_test_2_result }} </td>
				<td class="{{ $rcss }}" > {{ $dummy_result->test_2_result }} </td>
				<?php $t = empty($dummy_result->test_2_result) ? $t :  $t+1; ?>
				
				<?php $rcss = checkForErrors($dummy_result, 3) ?>
				<td class="{{ $rcss }}" align="right"> {{ $dummy_result->expected_test_3_result }} </td>
				<td class="{{ $rcss }}" > {{ $dummy_result->test_3_result }} </td>
				<?php $t = empty($dummy_result->test_3_result) ? $t :  $t+1; ?>
				
				<td> {{ $dummy_result->all_tests_completed }} </td>
				
				<?php $rcss = checkForErrors($dummy_result, "FINAL") ?>
				<td class="{{ $rcss }}" align="right"> {{ $dummy_result->expected_final_result }} </td>
				<td class="{{ $rcss }}" > {{ $dummy_result->actual_final_result }} </td>
				<?php $test_num = ($t > $test_num) ? $t : $test_num; ?>
			</tr>

			@endforeach
	</table>
		<button id="make_new_worksheet" name="xx" value="YY" style="float: right; margin: -10px 0 10px 0; background: brown; color: #eee; height: 4em; width: 15em;">PREPARE <BR>NEXT WORKSHEET </button>

	<script type="text/javascript">
		$(function (){

			$("#make_new_worksheet").click(function (evt) {
				location.href = "/nextws/{{$ws_id}}?x={{ $test_num+1 }}";
			});
		});
	</script>

@stop