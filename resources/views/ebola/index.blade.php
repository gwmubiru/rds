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
table {
	width: 100%;
	word-break: break-all;
	border-style: solid;
}
</style>

<div class="panel-body">
	<br><br>
	<div class="container">

		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#submissions_summary">EVD Results</a></li>
			<li><a data-toggle="tab" href="#evd_summaries">Summaries</a></li>
			<!-- <li><a data-toggle="tab" href="#screening_summary_tab">Screening Summary</a></li> -->
			<!-- <li><a data-toggle="tab" href="#screened_persons_tab">Screened Persons</a></li> -->
		</ul>

		<div class="tab-content">

			<div id="submissions_summary" class="tab-pane fade in active">
				<!-- <h3>Summary of Submissions</h3> -->
				<form method="get" action="/evd/csv">

					<div class='form-inline'>

						<!-- <label for="fro">From:
						{!! Form::text('fro', old('fro'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

						<label for="fro">To: </label>
						{!! Form::text('to', old('to'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!} -->

						<!-- <input type='submit' value='Generate CSV Template' class="btn btn-md btn-success"> -->
					</form>
					<?php if (\Auth::user()->ref_lab == 2888 || session('is_admin')==1 || \Auth::user()->ref_lab == 3035 || \Auth::user()->ref_lab == 3036) : ?>
						<a href="/uploads/evdCSVResultTemplate.csv" download="evdCSVResultTemplate">Download EVD Result template from here</a>

						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#uploadCsv">Upload CSV</button>

						<script>
							$(document).ready(function(){
								$("#myModal").modal('show');
							});
						</script>
					<!--	<div id="myModal" class="modal fade">
						    <div class="modal-dialog">
						        <div class="modal-content">
						            <div class="modal-header">
						                <h2 class="modal-title" style="color:red"><b>NOTICE!</h2>
						                <button type="button" class="close" data-dismiss="modal">&times;</button>
						            </div>
						            <div class="modal-body">
										<p>Please download the new CSV template for uploading results. <br>The old one will be rejected.</p>
									</b>
									<i>If you have already downloaded the new template, please ignore this message.</i>

						            </div>
						        </div>
						    </div>
						</div>-->

					<?php endif; ?>
					<!-- <a href="/cif/evd" class='btn btn-md btn-primary'>Add New Report</a> -->
				</div>
				<br>
				<table class="table table-responsive-sm table-striped table-bordered table-sm" id="submissions"  style="width:100%" >
					<thead>
						<tr>
							<!-- <th class="text-center">Case ID</th> -->
							<th class="text-center">Lab No.</th>
							<th class="text-center">Patient Name</th>
							<th class="text-center">Sex</th>
							<th class="text-center">Age</th>
							<th class="text-center">Health Facility</th>
							<th class="text-center">District</th>
							<th class="text-center">Result</th>
							<th class="text-center">Organism</th>
							<th class="text-center">Test Date</th>
							<th class="text-center">Testing Lab</th>
							<th class="text-center"></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($patients AS $data)
						<tr>

							<td width='10%'>{{$data->lab_number}}</td>
							<td width='15%'>{{$data->patient_surname}} {{$data->patient_firstname}}</td>
							<td width='8%'>{{$data->sex}}</td>
							<td width='5%'>{{$data->age}}</td>
							<td width='12%'>{{$data->interviewer_facility}}</td>
							<td width='10%'>{{$data->interviewer_district}}</td>
							<td width='9%'>{{$data->result}}</td>
							<td width='10%'>{{$data->organism}}</td>
							<td width='9%'>{{$data->test_date}}</td>
							<td width='16%'>{{$data->testing_lab}}</td>
							<td><a href='/print_evd_result?id={{$data->id}}' class='btn btn-sm btn-primary'>Print / Download</a></td>

						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div id="evd_summaries" class="tab-pane fade">
				<h3>EVD Test Summaries</h3>
				<br>
				<table class="table table-responsive-sm table-striped table-bordered table-sm" id="evd_summaries_table">
					<thead>
						<tr>
							<th>District</th>
							<th>Total Samples Tested</th>
							<th>Positives</th>
							<th>Positivity Rate</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($testing_summary AS $data)
						<tr>
							<?php
							echo "<td>$data->interviewer_district</td>";
							echo "<td>$data->total_tests</td>";
							echo "<td>$data->positive_tests</td>";
							echo "<td>".(round($data->positive_tests/$data->total_tests*100)) ."%</td>";
							?>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- upload CSV modal -->
			<div class="modal fade bd-example-modal-lg" id="uploadCsv" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<!--Body-->
						<div class="modal-body">
							<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('upload_evd_csv') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
								{!! Form::model('', array('url' => '/upload_evd_csv', 'files' => true, 'method'=>'post')) !!}
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

		</div>
	</div>
</div>

<script>

	$(function() {
		$('#submissions').DataTable({

		});

		$('#evd_summaries_table').DataTable({
		});
		$('#screening_summary_table').DataTable({
		});
	});

	$(".standard-datepicker-nofuture").datepicker({
		dateFormat: "yy-mm-dd",
		maxDate: 0
	});

</script>
@endsection()

