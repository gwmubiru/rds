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
	<div class="container">

		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#submissions_summary">Submissions Summary</a></li>
			<li><a data-toggle="tab" href="#rdt_positives_tab">H.A.T RDT Positives</a></li>
			<li><a data-toggle="tab" href="#screening_summary_tab">Screening Summary</a></li>
			 <li><a data-toggle="tab" href="#screened_persons_tab">Screened Persons</a></li>
			<li><a data-toggle="tab" href="#covid_results">Covid Results</a></li>
		</ul>

		<div class="tab-content">

			<div id="submissions_summary" class="tab-pane fade in active">
				<h3>Summary of Submissions</h3>
				<table class="table table-responsive-sm table-striped table-bordered table-sm" id="submissions">
					<thead>
						<tr>
							<th class="text-center">District</th>
							<th class="text-center">HAT -ve</th>
							<th class="text-center">HAT +ve</th>
							<th class="text-center">Sars Cov-2 -ve</th>
							<th class="text-center">Sars Cov-2 +ve</th>
							<th class="text-center">Total Submissions</th>
							<th class="text-center">Submission without result</th>
							<th class="text-center">Last Submission Date</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($hat_district_summaries AS $data)
						<tr>
							<?php
							echo "<td>$data->district</td>";
							echo "<td>$data->hat_rdt_neg</td>";
							echo "<td>$data->hat_rdt_pos</td>";
							echo "<td>$data->cov_neg</td>";
							echo "<td>$data->cov_pos</td>";
							echo "<td>$data->total_tests</td>";
							echo "<td>$data->has_no_result</td>";
							echo "<td>$data->last_submission_date</td>";

							?>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div id="rdt_positives_tab" class="tab-pane fade">
				<h3>HAT RDT Positives</h3>
				<div class="container-fluid" style="width: 100%; overflow-x: auto;  overflow-y: auto;">
					<table class="table table-responsive-sm table-striped table-bordered table-sm" id="rdt_positives">
						<thead>
							<tr>
								<th class="text-center">Date</th>
								<th class="text-center">Testing site</th>
								<th class="text-center">Name of HAT RDT suspect</th>
								<th class="text-center">Sex</th>
								<th class="text-center">Age(Y)</th>
								<th class="text-center">Village</th>
								<th class="text-center">Parish</th>
								<th class="text-center">Subcounty</th>
								<th class="text-center">District</th>
								<th class="text-center">HAT RDT result</th>
								<th class="text-center">Gland puncture</th>
								<th class="text-center">Thick blood smears</th>
								<th class="text-center">HCT result</th>
								<th class="text-center">mAECT result</th>
								<th class="text-center">HAT case</th>
								<th class="text-center">Stage</th>
								<th class="text-center">Referred</th>
								<th class="text-center">Phone # or NoK</th>
								<th class="text-center">Remarks</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($rdt_positives AS $data)
							<tr>
								<?php
								echo "<td>$data->created_at</td>";
								echo "<td>$data->interviewer_facility</td>";
								echo "<td>$data->patient_surname</td>";
								echo "<td>$data->sex</td>";
								echo "<td>$data->age</td>";
								echo "<td>$data->village</td>";
								echo "<td>$data->parish</td>";
								echo "<td>$data->subcounty</td>";
								echo "<td>$data->district</td>";
								echo "<td>$data->patientHatRdtResult</td>";
								echo "<td>$data->patientGlandPuncture</td>";
								echo "<td>$data->patientThickBloodSmears</td>";
								echo "<td>$data->patientHCTResult</td>";
								echo "<td>$data->patientMaectResult</td>";
								echo "<td>$data->patientHatCase</td>";
								echo "<td>$data->patientHatCaseStage</td>";
								echo "<td>$data->patientReferralSite</td>";
								echo "<td>$data->nok_contact</td>";
								echo "<td>$data->patientRemarks</td>";
								?>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div id="screening_summary_tab" class="tab-pane fade">
				<h3>Screening Summary</h3>

				<div class='form-inline' style="float:right">

					<label for="fro">Select a   district to filter:

						<select name="hat_district" id="hat_district_filter" onchange="set_filter();">
							@<?php foreach ($districts as $key => $value): ?>
								<option value="?{{$key}}">{{ $value}}</option>
							<?php endforeach; ?>
						</select>

					</div>

					<table class="table table-responsive-sm table-striped table-bordered table-lg" id="screening_summary_table">
						<thead>
							<tr>
								<th class="text-center">S/no</th>
								<th class="text-center">Date</th>
								<th class="text-center">Site (Village/community)</th>
								<th class="text-center">Target Popl<u>n</u></th>
								<th class="text-center" colspan="6">Popl<u>n</u> screened by sex & age group</th>
								<th class="text-center">Total Popl<u>n</u> screened</th>
								<th class="text-center">Coverage(%)</th>
								<th class="text-center" colspan="2">RDT +ve</th>
								<th class="text-center" colspan="2">CTC +ve</th>
								<th class="text-center" colspan="2">mAECT +ve</th>
								<th class="text-center" colspan="2">HAT cases</th>
							</tr>
							<tr>
								<th class="text-center" style="background:gray"></th>
								<th class="text-center" style="background:gray"></th>
								<th class="text-center"  style="background:gray"></th>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center" colspan="2">0-14yrs</td>
								<td class="text-center" colspan="2">15-45yrs</td>
								<td class="text-center" colspan="2">46yrs+</td>

								<th class="text-center" style="background:gray"></th>
								<th class="text-center" style="background:gray"></th>
								<th class="text-center"  style="background:gray"></th>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
							</tr>
							<tr>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center"  style="background:gray"></td>
								<td class="text-center">M</td>
								<td class="text-center">F</td>
								<td class="text-center">M</td>
								<td class="text-center">F</td>
								<td class="text-center">M</td>
								<td class="text-center">F</td>
								<td class="text-center" style="background:gray"></td>
								<td class="text-center" style="background:gray"></td>
								<td class="text-center">M</td>
								<td class="text-center">F</td>
								<td class="text-center">M</td>
								<td class="text-center">F</td>
								<td class="text-center">M</td>
								<td class="text-center">F</td>
								<td class="text-center">M</td>
								<td class="text-center">F</td>
							</tr>

						</thead>
						<tbody>
							<?php $row=1; ?>
							@foreach ($sex_age_aggregate AS $data)
							<tr>
								<?php
								echo "<td>$row</td>";
								echo "<td>$data->created_at</td>";
								echo "<td>$data->village</td>";

								echo "<td>200</td>";
								echo "<td>$data->m_b14</td>";
								echo "<td>$data->f_b14</td>";
								echo "<td>$data->m_a15_b45</td>";
								echo "<td>$data->f_a15_b45</td>";
								echo "<td>$data->m_a46</td>";
								echo "<td>$data->f_a46</td>";

								echo "<td>$data->total_screened</td>";
								echo "<td>".$data->total_screened/200*100 . "%</td>";
								echo "<td>$data->m_rdt_pos</td>";
								echo "<td>$data->f_rdt_pos</td>";
								echo "<td>$data->m_ctc_pos</td>";
								echo "<td>$data->f_ctc_pos</td>";
								echo "<td>$data->m_maect_pos</td>";
								echo "<td>$data->f_maect_pos</td>";
								echo "<td>$data->m_hat_case</td>";
								echo "<td>$data->f_hat_case</td>";

								?>
							</tr>
							<?php $row++; ?>
							@endforeach
						</tbody>
					</table>
					{!! Form::close() !!}
				</div>

				<div id="screened_persons_tab" class="tab-pane fade">
					<div class='form-inline'>
							<form method="get" action="/generate_hatcsv">

							{!! Form::label('district', 'District:', array('style' =>'padding-top:8px')) !!}
							{!! Form::select('screening_district', $districts,'',['id'=>'facility_district', 'class'=>'form-control', 'style' =>'width:195px']) !!}

							<label for="fro">Screened Between:
							{!! Form::text('fro', old('fro'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

							<label for="fro">And: </label>
							{!! Form::text('to', old('to'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}
							<input type='submit' value='Generate CSV' class="btn btn-md btn-success"></input>
							</form>
						</div>
						<table class="table table-responsive-sm table-striped table-bordered table-lg" id="screened_persons">
                                                        <thead>
                                                                <tr>
                                                                        <th class="text-center">Village</th>
                                                                        <th class="text-center">Parish</th>
                                                                        <th class="text-center">Subcounty</th>
                                                                        <th class="text-center">District</th>
                                                                        <th class="text-center">Patient ID</th>
									<th class="text-center">Patient Name</th>
									<th class="text-center">Age</th>
                                                                        <th class="text-center">HAT RDT Result</th>
                                                                        <th class="text-center">Gland Puncture</th>
                                                                        <th class="text-center">Thick Blood Smear</th>
                                                                        <th class="text-center">HCT Result</th>
                                                                        <th class="text-center">Maect Result</th>
                                                                        <th class="text-center">HAT Case</th>
                                                                        <th class="text-center">Test Date</th>
                                                                        <th class="text-center">Submitted By</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                                <tr>
                                                                        @foreach ($hat_results AS $data)
                                                                        <?php
                                                                        echo "<td>$data->village</td>";
                                                                        echo "<td>$data->parish</td>";
                                                                        echo "<td>$data->subcounty</td>";
                                                                        echo "<td>$data->district</td>";
                                                                        echo "<td>$data->epidNo</td>";
									echo "<td>$data->patient_surname</td>";
									echo "<td>$data->age</td>";
                                                                        echo "<td>$data->patientHatRdtResult</td>";
                                                                        echo "<td>$data->patientGlandPuncture</td>";
                                                                        echo "<td>$data->patientThickBloodSmears</td>";
                                                                        echo "<td>$data->patientHCTResult</td>";
                                                                        echo "<td>$data->patientMaectResult</td>";
                                                                        echo "<td>$data->patientHatCase</td>";
                                                                        echo "<td>$data->created_at</td>";
                                                                        echo "<td>$data->username</td>";
                                                                        ?>
                                                                </tr>
                                                                @endforeach
                                                        </tbody>
                                                </table>

					{!! Form::close() !!}
					</div>

					<div id="covid_results" class="tab-pane fade">

						<table class="table table-responsive-sm table-striped table-bordered table-lg" id="covid_results_table">
							<thead>
							<tr>
									<th class="text-center">Village</th>
									<th class="text-center">Parish</th>
									<th class="text-center">Subcounty</th>
									<th class="text-center">District</th>
									<th class="text-center">Patient ID</th>
									<th class="text-center">Patient Name</th>
									<th class="text-center">Age</th>
									<th class="text-center">Covid-19 PCR Result</th>
									<th class="text-center">Test Date</th>
									<th class="text-center">Submitted By</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									@foreach ($covid_results AS $data)
									<?php
									echo "<td>$data->village</td>";
									echo "<td>$data->parish</td>";
									echo "<td>$data->subcounty</td>";
									echo "<td>$data->district</td>";
									echo "<td>$data->epidNo</td>";
									echo "<td>$data->patient_surname</td>";
									echo "<td>$data->age</td>";
									echo "<td>$data->result</td>";
									echo "<td>$data->created_at</td>";
									echo "<td>$data->username</td>";
									?>
								</tr>
							@endforeach
								</tbody>
							</table>
						</div>
				</div>
			</div>
		</div>
		<script>
			function set_filter(ajax) {
				ajax = document.getElementById("hat_district_filter").value;
				currentUrl = window.location.href+ajax;
				return	window.location.reload()
				// console.log('1234');
				// alert(ajax);
				return ajax
			}

			function getUrl() {
				var currentUrl = window.location.href;
				setUrl(currentUrl);
			}

			function setUrl(url) {
				var result = url + "?anotherargument";
				window.location.href = result;
			}

			$(function() {
				$('#submissions').DataTable({
				});

				$('#covid_results_table').DataTable({
				});

				$('#rdt_positives').DataTable({
				});
				$('#screening_summary_table').DataTable({

				});

				 $('#screened_persons').DataTable({
                                });

			});

			$(".standard-datepicker-nofuture").datepicker({
				dateFormat: "yy-mm-dd",
				maxDate: 0
			});

		</script>
		@endsection()
