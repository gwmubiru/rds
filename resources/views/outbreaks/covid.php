@extends('layouts/layout')
@section('content')

<style>
body {
}
.text-line {
	background-color: transparent;
	color: black;
	outline: dotted;
	outline-style: none;
	outline-offset: 0;
	border-top: none;
	border-left: none;
	border-right: none;
	border-bottom: solid black 2px;
	padding: 3px 30px;
}
</style>

<div id='d3' class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>'save/covid/form','id'=>'form_id')) !!}

		<h3 class="panel-title" style="text-align:center"><strong>Interim 2019 Novel Coronavirus (2019-nCov) Case Investigation Form</strong></h3>
		<p style="text-align:center; color:#184e7b;"><i>If you have questions, contact the Public Health Emergency Operations Center (PHEOC)
			<br> Toll Free line:0800203033, MoH Call center: 08001000066</i></p>
			<br>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="form-inline">
						{!! Form::label('epidNo', 'EPID No:(for UVRI only)', array('class' =>'col-md-3 ')) !!}
						{!! Form::text('epidNo', old('epidNo'), array('class' => 'form-control col-sm-4 text-line')) !!}
					</div>
				</div>

				<div class="panel panel-primary">
					<div class="panel-heading "><strong></strong></div>
					<div class="panel-body">
						<div class="form-inline">
							{!! Form::label('interviewer_name', 'Interviewer Name:', array('class' =>'col-md-2 ')) !!}
							{!! Form::text('interviewer_name', old('interviewer_name'), array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('interviewer_phone', 'Phone:', array('class' =>'col-md-1 ')) !!}
							{!! Form::text('interviewer_phone', old('interviewer_phone'), array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('interviewer_email', 'Email:', array('class' =>'col-md-1 ')) !!}
							{!! Form::text('interviewer_email', old('interviewer_email'), array('class' => 'form-control col-sm-6 text-line')) !!}

						</div> <br><br>

						<div class="form-inline">
							{!! Form::label('interviewer_facility', 'Health Facility:', array('class' =>'col-md-2 ')) !!}
							{!! Form::text('interviewer_facility', old('interviewer_facility'), array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('facility_sub_district', 'sub District:', array('class' =>'col-md-1')) !!}
							{!! Form::text('facility_sub_district', old('facility_sub_district'), array('class' => 'form-control col-sm-4 text-line')) !!}

							{!! Form::label('facility_district', 'District:', array('class' =>'col-md-1 ')) !!}
							{!! Form::text('facility_district', old('facility_district'), array('class' => 'form-control col-sm-4 text-line')) !!}

						</div><br>
					</div>

					<div class="form-inline">

						{!! Form::label('patient_surname', 'Patient Name:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_surname', old('patient_surname'), array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('caseID', 'Case ID:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('caseID', old('caseID'), array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('sex', 'Sex:', array('class' =>'col-md-1 ')) !!}
						<div class="radio-inline">{!! Form::radio('sex', 'Male', false) !!} <span class="input-tag">M</span></div>
						<div class="radio-inline">{!! Form::radio('sex', 'Female', false) !!} <span class="input-tag">F</span></div>
					</div><br>

					<div class="form-inline">
						{!! Form::label('dob', 'DOB:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('dob', old('dob'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!}

						<div class="form-group">
							<label for="age">Or Estimated Age</label>
							<input type="text" name="age" id="age" class="form-control input-sm" size="20">
							<select name="age_units" id="id_age_units" class="form-control input-sm">
								<option value="Y">Years</option>
								<option value="M">Months</option>
								<option value="D">Days</option>
							</select>
						</div>
					</div><br>

					<div class="form-inline">
						{!! Form::label('patient_village', 'Village:', array('class' =>'col-md-2')) !!}
						{!! Form::text('patient_village', old('patient_village'), array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('patient_parish', 'Parish:', array('class' =>'col-md-2')) !!}
						{!! Form::text('patient_parish', old('patient_parish'), array('class' => 'form-control col-sm-4 text-line')) !!}
					</div><br><br>

					<div class="form-inline">

						{!! Form::label('patient_subcounty', 'Sub-County:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_subcounty', old('patient_subcounty'), array('class' => 'form-control col-sm-4 text-line')) !!}

						{!! Form::label('patient_district', 'District:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('patient_district', old('patient_district'), array('class' => 'form-control col-sm-4 text-line')) !!}
					</div> <br><br>

					<p style="text-align:left; color:#184e7b;"><i>Case Investigation</i></p>

					<div class="form-inline">
						{!! Form::label('symptomatic_onset_date', '1. Date of symptom onset:', array('class' =>'col-md-2')) !!}
						{!! Form::text('symptomatic_onset_date', old('symptomatic_onset_date'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!}
					</div>
					<div class="form-inline">

						{!! Form::label('symptoms', '2. Does the patient have the following signs and symptoms:', array('class' =>'col-md-2')) !!}
						<select name="symptoms" onchange="if (this.value=='other Symptoms'){this.form['otherSymp'].style.visibility='visible'}else {this.form['otherSymp'].style.visibility='hidden'};">
							<option value="" selected="selected">Select...</option>
							<option value="Cough">Cough</option>
							<option value="Fever">Fever</option>
							<option value="Sore Throat">Sore Throat</option>
							<option value="Shortness of breath">Shortness of breath</option>
							<option value="Headache">Headache</option>
							<option value="Chest Pain">Chest Pain</option>
							<option value="Runny Nose">Runny Nose</option>
							<option value="General Weakness">General Weakness</option>
							<option value="Chills">Chills</option>
							<option value="other Symptoms">Other</option>
						</select>

						<input type="text" name="otherSymp" id="otherSymp" class="form-control input-sm" size="30" placeholder="Other symptoms..." style="visibility:hidden;">
					</div><br><br>

					<p style="text-align:left; color:#184e7b;"><i>In the 14 days before symptom onset, did the patient:</i></p>

					<div class="panel panel-default">
						<div class="panel-body">

							<div class="form-inline">
								{!! Form::label('TravelToChina', '3. Has the patient spend time in China or any other country affected by 2019-nCoV?', array('class' => 'col-md-7')) !!}
								<select name="TravelToChina" id="TravelToChina">
									<option value="" selected="selected">Select...</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
									<option value="Unkown">Unknown</option>
								</select>
							</div> <br>

							<div class="form-inline" id="chinaTravel" style="display:none;">
								{!! Form::label('travelDateToChina', 'i) Date 	traveled to China or other country affected by 2019-nCoV?', array('class' =>'col-md-6')) !!}
								{!! Form::text('travelDateToChina', old('travelDateToChina'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!}

								{!! Form::label('travelDateFromChina', 'ii) Date 	traveled from China or other country affected by 2019-nCoV?', array('class' =>'col-md-6')) !!}
								{!! Form::text('travelDateFromChina', old('travelDateFromChina'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!}

								{!! Form::label('stateVisited', 'State / Country / City visited', array('class' =>'col-md-6')) !!}
								{!! Form::text('stateVisited', old('stateVisited'), array('class' => 'form-control col-sm-4  text-line')) !!}

								{!! Form::label('UgArrivalDateVisited', 'iii) Date Arrived in Uganda', array('class' =>'col-md-6')) !!}
								{!! Form::text('UgArrivalDateVisited', old('UgArrivalDateVisited'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture text-line')) !!}
							</div><br>

							<div class="form-inline">
								{!! Form::label('closeContact4', '4. Has the patient been in close contact with a person who is under investigation for 2019-nCoV while that person was ill?', array('class' => 'col-md-7')) !!}

								<select name="closeContact4">
									<option value="" selected="selected">Select...</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
									<option value="Unkown">Unknown</option>
								</select>
							</div> <br>

							<div class="form-inline">
								{!! Form::label('closeContact5', '5. Has the patient been in close contact with a laboratory-confirmed 2019-nCoV case while the case was ill?', array('class' => 'col-md-7')) !!}

								<select name="closeContact5">
									<option value="" selected="selected">Select...</option>
									<option value="Yes">Yes</option>
									<option value="No">No</option>
									<option value="Unkown">Unknown</option>
								</select>
							</div> <br>
						</div>
					</div>

					<p style="text-align:left; color:#184e7b;"><i>Additional Patient Information:</i></p>

					<div class="form-inline">
						{!! Form::label('is_health_care_worker_being_tested', '6. Is the patient a health care worker?', array('class' => 'col-md-4')) !!}

						<select name="is_health_care_worker_being_tested">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
							<option value="Unkown">Unknown</option>
						</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('healthFacilityHistory', '7. History of being in a health facility(as a patient,worker,or visiter) where cases of 2019-nCoV have been reported?', array('class' => 'col-md-7')) !!}

						<select name="healthFacilityHistory">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
							<option value="Unkown">Unknown</option>
						</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('acuteRespiratory', '8. Is patient part of a cluster of patients with severe acute respiratory illness (eg. fever & pneumonia requiring hospitalization) of unknown etiology in which nCoV is being evaluated?', array('class' => 'col-md-7')) !!}

						<select name="acuteRespiratory">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
							<option value="Unkown">Unknown</option>
						</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('AdditionalSigns', '9. Does the patient have these additional signs and symptoms:', array('class' => 'col-md-7')) !!}

						<select name="additionalSigns" onchange="if (this.value=='Other'){this.form['otherSign'].style.visibility='visible'}else {this.form['otherSign'].style.visibility='hidden'};">
							<option value="" selected="selected">Select...</option>
							<option value="Chills">Chills</option>
							<option value="Headache">Headache</option>
							<option value="Muscle aches">Muscle aches</option>
							<option value="Vomiting">Vomiting</option>
							<option value="Abdominal pain">Abdominal pain</option>
							<option value="Diarrhea">Diarrhea</option>
							<option value="Runny Nose">Runny Nose</option>
							<option value="Other">Other</option>
						</select>

						<input type="text" name="otherSign" id="otherSign" class="form-control input-sm" size="30" placeholder="Other symptoms..." style="visibility:hidden;">
					</div><br>

					<div class="form-inline">
						{!! Form::label('AdditionalSigns', '10. Diagnosis (select all that apply):', array('class' => 'col-md-3')) !!}

						<select name="additionalSigns">
							<option value="" selected="selected">Select...</option>
							<option value="Pneumonia">Pneumonia</option>
							<option value="Acute respiratory distress syndrome">Headache</option>
							<option value="None">None</option>
						</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('comorbid', '11. Comorbid conditions:', array('class' => 'col-md-3')) !!}

						<select name="comorbid" onchange="if (this.value=='Other'){this.form['otherCombid'].style.visibility='visible'}else {this.form['otherCombid'].style.visibility='hidden'};">
							<option value="" selected="selected">Select...</option>
							<option value="Pregnancy">Pregnancy</option>
							<option value="Diabetes">Diabetes</option>
							<option value="Cardiac disease">Cardiac disease</option>
							<option value="Hypertension">Hypertension</option>
							<option value="Chronic Pulmonary disease">Chronic Pulmonary disease</option>
							<option value="Chronic kidney disease">Chronic kidney disease</option>
							<option value="Chronic Liver disease">Chronic Liver disease</option>
							<option value="Immunocomprised">Immunocomprised</option>
							<option value="None">None</option>
							<option value="Unknown">Unknown</option>
							<option value="Other">Other</option>
						</select>

						<input type="text" name="otherCombid" id="otherCombid" class="form-control input-sm" size="30" placeholder="Other Comorbid..." style="visibility:hidden;">
					</div><br>

					<div class="form-inline">
						{!! Form::label('patientHospitalized', '12. Is / was the patient hospitalized:', array('class' => 'col-md-3')) !!}

						<select name="admitted" onchange="if (this.value=='Yes'){this.form['admissionDate'].style.visibility='visible'}else {this.form['otherCombid'].style.visibility='hidden'};">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>

						<input type="text" name="admissionDate" id="admissionDate" class="form-control input-sm standard-datepicker-nofuture" size="30" placeholder="Admission date...." style="visibility:hidden;">
					</div><br>

					<div class="form-inline">
						{!! Form::label('icuAdmitted', '13. Admitted to ICU:', array('class' => 'col-md-3')) !!}
						<select name="icuAdmitted">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('intubated', '14. Intubated:', array('class' => 'col-md-3')) !!}
						<select name="intubated">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('ecmo', '15. on ECMO?:', array('class' => 'col-md-3')) !!}
						<select name="ecmo">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>
					</div><br>

					<div class="form-inline">
						{!! Form::label('patientDied', '16. Patient died?:', array('class' => 'col-md-3')) !!}
						<select name="patientDied" onchange="if (this.value=='Yes'){this.form['deathDate'].style.visibility='visible'}else {this.form['deathDate'].style.visibility='hidden'};">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>
						<input type="text" name="deathDate" id="deathDate" class="form-control input-sm standard-datepicker-nofuture" size="30" placeholder="If dead, date of death...." style="visibility:hidden;">
					</div><br>

					<div class="form-inline">
						{!! Form::label('otherEtiology', '18. Does the patient have another diagnosis / etiology for their respiratory illness?:', array('class' => 'col-md-7')) !!}
						<select name="otherEtiology" onchange="if (this.value=='Yes'){this.form['otherEti'].style.visibility='visible'}else {this.form['otherEti'].style.visibility='hidden'};">
							<option value="" selected="selected">Select...</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
							<option value="Unknown">Unknown</option>
						</select>
						<input type="text" name="otherEti" id="otherEti" class="form-control input-sm-5" size="30" placeholder="specify other diagnosis/etiology...." style="visibility:hidden;">
					</div><br>
				</div>


				<div class="panel panel-primary">
					<div class="panel-heading "><p><i>Laboratory specimen for 2019-nCoV diagnosis</i></p></div>
					<div class="panel-body">
						<div class="form-inline">

							<table class="table table-bordered" id="item_table">
								<tr>
									<th class="text:center">Specimen Type <small></th>
										<th class="text:center">Specimen ID</th>
										<th class="text:center">Date collected</th>
										<th class="text:center">Sent to UVRI?</th>
										<th><button type="button" name="add" class="btn btn-success btn-sm add"><span class="glyphicon glyphicon-plus"></span></button></th>
									</tr>
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
		</div>

<script>
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
				html += '<td><select name ="specimen_type[]"> <option value=""></option><option value="NP swab">NP Swab</option><option value="Blood">Blood</option><option value="other">Other</option></td>';
					html += '<td><input class="form-control standard-datepicker-nofuture" name="specimen_collection_date[]" type="text" id="specimen_collection_date"></td>';
					html += '<td><input type="text" name="specimen_ulin[]" '+keyup+' class="form-control qty_to_order" id="specimen_ulin'+row_count+'" /></td>';
					html += '<td><input type="text" name="testing_lab[]" class="form-control testing_lab" /></td>';
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

			$(document).ready(function(){
				$('#TravelToChina').on('change', function () {
					if(this.value === "Yes"){
						$("#chinaTravel").show();
					} else {
						$("#chinaTravel").hide();
					}
				});
			});

		</script>
		@endsection

