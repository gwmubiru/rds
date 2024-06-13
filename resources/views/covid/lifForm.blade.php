@extends('layouts/layout')
@section('content')

<ul class="breadcrumb">
	<li><a href="/">HOME</a></li>
	<li><a href="/cif/covid/form">CIF Form</a></li>
	<li><a href="/poe/covid/form">POE Form</a></li>
</ul>

<div id='d3' class="panel panel-default">
	<div class="panel-body">
		@if ($message = Session::get('msge'))
	<div class="alert alert-success alert-block">
		<button type="button" class="close" data-dismiss="alert">×</button>
		{!! Session::get('msge') !!}
	</div>

	@endif
	{!! Form::open(array('url'=>'/save/covid/form','id'=>'form_id')) !!}
	{!! Form::text('formType', "lif", array('class' => 'form-control col-sm-4 hidden' )) !!}

		@if(count($errors))

		<div class="alert alert-danger">
		<strong>Hey!</strong> There were some problems with your input.
			<br/>
			<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
@endif

	<h3 class="panel-title" style="text-align:center"><strong>Lab Investigation Form for Suspected COVID-19 Cases	</strong></h3>
		<br>
		<div class="panel panel-default">

			<div class="row">
				<div class="panel-body">
					<div class="col-md-4">

						<div class="form-group {{ $errors->has('request_date') ? 'has-error' : '' }}">
							{!! Form::label('request_date', 'Date:', array('class' =>'col-md-12')) !!}
							{!! Form::text('request_date', old('request_date'), array('class' => 'form-control col-sm-4 standard-datepicker-nofuture ','id'=>'request_date', 'required'=>1)) !!}<br><br><br>

						<p class="text-danger">{{ $errors->first('request_date') }}</p>

				</div>
					</div>


				<div class="col-md-4">
					<div class="form-group {{ $errors->has('ulin') ? 'has-error' : '' }}">
							@if(Auth::user()->ref_lab == 2891)
							{!! Form::label('ulin', 'Locator ID:', array('class' =>'col-md-12  ')) !!}
							{!! Form::select('ulin',[""=>""]+$locator_ids,'',['id'=>'lab_number', 'class'=>'form-control', 'style'=>"width:250px"]) !!}

							@else
							{!! Form::label('ulin', 'Unique Lab No:', array('class' =>'col-md-12  ')) !!}
							{!! Form::text('ulin', old('ulin'), array('class' => 'form-control col-sm-4','id'=>'ulin' )) !!}<br><br><br>
							@endif
							<span class="text-danger">{{ $errors->first('ulin') }}</span>
						</div>
				</div>


						<div class="col-md-4">
						<div class="form-group {{ $errors->has('serial_number') ? 'has-error' : '' }}">

							@if(Auth::user()->ref_lab == 2891)
							{!! Form::label('serial_number', 'Barcode/Form Number:', array('class' =>'col-md-12 ')) !!}

							@else
							{!! Form::label('serial_number', 'Serial Number:', array('class' =>'col-md-12 ')) !!}
							@endif

							{!! Form::text('serial_number', old('serial_number'), array('class' => 'form-control col-sm-4','id'=>'serial_number')) !!}<br><br><br>
							<span class="text-danger">{{ $errors->first('serial_number') }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>


		



<div class="panel panel-primary">
			<div class="panel-heading "><strong>Interviewer Info</strong></div>

			<div class="panel-body">
				<div class="col-md-4">
					<div class="form-group {{ $errors->has('interviewer_name') ? 'has-error' : '' }}">
						{!! Form::label('interviewer_name', 'Interviewer Name:', array('class' =>'col-md-12 ')) !!}
						@if(App\Closet\MyHTML::is_site_of_collection_user() || App\Closet\MyHTML::is_site_of_collection_editor() || App\Closet\MyHTML::is_rdt_site_user() || App\Closet\MyHTML::is_facility_dlfp_user())
							{!! Form::text('interviewer_name', Auth::user()->family_name, array('class' => 'form-control col-sm-4','id'=>'interviewer_name', 'required'=>1)) !!}<br><br><br>
						@else
							{!! Form::text('interviewer_name', old('interviewer_name'), array('class' => 'form-control col-sm-4','id'=>'interviewer_name', 'required'=>1)) !!}<br><br><br>
						@endif
						
						<p class="text-danger">{{ $errors->first('interviewer_name') }}</p>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group {{ $errors->has('interviewer_facility') ? 'has-error' : '' }}">
						{!! Form::label('interviewer_facility', 'Interviewer Facility:', array('class' =>'col-md-12 ')) !!}
						
						@if(App\Closet\MyHTML::is_site_of_collection_user() || App\Closet\MyHTML::is_site_of_collection_editor() || App\Closet\MyHTML::is_rdt_site_user() || App\Closet\MyHTML::is_facility_dlfp_user())
							{!! Form::text('interviewer_facility', App\Closet\MyHTML::getUserSiteOfCollection()['facility_name'], array('class' => 'form-control col-sm-4','id'=>'interviewer_facility', 'required'=>1)) !!}<br><br><br>
						@else
							{!! Form::text('interviewer_facility', old('interviewer_facility'), array('class' => 'form-control col-sm-4','id'=>'interviewer_facility', 'required'=>1)) !!}<br><br><br>
						@endif
						
						<p class="text-danger">{{ $errors->first('interviewer_facility') }}</p>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group {{ $errors->has('interviewer_phone') ? 'has-error' : '' }}">
					{!! Form::label('interviewer_phone', 'Interviewer Phone:', array('class' =>'col-md-12 ')) !!}
						@if(App\Closet\MyHTML::is_site_of_collection_user() || App\Closet\MyHTML::is_site_of_collection_editor() || App\Closet\MyHTML::is_rdt_site_user() || App\Closet\MyHTML::is_facility_dlfp_user())
						{!! Form::text('interviewer_phone', Auth::user()->telephone, array('class' => 'form-control col-sm-4','id'=>'interviewer_phone', 'required'=>1)) !!}<br><br><br>
						@else
						{!! Form::text('interviewer_phone', old('interviewer_phone'), array('class' => 'form-control col-sm-4','id'=>'interviewer_phone', 'required'=>1)) !!}<br><br><br>
						@endif
						
						<p class="text-danger">{{ $errors->first('interviewer_phone') }}</p>
					</div>
				</div>

				<div class="row">

					<div class="panel-body">
						
						@if(App\Closet\MyHTML::is_site_of_collection_user() || App\Closet\MyHTML::is_site_of_collection_editor() || App\Closet\MyHTML::is_rdt_site_user() || App\Closet\MyHTML::is_facility_dlfp_user())
						  {!! Form::hidden('where_sample_collected_from', 'HEALTH FACILITY') !!}		
						  {!! Form::hidden('facility', App\Closet\MyHTML::getUserSiteOfCollection()['facility_name']) !!}
						@else
	  				<div class="col-md-12">  
	  					<div class="form-inline {{ $errors->has('where_sample_collected_from') ? 'has-error' : '' }}">
	  						{!! Form::label('where_sample_collected_from', 'Sample Collection Point', array('class', 'id'=>'where_sample_collected_from')) !!}<br>
	  						<label for="chkYes"><input type="radio" id="hf" name="where_sample_collected_from" value="HEALTH FACILITY" onclick="ShowHideDiv()" old('request_date')/> Health Facility</label>
	  						<label for="poe"><input type="radio" id="poe" name="where_sample_collected_from" value="POE" onclick="ShowHideDiv()" /> Point Of Entry</label>
	  						<label for="inst"><input type="radio" id="inst" name="where_sample_collected_from" value="QURANTINE" onclick="ShowHideDiv()" /> Quarantine</label>
	  						<label for="other"><input type="radio" id="other" name="where_sample_collected_from" value="OTHER" onclick="ShowHideDiv()" /> Other</label>
	  						<p class="text-danger">{{ $errors->first('where_sample_collected_from') }}</p>
	  					</div>

	  					<div class="form-group {{ $errors->has('facility') ? 'has-error' : '' }}">
	  						<div id="dvHF" style="display: none">
	  							Select Facility:
	  							{!! Form::select('facility',[""=>""]+$facilities,'',['id'=>'facilityWhere_sample_collected_from', 'class'=>'form-control', 'style'=>"width:250px"]) !!}
	  						</div>
	  					</div>
	  				</div>
	  				@endif
	  				<p class="text-danger">{{ $errors->first('facility') }}</p>

	  				<div class="form-group {{ $errors->has('poe') ? 'has-error' : '' }}">
	  					<div id="dvPoe" style="display: none">
	  						Boarder / PoE:
	  						{!! Form::select('poe',[""=>""]+$poe,old('nameWhere_sample_collected_from'),['id'=>'poeWhere_sample_collected_from', 'class'=>'form-control', 'style'=>"width:250px"]) !!}
	  					</div>
	  				</div>
	  				<p class="text-danger">{{ $errors->first('poe') }}</p>

	  				<div class="col-md-4">

	  					<div class="form-group {{ $errors->has('quarantine') ? 'has-error' : '' }}">
	  						<div id="dvInst" style="display: none">
	  							Name of Qurantine center:
	  							{!! Form::text('quarantine', old('quarantine'), array('class' => 'form-control')) !!}


	  							District:
	  							{!! Form::select('q_swabing_district',[""=>"select swabing district..."]+$districts,'',['id'=>'q_swabing_district', 'class'=>'form-control', 'style' =>'width:100%']) !!}

	  						</div>

	  					</div>

	  					<p class="text-danger">{{ $errors->first('quarantine') }}</p>

	  					<div class="form-group {{ $errors->has('other') ? 'has-error' : '' }}">
	  						<div id="dvOther" style="display: none">
	  							Other Collection Point:
	  							{!! Form::text('other', old('nameWhere_sample_collected_from'), array('class' => 'form-control')) !!}

	  							District:
	  							{!! Form::select('swabing_district',[""=>"select swabing district..."]+$districts,'',['id'=>'swabing_district', 'class'=>'form-control', 'style' =>'width:100%']) !!}
	  						</div>
	  					</div>
	  					<p class="text-danger">{{ $errors->first('other') }}</p>

	  				</div>
	  			</div>






			<div class="col-md-4">
				<div class="form-group">
				{!! Form::label('who_being_tested', '2. Who is being tested?:', array('class' =>'col-md-12')) !!}
					<select class="form-control col-sm-4" name="who_being_tested" id="who_being_tested">
						<option value="Case" {{ old('who_being_tested') == 'Case'? 'selected' : '' }}>Case</option>
						<option value="Contact" {{ old('who_being_tested') == 'Contact'? 'selected' : '' }}>Contact</option>
						<option value="Traveler" {{ old('who_being_tested') == 'Traveler'? 'selected' : '' }}>Traveler</option>
						<option value="Quarantine" {{ old('who_being_tested') == 'Quarantine'? 'selected' : '' }}>Quarantine</option>
						<option value="Alert" {{ old('who_being_tested') == 'Alert'? 'selected' : '' }}>Alert</option>
						<option value="Health Worker" {{ old('who_being_tested') == 'Health Worker'? 'selected' : '' }}>Health Worker</option>
						<option value="Other" {{ old('who_being_tested') == 'Other'? 'selected' : '' }}>Other</option>
					</select>
				</div>
				<div class="col-md-12">
				<div class="form-group" name="hwt" id="hwt" style="display:none;">
					{!! Form::label('facility_name', 'Health Worker&#39;s Facility:', array('class' =>'col-md-12')) !!}
							{!! Form::select('health_care_worker_facility',[""=>"select health worker&#39;s facility"]+$facilities,old('health_care_worker_facility'),['id'=>'health_care_worker_facility', 'class'=>'form-control', 'style' => 'width:100%']) !!}
						</div>
				<div class="form-group" name="traveler" id="traveler" style="display:none; color:red;">
					{!! Form::label('receipt_number', 'Receipt Number:', array('class' =>'col-md-12')) !!}
							{!! Form::text('receipt_number',old('receipt_number'),['class'=>'form-control', 'style' => 'width:100%']) !!}
						</div>

			<div class="form-inline" name="hwt" id="hwt" style="display:none;">
					{!! Form::select('health_care_worker_facility',[""=>"select health worker&#39;s facility"]+$facilities,'',['id'=>'health_care_worker_facility', 'class'=>'form-control', 'style' => 'width:100%']) !!}
				</div>
		</div>
		</div>


			<div class="col-md-4">
			<div class="form-group">
					{!! Form::label('reason_for_healthWorker_testing', '3. Reason for Health Worker testing:', array('class' =>'col-md-12 ')) !!}
				<select class="form-control col-sm-4 select-field" name="reason_for_healthWorker_testing" onchange="if (this.value=='Other'){this.form['reason_for_healthWorker_testingOther'].style.visibility='visible'}else {this.form['reason_for_healthWorker_testingOther'].style.visibility='hidden'};">
					<option value="" selected="selected">Select...</option>
						<option value="Routine exposure">Routine exposure</option>
						<option value="Quarantine">Quarantine</option>
					<option value="Other">Other</option>
					</select>
				</div>

				<div class="form-inline">
					<input type="text" name="reason_for_healthWorker_testingOther" id="reason_for_healthWorker_testingOther" class="form-control input-sm" size="30" placeholder="Other reason for testing..." style="visibility:hidden;"></input>
				</div>
			</div>


		<div class="col-md-4">
			<div class="form-group">
					{!! Form::label('isolatedPerson_test_day', '4. Person isolated/quarantined? Day of testing', array('class' =>'')) !!}
					<select class="form-control col-sm-4 select-field" name="isolatedPerson_test_day" onchange="if (this.value=='Other'){this.form['isolatedPerson_test_dayOther'].style.visibility='visible'}else {this.form['isolatedPerson_test_dayOther'].style.visibility='hidden'};">
						<option value="" selected="selected"></option>
						<option value="Day 0">Day 0</option>
						<option value="Day 7">Day 7</option>
						<option value="Day 13">Day 13</option>
					<option value="Other">Other</option>
				</select>
				</div>
				<div class="form-inline">
					<input type="text" name="isolatedPerson_test_dayOther" id="isolatedPerson_test_dayOther" class="form-control input-sm" size="30" placeholder="Other specific testing day..." style="visibility:hidden;"></input>
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('travel_out_of_ug_b4_onset', '5. Patient traveled out of Uganda in 2wks before onset (or sample-taking, if no symptoms)?', array('class' =>'col-md-12 ')) !!}
					<select class="form-control col-sm-4 select-field" name="travel_out_of_ug_b4_onset" id="travel_out_of_ug_b4_onset">
						<option value="" selected="selected">Select...</option>
						<option value="1">Yes</option>
					<option value="0">No</option>
			</select>
			</div>
			</div>

			<div class="col-md-4">
				<div class="form-inline"  name="onset" id="onset" style="display:none;">
					{!! Form::label('destination_b4_onset', '6. If Yes, where?', array('class' =>'col-md-12  ')) !!}
					{!! Form::text('destination_b4_onset', old('destination_b4_onset'), array('class' => 'form-control col-sm-12')) !!}

					{!! Form::label('return_date', '7. Return Date:', array('class' =>'col-md-12  ')) !!}
					{!! Form::text('return_date', old('return_date'), array('class' => 'form-control col-sm-4 standard-datepicker-nofuture')) !!}

				</div>
		</div>

		</div>
	</div>
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading "><strong>Section 1: Patient Information</strong></div>

		<div class="panel-body">
		<div class="col-md-4">
			<div class="form-group {{ $errors->has('patient_surname') ? 'has-error' : '' }}">
					{!! Form::label('patient_surname', '1. Fullname:', array('class' =>'col-md-12 ')) !!}
					{!! Form::text('patient_surname', old('patient_surname'), array('class' => 'form-control col-sm-4')) !!}

				<p class="text-danger">{{ $errors->first('patient_surname') }}</p>

			</div>
	</div>
			<div class="col-md-4">
				<div class="form-group {{ $errors->has('pa') ? 'has-error' : '' }}">
					{!! Form::label('passportNo', '2. Passport # / NIN:', array('class' =>'col-md-12 ')) !!}
					{!! Form::text('passportNo', old('passportNo'), array('class' => 'form-control col-sm-4')) !!}

				<p class="text-danger">{{ $errors->first('pas') }}</p>
				</div>
			</div>

	<div class="col-md-4">

			<div class="form-group {{ $errors->has('qas') ? 'has-error' : '' }}">
					{!! Form::label('sex', '3. Sex:', array('class' =>'col-md-12 ')) !!}
					<select class="form-control col-sm-4 select-field" name="sex">
					<option value="" selected="selected">Select...</option>
						<option value="Male" {{ old('sex') == 'Male'? 'selected' : '' }}>Male</option>
						<option value="Female" {{ old('sex') == 'Female'? 'selected' : '' }}>Female</option>
				</select>
					<p class="text-danger">{{ $errors->first('a') }}</p>
				</div>
			</div>
	</div>

		<div class="panel-body">

			<div class="col-md-4">
				{!! Form::label('dob', '4. Date of birth:', array('class' =>'col-md-12 ')) !!}
				{!! Form::text('dob', old('dob'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture ')) !!}
			<p class="text-danger">{{ $errors->first('') }}</p>
			</div>

		<div class="col-md-4">
				<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">

				<label for="age">Or Estimated Age</label>

		<div class="input-group">
			      <input type="text" class="form-control" name="age" id="age" aria-describedby="basic-addon2">

		      <span class="input-group-addon" id="basic-addon2" style="width: 40%;padding: 0 4px"><select style="width: 100%; height: 26px;"  name="age_units" id="id_age_units" class=" form-control input-sm">
					<option value="Y">Years</option>
						<option value="M">Months</option>
						<option value="D">Days</option>

			</select></span>
		    </div>

				<p class="text-danger">{{ $errors->first('') }}</p>
			</div>
			</div>

		<div class="col-md-4">
				<div class="form-group {{ $errors->has('origin') ? 'has-error' : '' }}">

				{!! Form::label('nationality', '5. Nationality:', array('class' =>'col-md-12	')) !!}
					<label for="Ug"><input type="radio" id="ug" name="origin" value="UGANDAN" onclick="ShowHideDiv()" /> Ugandan</label>
					<label for="noUg"><input type="radio" id="noug" name="origin" value="NON-UGANDAN" onclick="ShowHideDiv()" /> Non-Ugandan</label>
				<label for="noUg"><input type="radio" id="noug" name="origin" value="LEFT BLANK" onclick="ShowHideDiv()" /> Left blank</label>
					<p class="text-danger">{{ $errors->first('nationality') }}</p>

				<div id="dvUg" style="display: none">

					{!! Form::text('patient_village', old('patient_village'), array('class' => 'form-control col-sm-4', 'placeholder' => 'Patient&#39;s village')) !!}<br><br>
					{!! Form::text('patient_subcounty', old('patient_subcounty'), array('class' => 'form-control col-sm-4','placeholder' => 'Patient&#39;s sub-county')) !!}<br><br>


				{!! Form::text('patient_parish', old('patient_parish'), array('class' => 'form-control col-sm-4','placeholder' => 'Patient&#39;s parish')) !!}<br><br>
					{!! Form::select('patient_district',[""=>"select home district..."]+$districts,'',['id'=>'patient_district', 'class'=>'form-control', 'style' =>'width:100%']) !!}
					</div>


				<div id="dvNoug" style="display: none">
						Select patient's nationality:
					{!! Form::select('foreign_nationality',[""=>""]+$nationality,'',['id'=>'foreign_nationality', 'class'=>'form-control', 'style'=>"width:340px"]) !!}

						Foreign district:
						{!! Form::text('foreignDistrict', old('foreignDistrict'), array('class' => 'form-control col-sm-4')) !!}
					</div>
				</div>
			</div>



<div class="panel-body">

		<div class="col-md-4">
			<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
				{!! Form::label('patient_contact', '6. Patient Phone No:', array('class' =>'col-md-12 ')) !!}

			{!! Form::text('patient_contact', old('patient_contact'), array('class' => 'form-control col-sm-12')) !!}
				<p class="text-danger">{{ $errors->first('') }}</p>
			</div>
		</div>

		<!-- <div class="col-md-4">

	<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
				{!! Form::label('patient_village', '7. Patient Village:', array('class' =>'col-md-12')) !!}
				{!! Form::text('patient_village', old('patient_village'), array('class' => 'form-control col-sm-4')) !!}
				<p class="text-danger">{{ $errors->first('') }}</p>
		</div>
		</div>

	<div class="col-md-4">
			<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
				{!! Form::label('patient_subcounty', 'Sub-County:', array('class' =>'col-md-12 ')) !!}
				{!! Form::text('patient_subcounty', old('patient_subcounty'), array('class' => 'form-control col-sm-4')) !!}
				<p class="text-danger">{{ $errors->first('') }}</p>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
		{!! Form::label('patient_parish', 'Parish:', array('class' =>'col-md-2')) !!}
			{!! Form::text('patient_parish', old('patient_parish'), array('class' => 'form-control col-sm-4')) !!}
			<p class="text-danger">{{ $errors->first('') }}</p>
		</div>
		</div> -->


	<div class="col-md-4">
			<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
		{!! Form::label('patient_NOK', '8. Next of Kin:', array('class' =>'col-md-12')) !!}
 		{!! Form::text('patient_NOK', old('patient_NOK'), array('class' => 'form-control col-sm-4')) !!}

<p class="text-danger">{{ $errors->first('') }}</p>
		</div>
		</div>

		<div class="col-md-4">
			<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
						{!! Form::label('nok_contact', '9. Next Of Kin Phone:', array('class' =>'col-md-12')) !!}
							{!! Form::text('nok_contact', old('nok_contact'), array('class' => 'form-control col-sm-4')) !!}
		<p class="text-danger">{{ $errors->first('') }}</p>
		</div>
		</div>

</div>
</div>
</div>


<div class="panel panel-primary">
	<div class="panel-heading "><strong>Section 2: Clinical Information</strong></div>
	<div class="panel-body">
		<div class="form-inline">
			{!! Form::label('patient_symptomatic', '10. Is / Was patient symptomatic?', array('class' => 'col-md-3')) !!}
			<div class="radio-inline">{!! Form::radio('patient_symptomatic', 'Yes', false) !!} <span class="input-tag">Yes</span></div>
			<div class="radio-inline">{!! Form::radio('patient_symptomatic', 'No', false) !!} <span class="input-tag">No</span></div>
		</div> <br>
		<div class="form-inline">

		{!! Form::label('symptomatic_onset_date', '11. Date Of onset of first symptom:', array('class' =>'col-md-3')) !!}
		{!! Form::text('symptomatic_onset_date', old('symptomatic_onset_date'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture')) !!}

			{!! Form::label('symptoms', '12. Symptoms:', array('class' =>'col-md-2')) !!}


		<select style="width: 18%;" name="symptoms[]" id= "symp" multiple>
				<option value="Cough">Cough</option>
			<option value="Fever">Fever</option>
				<option value="Sore Throat">Sore Throat</option>
				<option value="Shortness of breath">Shortness of breath</option>
				<option value="Headache">Headache</option>
				<option value="Chest Pain">Chest Pain</option>
				<option value="Runny Nose">Runny Nose</option>
				<option value="General Weakness">General Weakness</option>
				<option value="Chills">Chills</option>
			</select>
			<input type="text" name="otherSymp" id="otherSymp" class="form-control input-sm" size="30" placeholder="Other symptoms...">
	</div><br>

	<div class="form-inline">
			{!! Form::label('known_underlying_condition', '13. Known underlying conditions:', array('class' =>'col-md-3')) !!}

			<select class="form-control col-sm-4" name="known_underlying_condition[]" id="knownUnderlying" multiple>
				<option value="Pregnancy">Pregnancy</option>
				<option value="Post-partum">Post-partum</option>
				<option value="TB">TB</option>
			<option value="HIV">HIV</option>
				<option value="Neurological disease">Neurological disease</option>
				<option value="Cardiovascular disease">Cardiovascular disease</option>
		<option value="Hypertension">Hypertension</option>
			<option value="Diabetes">Diabetes</option>
			<option value="Renal disease">Renal disease</option>
				<option value="Chronic Lung disease">Chronic Lung disease</option>
				<option value="Liver disease">Liver disease</option>
				<option value="Malignancy">Malignancy</option>
			</select>

			<input type="text" name="otherUnderlyingCondition" id="otherUnderlyingCondition" class="form-control input-sm" size="30" placeholder="Other known underlying conditions...">
		</div>

</div>
</div>


<div class="panel panel-primary">
	<div class="panel-heading "><strong>Section 3: Specimen Colection</strong></div>
@if(Auth::user()->ref_lab != 55)
<div class="panel-body">
	<div class="form-inline">

			{!! Form::label('sampletype', 'Sample Type:', array('class' => 'col-md-2')) !!}
			<select style="width: 18%;" name ="specimen_type[]" id="specimen_type" multiple>
			<option value="Nasopharyngeal swab">NP Swab</option>
					<option value="Oropharyngeal Swab">OP Swab</option>
			</select> <br><br>

				@if(Auth::user()->ref_lab ==2891)

				@else
			{!! Form::label('sp_ulin', 'Sample ID:', array('class' =>'col-md-2 ')) !!}
			{!! Form::text('sp_ulin', old('specimen_ulin'), array('class' => 'form-control col-sm-4 text-line')) !!}
			@endif
			{!! Form::label('sample_collection_date', 'Sample Collection Date:', array('class' =>'col-md-2 ')) !!}
			{!! Form::text('sample_collection_date', old('specimen_collection_date'), array('class' => 'form-control col-sm-4 text-line standard-datepicker-nofuture')) !!}

	</div><br>
	</div><br>

	@elseif(Auth::user()->ref_lab == 55)
	<table class="table table-bordered">
		<tr>
			<th class="text:center">Specimen Type <small></th>
				<th class="text:center">Specimen ID</th>
				<th class="text:center">Date Collected</th>
				<th class="text:center">Test Result</th>
				<th class="text:center">Test Type</th>
				<th class="text:center">Test Date</th>
				<th class="text:center">Tested By</th>
				<!-- <th class="text:center">Test Site</th> -->
			</tr>
			<tr>
				<td><select style="width: 100%;" name ="specimen_type[]" id="samples" multiple>
				<option value="Nasopharyngeal swab">NP Swab</option>
					<option value="Oropharyngeal Swab">OP Swab</option>
				</select>
			</td>
				<td>{!! Form::text('sp_ulin', old('specimen_ulin'), array('class' => 'form-control col-sm-4 text-line')) !!}</td>
				<td>{!! Form::text('sample_collection_date', old('specimen_collection_date'), array('class' => 'form-control col-sm-4 text-line standard-datepicker-nofuture')) !!}</td>
				<td><select style="width: 100%;" name ="test_result">
					<option value="" selected="selected">Select resut...</option>
						<option value="Negative" {{ old('test_result') == 'Negative'? 'selected' : '' }}>Negative</option>
						<option value="Positive" {{ old('test_result') == 'Positive'? 'selected' : '' }}>Positive</option>
						<option value="Fail" {{ old('test_result') == 'Fail'? 'selected' : '' }}>Fail</option>
				</select>
			</td>
				<td><select style="width: 100%;" name ="test_method">
					<option value="" selected="selected">Test method...</option>
						<option value="RDT" {{ old('test_method') == 'RDT'? 'selected' : '' }}>RDT</option>
							<option value="PCR" {{ old('test_method') == 'PCR'? 'selected' : '' }}>PCR</option>
				</select>
			</td>
			<td>{!! Form::text('test_date', old('test_date'), array('class' => 'form-control col-sm-4 text-line standard-datepicker-nofuture')) !!}</td>
				<td>{!! Form::text('testedBy', old('testedBy'), array('class' => 'form-control col-sm-4 text-line')) !!}</td>
				<!-- <td>{!! Form::text('tested_by', old('tested_by'), array('class' => 'form-control col-sm-4 text-line')) !!}</td> -->
			</tr>
		</table>
@endif
</div><br>

{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('save'),
array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
</div>

</div>
</div>

	@endsection
