@extends('layouts/layout')
@section('content')

<ul class="breadcrumb">
	<li><a href="/">HOME</a></li>
	<li><a href="/lif/covid/form">LIF Form</a></li>
	<li><a href="/poe/covid/form">POE Form</a></li>
</ul>

<div id='d3' class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>'covid/update/','id'=>'form_id')) !!}

		<h3 class="panel-title" style="text-align:center"><strong>Interim 2019 Novel Coronavirus (2019-nCov) Case Investigation Form</strong></h3>
		<p style="text-align:center; color:#184e7b;"><i>If you have questions, contact the Public Health Emergency Operations Center (PHEOC)
			<br> Toll Free line:0800203033, MoH Call center: 08001000066</i></p>
			<br>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="form-inline">
						{!! Form::text('formType', 'cif', array('class' => 'form-control hidden')) !!}
					</div>
					<div class="form-inline">
						{!! Form::label('epidNo', 'EPID No:(for UVRI only)', array('class' =>'col-md-3 ')) !!}
						{!! Form::text('epidNo', $patient->epidNo, array('class' => 'form-control col-sm-4 text-line')) !!}
					</div>
				</div>

				<div class="panel panel-primary">
					<div class="panel-heading "><strong></strong></div>
					<div class="panel-body">
						<div class="form-inline">
							{!! Form::label('interviewer_name', 'Interviewer Name:', array('class' =>'col-md-2 ')) !!}
							{!! Form::text('interviewer_name', $patient->interviewer_name, array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('interviewer_phone', 'Phone:', array('class' =>'col-md-1 ')) !!}
							{!! Form::text('interviewer_phone', $patient->interviewer_phone, array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('interviewer_email', 'Email:', array('class' =>'col-md-1 ')) !!}
							{!! Form::text('interviewer_email', $patient->interviewer_email, array('class' => 'form-control col-sm-6 text-line')) !!}

						</div> <br><br>

						<div class="form-inline">
							{!! Form::label('health_facility', 'Health Facility:', array('class' =>'col-md-2 ')) !!}
							{!! Form::text('health_facility', $patient->health_facility, array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('facility_sub_district', 'sub District:', array('class' =>'col-md-1')) !!}
							{!! Form::text('facility_sub_district',$patient->facility_sub_district, array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('facility_district', 'District:', array('class' =>'col-md-1')) !!}
							{!! Form::select('facility_district',[""=>""]+$districts,$patient->patient_district,['id'=>'patient_district', 'class'=>'form-control col-sm-4', 'style' =>'width:195px']) !!}

						</div><br>
					</div>

					<div class="form-inline">

						{!! Form::label('patient_surname', 'Patient Name:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_surname',$patient->patient_surname, array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('caseID', 'Case ID:', array('class' =>'col-md-1 ')) !!}
						{!! Form::text('caseID', $patient->caseID, array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('sex', 'Sex:', array('class' =>'col-md-1 ')) !!}
						<div class="radio-inline">{!! Form::radio('sex', 'Male', false) !!} <span class="input-tag">M</span></div>
						<div class="radio-inline">{!! Form::radio('sex', 'Female', false) !!} <span class="input-tag">F</span></div>
					</div><br>

					<div class="form-inline">
						<!-- {!! Form::label('dob', 'DOB:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('dob', $patient->dob, array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!} -->


						<div class="form-group">
							{!! Form::label('age', 'Age:', array('class' =>'col-md-2 ')) !!}
							<!-- <input type="text" name="age" id="age" class="form-control input-sm" size="20"> -->
							{!! Form::text('age', $patient->age, array('class' => 'form-control col-sm-4 text-line', 'name'=>'age')) !!}
							<select style="width: 20%;" name="age_units" id="id_age_units" class="form-control input-sm">
								<option value="Year(s)">Years</option>
								<option value="Month(s)">Months</option>
								<option value="Day(s)">Days</option>
							</select>
						</div>
					</div><br>

					<div class="form-inline">
						{!! Form::label('patient_village', 'Village:', array('class' =>'col-md-2')) !!}
						{!! Form::text('patient_village', $patient->patient_village, array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('patient_parish', 'Parish:', array('class' =>'col-md-2')) !!}
						{!! Form::text('patient_parish', $patient->patient_parish, array('class' => 'form-control col-sm-4 text-line')) !!}
					</div><br><br>

					<div class="form-inline">

						{!! Form::label('patient_subcounty', 'Sub-County:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_subcounty', $patient->patient_subcounty, array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('patient_district', 'District:', array('class' =>'col-md-2 ')) !!}
						{!! Form::select('patient_district',[""=>""]+$districts,$patient->patient_district,['id'=>'patient_district', 'class'=>'form-control', 'style' =>'width:195px']) !!}
					</div> <br><br>

					<p style="text-align:left; color:#184e7b;"><i>Case Investigation</i></p>

					<div class="form-inline">
						{!! Form::label('symptomatic_onset_date', '1. Date of symptom onset:', array('class' =>'col-md-2')) !!}
						{!! Form::text('symptomatic_onset_date', $patient->symptomatic_onset_date, array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!}
					</div>
					<div class="form-inline">

						{!! Form::label('symptoms', '2. Does the patient have the following signs and symptoms:', array('class' =>'col-md-2')) !!}
						{!! Form::text('symptoms', $patient->symptoms, array('class' => 'form-control col-sm-4')) !!}
					</div><br><br><br><br>

					<p style="text-align:left; color:#184e7b;"><i>In the 14 days before symptom onset, did the patient:</i></p>

					<div class="panel panel-default">
						<div class="panel-body">

							<div class="form-inline">
								{!! Form::label('TravelToChina', '3. Has the patient spend time in China or any other country affected by 2019-nCoV?', array('class' => 'col-md-7')) !!}
								<select style="width: 20%;" name="TravelToChina" id="TravelToChina">
									<option value="" selected="selected">Select...</option>
									<option value="1" {!! $patient->TravelToChina == "1" ? 'selected' : ''!!}>Yes</option>
									<option value="0" {!! $patient->TravelToChina == "0" ? 'selected' : ''!!}>No</option>
									<option value="3" {!! $patient->TravelToChina == "3" ? 'selected' : ''!!}>Unknown</option>

								</select>
							</div> <br>

							<div class="form-inline" id="chinaTravel">
								{!! Form::label('travelDateToChina', 'i) Date 	traveled to China or other country affected by 2019-nCoV?', array('class' =>'col-md-7')) !!}
								{!! Form::text('travelDateToChina', $patient->ravelDateToChina, array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!} <br><br>

								{!! Form::label('travelDateFromChina', 'ii) Date 	traveled from China or other country affected by 2019-nCoV?', array('class' =>'col-md-7')) !!}
								{!! Form::text('travelDateFromChina', $patient->travelDateFromChina, array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!} <br><br>

								{!! Form::label('stateVisited', 'State / Country / City visited', array('class' =>'col-md-7')) !!}
								{!! Form::text('stateVisited', $patient->stateVisited, array('class' => 'form-control col-sm-4  text-line')) !!} <br><br>

								{!! Form::label('UgArrivalDate', 'iii) Date Arrived in Uganda', array('class' =>'col-md-7')) !!}
								{!! Form::text('UgArrivalDate', $patient->UgArrivalDate, array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!}
							</div>
						</div>

						<div class="form-inline">
							{!! Form::label('closeContact4', '4. Has the patient been in close contact with a person who is under investigation for 2019-nCoV while that person was ill?', array('class' => 'col-md-7')) !!}

							<div class="form-inline">
								<select style="width: 20%;" name="closeContact4">
									<option value="" selected="selected">Select...</option>
									<option value="1" {!! $patient->closeContact4 == "1" ? 'selected' : '' !!}>Yes</option>
									<option value="0" {!! $patient->closeContact4 == "0" ? 'selected' : '' !!}>No</option>
									<option value="3" {!! $patient->closeContact4 == "3" ? 'selected' : '' !!}>Unknown</option>
								</select>
							</div>
						</div> <br>

						<div class="form-inline">
							{!! Form::label('closeContact5', '5. Has the patient been in close contact with a laboratory-confirmed 2019-nCoV case while the case was ill?', array('class' => 'col-md-7')) !!}

							<select style="width: 20%;" name="closeContact5">
								<option value="" selected="selected">Select...</option>
								<option value="1" {!! $patient->closeContact5 == "1" ? 'selected' : '' !!}>Yes</option>
								<option value="0" {!! $patient->closeContact5 == "0" ? 'selected' : '' !!}>No</option>
								<option value="3" {!! $patient->closeContact5 == "3" ? 'selected' : '' !!}>Unknown</option>
							</select>
						</div> <br>
					</div>

				<p style="text-align:left; color:#184e7b;"><i>Additional Patient Information:</i></p>

				<div class="form-inline">
					{!! Form::label('is_health_care_worker_being_tested', '6. Is the patient a health care worker?', array('class' => 'col-md-7')) !!}

					<select style="width: 36%;" name="is_health_care_worker_being_tested">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->is_health_care_worker_being_tested == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->is_health_care_worker_being_tested == "0" ? 'selected' : '' !!}>No</option>
						<option value="3" {!! $patient->is_health_care_worker_being_tested == "3" ? 'selected' : '' !!}>Unknown</option>
					</select>
				</div><br>

				<div class="form-inline">
					{!! Form::label('healthFacilityHistory', '7. History of being in a health facility(as a patient,worker,or visiter) where cases of 2019-nCoV have been reported?', array('class' => 'col-md-7')) !!}

					<select style="width: 36%;" name="healthFacilityHistory">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->healthFacilityHistory == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->healthFacilityHistory == "0" ? 'selected' : '' !!}>No</option>
						<option value="3" {!! $patient->healthFacilityHistory == "3" ? 'selected' : '' !!}>Unknown</option>
					</select>
				</div><br>

				<div class="form-inline">
					{!! Form::label('acuteRespiratory', '8. Is patient part of a cluster of patients with severe acute respiratory illness (eg. fever & pneumonia requiring hospitalization) of unknown etiology in which nCoV is being evaluated?', array('class' => 'col-md-7')) !!}

					<select style="width: 36%;" name="acuteRespiratory">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->acuteRespiratory == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->acuteRespiratory == "0" ? 'selected' : '' !!}>No</option>
						<option value="3" {!! $patient->acuteRespiratory == "3" ? 'selected' : '' !!}>Unknown</option>
					</select>
				</div><br>

				<div class="form-inline">
					{!! Form::label('AdditionalSigns', '9. Does the patient have these additional signs and symptoms:', array('class' => 'col-md-7')) !!}
					{!! Form::text('additionalSigns', $patient->additionalSigns, array('class' => 'form-control col-sm-4', 'style' => 'width:400px')) !!}
					<br>
				</div><br><br>

				<div class="form-inline">
					{!! Form::label('diagnosis', '10. Diagnosis (select all that apply):', array('class' => 'col-md-7')) !!}
					{!! Form::textarea('diagnosis', $patient->diagnosis, array('class' => 'form-control col-sm-4','style' => 'width:250px; height:100px')) !!}
				</div><br><br>

				<div class="form-inline">
					{!! Form::label('comorbid', '11. Comorbid conditions:', array('class' => 'col-md-7')) !!}
					{!! Form::textarea('comorbid', $patient->comorbid, array('class' => 'form-control col-sm-4','style' => 'width:250px; height:100px')) !!}
					<br>
				</div><br>

				<div class="form-inline">
					{!! Form::label('patientHospitalized', '12. Is / was the patient hospitalized:', array('class' => 'col-md-3')) !!}

					<select style="width: 20%;" name="admitted">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->patientHospitalized == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->patientHospitalized == "2" ? 'selected' : '' !!}>No</option>
					</select>

					<input type="text" name="admissionDate" id="admissionDate" class="form-control input-sm standard-datepicker-nofuture" size="30" placeholder="Admission date....">
				</div><br>

				<div class="form-inline">
					{!! Form::label('icuAdmitted', '13. Admitted to ICU:', array('class' => 'col-md-3')) !!}
					<select style="width: 20%;"  name="icuAdmitted">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->icuAdmitted == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->icuAdmitted == "0" ? 'selected' : '' !!}>No</option>
					</select>
				</div><br>

				<div class="form-inline">
					{!! Form::label('intubated', '14. Intubated:', array('class' => 'col-md-3')) !!}
					<select style="width: 20%;" name="intubated">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->intubated == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->intubated == "0" ? 'selected' : '' !!}>No</option>
					</select>
				</div><br>

				<div class="form-inline">
					{!! Form::label('ecmo', '15. on ECMO?:', array('class' => 'col-md-3')) !!}
					<select style="width: 20%;" name="ecmo">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->ecmo == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->ecmo == "0" ? 'selected' : '' !!}>No</option>
					</select>
				</div><br>

				<div class="form-inline">
					{!! Form::label('patientDied', '16. Patient died?:', array('class' => 'col-md-3')) !!}
					<select style="width: 20%;" name="patientDied">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->patientDied == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->patientDied == "0" ? 'selected' : '' !!}>No</option>
					</select>
					<input type="text" name="deathDate" id="deathDate" class="form-control input-sm standard-datepicker-nofuture" size="30" placeholder="If dead, date of death...." >
					<!-- {!! Form::text('deathDate', $patient->deathDate, array('class' => 'form-control col-sm-4 standard-datepicker-nofuture','style' => 'width:400px')) !!} -->
				</div><br>

				<div class="form-inline">
					{!! Form::label('otherEtiology', '18. Does the patient have another diagnosis / etiology for their respiratory illness?:', array('class' => 'col-md-3')) !!}
					<select style="width: 20%;" name="otherEtiology">
						<option value="" selected="selected">Select...</option>
						<option value="1" {!! $patient->otherEtiology == "1" ? 'selected' : '' !!}>Yes</option>
						<option value="0" {!! $patient->otherEtiology == "0" ? 'selected' : '' !!}>No</option>
						<option value="3" {!! $patient->otherEtiology == "3" ? 'selected' : '' !!}>Unknown</option>
					</select>
					<input type="text" name="otherEti" id="otherEti" class="form-control input-sm-5" size="30" placeholder="specify other diagnosis/etiology....">
				</div><br><br>

			<div class="panel panel-primary">
				<div class="panel-heading "><p><i>Laboratory specimen for 2019-nCoV diagnosis</i></p></div>
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
								{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('Update'),
								array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		//Date picker
		$('#datepicker').datepicker({
			autoclose: true
		});

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

	<!--
	for the yes,no,unknown drop down values, these are the corresponding values.
	1: Yes, 0: No, 3: Unknown

	Ps: these are times you wish you were duck :D
-->
