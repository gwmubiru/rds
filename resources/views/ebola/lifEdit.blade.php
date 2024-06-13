@extends('layouts/layout')
@section('content')
<ul class="breadcrumb">
	<li><a href="/">HOME</a></li>
	<li><a href="/cif/covid/form">CIF Form</a></li>
	<li><a href="/poe/covid/form">POE Form</a></li>
</ul>
<div id='d3' class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>'covid/update/','id'=>'form_id')) !!}

		<h3 class="panel-title" style="text-align:center"><strong>Lab Investigation Form for Suspected COVID-19 Cases	</strong></h3>
		<br>
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-inline">
					{!! Form::label('request_date', 'Date:', array('class' =>'col-md-1')) !!}
					{!! Form::text('request_date', $patient->request_date, array('class' => 'form-control col-sm-4 standard-datepicker-nofuture ')) !!}

					{!! Form::label('ulin', 'Unique Lab No:', array('class' =>'col-md-2  ')) !!}
					{!! Form::text('ulin', $patient->ulin, array('class' => 'form-control col-sm-4')) !!}

					{!! Form::label('serial_number', 'Serial Number:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('serial_number', $patient->serial_number, array('class' => 'form-control col-sm-4')) !!}
				</div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading "><strong>Interviewer Info</strong></div>
				<div class="panel-body">
					<div class="form-inline">
						{!! Form::label('interviewer_name', 'Name:', array('class' =>'col-md-1 ')) !!}
						{!! Form::text('interviewer_name', $patient->interviewer_name, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('interviewer_facility', 'Facility:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('interviewer_facility', $patient->interviewer_facility, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('interviewer_phone', 'Phone:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('interviewer_phone', $patient->interviewer_phone, array('class' => 'form-control col-sm-4')) !!}
					</div> <br><br>

					<div class="form-inline">
						{!! Form::label('where_sample_collected_from', '1. Where is sample collected?:', array('class' =>'col-md-3 ')) !!}
						<div class="radio-inline">{!! Form::radio('where_sample_collected_from', 'Patient home', $patient->where_sample_collected_from == "Patient home" ? true : false) !!} <span class="input-tag">Patient home</span></div>
						<div class="radio-inline">{!! Form::radio('where_sample_collected_from', 'Health Facility', $patient->where_sample_collected_from == "Health Facility" ? true : false) !!} <span class="input-tag">Health Facility</span></div>
						<div class="radio-inline">{!! Form::radio('where_sample_collected_from', 'Point Of Entry', $patient->where_sample_collected_from == "Point Of Entry" ? true : false) !!} <span class="input-tag">Point Of Entry</span></div>
						<div class="radio-inline">{!! Form::radio('where_sample_collected_from', 'Institution', $patient->where_sample_collected_from == "Institution" ? true : false) !!} <span class="input-tag">Institution</span></div>
						<div class="radio-inline">{!! Form::radio('where_sample_collected_from', 'Other', $patient->where_sample_collected_from == "Other" ? true : false) !!} <span class="input-tag">Other</span></div>
					</div><br>

					<div class="form-inline">
						{!! Form::label('who_being_tested', '2. Who is being tested?:', array('class' =>'col-md-3')) !!}

						<select style="width: 18%;" name="who_being_tested" id="who_being_tested">
							<option value="" selected="selected">Select...</option>
							<option value="Case" {!! $patient->who_being_tested == "Case" ? 'selected' : '' !!}>Case</option>
							<option value="Contact" {!! $patient->who_being_tested == "Contact" ? 'selected' : '' !!}>Contact</option>
							<option value="Point Of Entry" {!! $patient->who_being_tested == "Point Of Entry" ? 'selected' : '' !!}>Point Of Entry</option>
							<option value="Quarantine" {!! $patient->who_being_tested == "Quarantine" ? 'selected' : '' !!}>Quarantine</option>
							<option value="Alert" {!! $patient->who_being_tested == "Alert" ? 'selected' : '' !!}>Alert</option>
							<option value="Health Worker" {!! $patient->who_being_tested == "Health Worker" ? 'selected' : '' !!}>Health Worker</option>
							<option value="Other" {!! $patient->who_being_tested == "Other" ? 'selected' : '' !!}>Other</option>
						</select>
						<br><br>

						<div class="form-inline" name="hwt" id="hwt">
							{!! Form::label('reason_for_healthWorker_testing', 'HW&#39;s Facility:', array('class' =>'col-md-2 ')) !!}
							{!! Form::select('health_care_worker_facility',[""=>""]+$facilities,'',['id'=>'health_care_worker_facility', 'class'=>'form-control']) !!}
							<br><br>

							<div class="form-inline">

								{!! Form::label('reason_for_healthWorker_testing', '3. Reason for health care worker(HW) testing?:', array('class' =>'col-md-3 ')) !!}

								<select style="width: 18%;" name="reason_for_healthWorker_testing" onchange="if (this.value=='Other'){this.form['reason_for_healthWorker_testingOther'].style.visibility='visible'}else {this.form['reason_for_healthWorker_testingOther'].style.visibility='hidden'};">
									<option value="" selected="selected">Select...</option>
									<option value="Routine exposure" {!! $patient->reason_for_healthWorker_testing == "Routine exposure" ? 'selected' : '' !!}>Routine exposure</option>
									<option value="Quarantine" {!! $patient->reason_for_healthWorker_testing == "Quarantine" ? 'selected' : '' !!}>Quarantine</option>
									<option value="Other" {!! $patient->reason_for_healthWorker_testing == "Other" ? 'selected' : '' !!}>Other</option>
								</select>

								<input type="text" name="reason_for_healthWorker_testingOther" id="reason_for_healthWorker_testingOther" class="form-control input-sm" size="30" placeholder="Other reason for testing..."></input>
							</div> <br>
						</div> <br>
					</div>

					<div class="form-inline">
						{!! Form::label('isolatedPerson_test_day', '4. If person is isolated/quarantined, specify day of testing:', array('class' =>'col-md-3 ')) !!}

						<select style="width: 18%;" name="isolatedPerson_test_day" onchange="if (this.value=='Other'){this.form['isolatedPerson_test_dayOther'].style.visibility='visible'}else {this.form['isolatedPerson_test_dayOther'].style.visibility='hidden'};">
							<option value="" selected="selected"></option>
							<option value="Day 0" {!! $patient->isolatedPerson_test_day == "Day 0" ? 'selected' : '' !!}>Day 0</option>
							<option value="Day 7" {!! $patient->isolatedPerson_test_day == "Day 7" ? 'selected' : '' !!}>Day 7</option>
							<option value="Day 13" {!! $patient->isolatedPerson_test_day == "Day 13" ? 'selected' : '' !!}>Day 13</option>
							<option value="Other" {!! $patient->isolatedPerson_test_day == "Other" ? 'selected' : '' !!}>Other</option>
						</select>

						<input type="text" name="isolatedPerson_test_dayOther" id="isolatedPerson_test_dayOther" class="form-control input-sm" size="30" placeholder="Other specific testing day..." style="visibility:hidden;"></input>
					</div><br><br>

					<div class="form-inline">
						{!! Form::label('travel_out_of_ug_b4_onset', '5. Patient traveled out of Uganda in 2wks before onset (or sample-taking, if no symptoms)?', array('class' =>'col-md-3 ')) !!}

						<select style="width: 18%;" name="travel_out_of_ug_b4_onset" id="travel_out_of_ug_b4_onset">
							<option value="" selected="selected">Select...</option>
							<option value="1" {!! $patient->travel_out_of_ug_b4_onset == "1" ? 'selected' : '' !!}>Yes</option>
							<option value="0" {!! $patient->travel_out_of_ug_b4_onset == "0" ? 'selected' : '' !!}>No</option>
						</select>
						<br><br>
					</div><br>

						<div class="form-inline" name="onset" id="onset" style="display:none;">

							{!! Form::label('destination_b4_onset', '6. If Yes, where?', array('class' =>'col-md-3  ')) !!}
							{!! Form::text('destination_b4_onset', $patient->destination_b4_onset, array('class' => 'form-control col-sm-4')) !!}

							{!! Form::label('return_date', '7. Return Date:', array('class' =>'col-md-2  ')) !!}
							{!! Form::text('return_date', $patient->return_date, array('class' => 'form-control col-sm-4 standard-datepicker-nofuture')) !!}
						</div> <br>
					</div>
				</div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading "><strong>Section 1: Patient Information</strong></div>
				<div class="panel-body">

					<div class="form-inline">
						{!! Form::label('patient_surname', '1. Surname:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_surname', $patient->patient_surname, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('patient_firstname', '2. First Name:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_firstname', $patient->patient_firstname, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('sex', '3. Sex:', array('class' =>'col-md-1 ')) !!}
						<div class="radio-inline">{!! Form::radio('sex', 'Male', $patient->sex == "Male" ? true : false) !!} <span class="input-tag">M</span></div>
						<div class="radio-inline">{!! Form::radio('sex', 'Female', $patient->sex == "Female" ? true : false) !!} <span class="input-tag">F</span></div>
					</div><br>

					<div class="form-inline">
						<!-- {!! Form::label('dob', '4. DOB:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('dob', $patient->dob, array('class' => 'form-control col-sm-4  standard-datepicker-nofuture ')) !!} -->

							<!-- <label for="age">Or Estimated Age</label> -->
							{!! Form::label('age', '4. Estimated age:', array('class' =>'col-md-2 ')) !!}
							{!! Form::text('age', $patient->age, array('class' => 'form-control col-sm-4 ')) !!}
							<select style="width: 18%;" name="age_units" id="id_age_units" class="form-control input-sm">
								<option value="Year(s)" {!! $patient->age_units == "Year(s)" ? 'selected' : '' !!}>Years</option>
								<option value="Month(s)" {!! $patient->age_units == "Month(s)" ? 'selected' : '' !!}>Months</option>
								<option value="Day(s)" {!! $patient->age_units == "Day(s)" ? 'selected' : '' !!}>Days</option>
							</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('nationality', '5. Nationality:', array('class' =>'col-md-2	')) !!}
						{!! Form::text('nationality', $patient->nationality, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('patient_contact', '6. Phone No:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_contact', $patient->patient_contact, array('class' => 'form-control col-sm-12')) !!}

					</div> <br><br>

					<div class="form-inline">
						{!! Form::label('patient_village', '7. Village:', array('class' =>'col-md-2')) !!}
						{!! Form::text('patient_village', $patient->patient_village, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('patient_parish', 'Parish:', array('class' =>'col-md-2')) !!}
						{!! Form::text('patient_parish', $patient->patient_parish, array('class' => 'form-control col-sm-4')) !!}
					</div><br><br>

					<div class="form-inline">

						{!! Form::label('patient_subcounty', 'Sub-County:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_subcounty', $patient->patient_subcounty, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('patient_district', 'District:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_district', $patient->patient_district, array('class' => 'form-control col-sm-4')) !!}
					</div> <br><br>

					<div class="form-inline">
						{!! Form::label('patient_NOK', '8. Next of Kin:', array('class' =>'col-md-2')) !!}
						{!! Form::text('patient_NOK', $patient->patient_NOK, array('class' => 'form-control col-sm-4')) !!}

						{!! Form::label('nok_contact', '9. Next Of Kin Phone:', array('class' =>'col-md-2')) !!}
						{!! Form::text('nok_contact', $patient->nok_contact, array('class' => 'form-control col-sm-4')) !!}
					</div> <br>

				</div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading "><strong>Section 2: Clinical Information</strong></div>
				<div class="panel-body">
					<div class="form-inline">
						{!! Form::label('patient_symptomatic', '10. Is / Was patient symptomatic?', array('class' => 'col-md-3')) !!}
						<div class="radio-inline">{!! Form::radio('patient_symptomatic', 'Yes',$patient->patient_symptomatic == "Yes" ? true : false) !!} <span class="input-tag">Yes</span></div>
						<div class="radio-inline">{!! Form::radio('patient_symptomatic', 'No', $patient->patient_symptomatic == "No" ? true :  false) !!} <span class="input-tag">No</span></div>
					</div> <br>
					<div class="form-inline">
						{!! Form::label('symptomatic_onset_date', '11. Date Of onset of first symptom:', array('class' =>'col-md-3')) !!}
						{!! Form::text('symptomatic_onset_date', $patient->symptomatic_onset_date,  array('class' => 'form-control col-sm-4  standard-datepicker-nofuture')) !!}

						{!! Form::label('symptoms', '12. Symptoms:', array('class' =>'col-md-2')) !!}
						{!! Form::textarea('symptoms', $patient->symptoms, array('class' => 'form-control col-sm-4','style' => 'width:250px; height:100px')) !!}

					</div><br>

					<div class="form-inline">
						{!! Form::label('known_underlying_condition', '13. Known underlying conditions:', array('class' =>'col-md-3')) !!}
						{!! Form::textarea('known_underlying_condition', $patient->known_underlying_condition, array('class' => 'form-control col-sm-4','style' => 'width:250px; height:100px')) !!}

					</div>
				</div>
			</div>

			<div class="panel panel-primary">
				<div class="panel-heading "><strong>Section 3: Specimen Colection</strong></div>
				<div class="panel-body">
					<div class="form-inline">

						<table class="table table-bordered" id="item_table">
							<thead>
								<tr>
									<th class="text:center">Specimen Type <small></th>
										<th class="text:center">Specimen ID</th>
										<th class="text:center">Date collected</th>
										<th class="text:center">Sent to UVRI?</th>
									</tr>
								</thead>
								<tbody>
									@foreach($sample as $samples)
									<tr>
										<td>{!! $samples->specimen_type !!}</td>
										<td>{!! $samples->specimen_ulin !!}</td>
										<td>{!! $samples->specimen_collection_date !!}</td>
										<td>{!! $samples->sentToUvri !!}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
							<div align="center">
								{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('save'),
								array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			$(document).ready(function(){
				$('#who_being_tested').on('change', function () {
					if(this.value === "Health Worker"){
						$("#hwt").show();
					} else {
						$("#hwt").hide();
					}
				});
			});

			$(document).ready(function(){
				$('#travel_out_of_ug_b4_onset').on('change', function () {
					if(this.value === "1"){
						$("#onset").show();
					} else {
						$("#onset").hide();
					}
				});
			});
			//Date picker
			$('#datepicker').datepicker({
				autoclose: true
			})

			$(document).ready(function(){

				$(document).on('click', '.add', function(){
					var row_count = parseInt($('#row_count').val())+1;
					$('#row_count').val(row_count);
					var keyup = 'onkeyup="compute('+row_count+')"';


					var html = '';
					html += '<tr>';
						html += '<td><select name ="specimen_type[]"> <option value=""></option><option value="np / op swab">NP / OP Swab</option><option value="Blood">Blood</option><option value="other">Other</option> <input type="text" name="otherSampleType" class="form-control otherSampleType" /></td>';
							html += '<td><input class="form-control standard-datepicker-nofuture" name="specimen_collection_date[]" type="text" id="specimen_collection_date"></td>';
							html += '<td><input type="text" name="specimen_ulin[]" '+keyup+' class="form-control qty_to_order" id="specimen_ulin'+row_count+'" /></td>';
							html += '<td><input type="text" name="testing_lab[]" class="form-control testing_lab	" /></td>';
							html += '<td><button type="button" name="remove" class="btn btn-danger btn-sm remove"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
							$('#item_table').append(html);

						});

						$(document).on('click', '.remove', function(){
							$(this).closest('tr').remove();
						});

					});

					// Generates auto-fill of distri, hub and IP text boxes
					$(".standard-datepicker-nofuture").datepicker({
						dateFormat: "yy-mm-dd",
						maxDate: 0
					});


					/**
					*Convert Age to date and visa viz
					*/
					$("#dob").change(function(){
						set_age();
					});

					$("#age").change(function(){
						set_dob();
					});

					$("#id_age_units").change(function(){
						set_dob();
					});

					function round1(val){
						return Math.round(val*10)/10;
					}

					function set_dob(){
						var date_now = new Date();
						var now_s = date_now.getTime();
						var age = $("#age").val();
						var units = $("#id_age_units").val();
						var age_s=0;
						if(units=='M'){
							age = age/12;
							age_s = age*365*24*3600*1000;
						}else if (units=='D') {

							age_s = age*24*3600*1000;
						}else{
							age_s = age*365*24*3600*1000;
						}

						var dob_s = now_s-age_s;

						var dob = new Date(dob_s);
						//dob.setMonth(0, 1);
						$("#dob").combodate('setValue', dob);
					}

					function set_age(){
						var date_now = new Date();
						var now_s = date_now.getTime();

						var dob = new Date($("#dob").val());
						var dob_s = dob.getTime();
						var yrs = (now_s-dob_s)/(365*24*3600*1000) || 0;
						var fraction_of_a_month_in_a_year=(30/365)||2;

						if(yrs<1 && yrs >= fraction_of_a_month_in_a_year){//Age in Months
							var mths = yrs*12;
							$("#age").val(round1(mths));
							$("#id_age_units").val("M");
						}else if(yrs<fraction_of_a_month_in_a_year){//Age in Days

							$("#id_age_units").val("D");
						}else{//Age in Years
							$("#age").val(round1(yrs));
							$("#id_age_units").val("Y");
						}
					}
				</script>
				@endsection
