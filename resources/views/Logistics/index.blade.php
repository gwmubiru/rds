@extends('layouts/layout')
@section('content')
<style type="text/css">
.nav-tabs {
	margin-bottom: 5px;
}
.btn-primary {
	margin-bottom: 5px;
}

body {
	/* font: 90%/1.45em "Helvetica Neue", HelveticaNeue, Verdana, Arial, Helvetica, sans-serif; */
	margin: 0;
	padding: 0;
	color: #333;
	background-color: #fff;
}

</style>
<div class="panel-body">
<br><br>
	<ul class="breadcrumb">	<li><a href="/">HOME</a></li></ul>

	<div id='d3' class="panel panel-default">
		<div class="panel-body">
			<div class="panel-body">
				<form method="get" action="/Logisitiscsv">

					<div class='form-inline'>

						<label for="fro">From:
							{!! Form::text('fro', old('fro'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

							<label for="fro">To: </label>
							{!! Form::text('to', old('to'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

							<input type='submit' value='Generate CSV' class="btn btn-md btn-success">
						</form>
										<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#uploadCsv">Upload CSV</button>
						<a href="/newLogisticsData" class='btn btn-md btn-primary'>Add New Report</a>
					</div>


					<!-- upload CSV modal -->
<div class="modal fade bd-example-modal-lg" id="uploadCsv" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<!--Body-->
		<div class="modal-body">
			<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('importLogisiticsExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
				{!! Form::model('', array('url' => 'importLogisiticsExcel', 'files' => true, 'method'=>'post')) !!}
				<input type="file" name="import_file" />
				<button class="btn btn-primary">Import File</button>
			{!! Form::close() !!}
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
		</div>
	</form>
</div>
</div>
</div>

					<table class="table table-responsive-sm table-striped table-bordered table-sm" id="logistics">
						<thead>
							<tr>
								<th class="text-center">Facility</th>
								<th class="text-center">District</th>
								<th class="text-center">Commodity</th>
								<th class="text-center">Stock on Hand</th>
								<th class="text-center">Quantity Received</th>
								<th class="text-center">Total Tests Done</th>
								<th class="text-center">-/+ Adjustments</th>
								<th class="text-center">Comment</th>
								<th class="text-center">Reporting Period</th>
								<th class="text-center">Date Submitted</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($logistics AS $data)
							<?php
							echo "<td>$data->facility</td>";
							echo "<td>$data->district</td>";
							echo "<td>$data->commodity</td>";
							echo "<td>$data->opening_balance</td>";
							echo "<td>$data->quantity_received</td>";
							echo "<td>$data->total_consumption</td>";
							echo "<td>$data->adjustment</td>";
							echo "<td>$data->comment</td>";
							echo "<td>$data->start_date - $data->end_date</td>";
							echo "<td>$data->date_submitted</td>";
							?>

							</tr>
							@endforeach
						</tbody>
					</table>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
		<script>

			$(function() {
				$('#logistics').DataTable({
				});
			});

			$(".standard-datepicker-nofuture").datepicker({
				dateFormat: "yy-mm-dd",
				maxDate: 0
			});

		</script>
		@endsection()
