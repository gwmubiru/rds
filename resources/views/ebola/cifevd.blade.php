@extends('layouts/layout')
@section('content')

<ul class="breadcrumb">
	<li><a href="/">HOME</a></li>
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

	<h3 class="panel-title" style="text-align:center"><strong>Case Investigation Form for Suspected Ebola Cases	</strong></h3>
		<br>
		<div class="panel panel-default">

			<div class="row">
				<div class="panel-body">
					<div class="col-md-4">

						<div class="form-group {{ $errors->has('date_of_case_report') ? 'has-error' : '' }}">
							{!! Form::label('date_of_case_report', 'Date of Case Report:', array('class' =>'col-md-12')) !!}
							{!! Form::text('date_of_case_report', old('date_of_case_report'), array('class' => 'form-control col-sm-4 standard-datepicker-nofuture ','id'=>'date_of_case_report', 'required'=>1)) !!}<br><br><br>

						<p class="text-danger">{{ $errors->first('date_of_case_report') }}</p>

				</div>
					</div>


				<div class="col-md-4">
					<div class="form-group {{ $errors->has('ulin') ? 'has-error' : '' }}">
							{!! Form::label('moh_case_id', 'MoH/UVRI Case ID:', array('class' =>'col-md-12  ')) !!}
							{!! Form::text('moh_case_id', old('moh_case_id'), array('class' => 'form-control col-sm-4','id'=>'moh_case_id' )) !!}

							<span class="text-danger">{{ $errors->first('ulin') }}</span>
						</div>
				</div>

						<div class="col-md-4">
						<div class="form-group {{ $errors->has('form_serial_number') ? 'has-error' : '' }}">

							@if(Auth::user()->ref_lab == 2891)
							{!! Form::label('form_serial_number', 'Barcode/Form Number:', array('class' =>'col-md-12 ')) !!}

							@else
							{!! Form::label('form_serial_number', 'Form Serial Number:', array('class' =>'col-md-12 ')) !!}
							@endif

							{!! Form::text('form_serial_number', old('form_serial_number'), array('class' => 'form-control col-sm-4','id'=>'form_serial_number')) !!}
							<span class="text-danger">{{ $errors->first('form_serial_number') }}</span>
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
							{!! Form::label('patient_surname', 'Patient&#39;s Surname:', array('class' =>'col-md-6 ')) !!}
							{!! Form::text('patient_surname', old('patient_surname'), array('class' => 'form-control col-sm-4')) !!}
						<p class="text-danger">{{ $errors->first('patient_surname') }}</p>

					</div>
			</div>
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('patient_firstname') ? 'has-error' : '' }}">
							{!! Form::label('patient_firstname', 'Other Names:', array('class' =>'col-md-12 ')) !!}
							{!! Form::text('patient_firstname', old('patient_firstname'), array('class' => 'form-control col-sm-4')) !!}

						<p class="text-danger">{{ $errors->first('pas') }}</p>
						</div>
					</div>
<p>
					<div class="col-md-4">
							<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">

							<label for="age">Age</label>

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

					<div class="form-group {{ $errors->has('sex') ? 'has-error' : '' }}">
							{!! Form::label('sex', 'Gender:', array('class' =>'col-md-12 ')) !!}
							<select class="form-control col-sm-4 select-field" name="sex" style="width:100%">
							<option value="" selected="selected">Select...</option>
								<option value="Male" {{ old('sex') == 'Male'? 'selected' : '' }}>Male</option>
								<option value="Female" {{ old('sex') == 'Female'? 'selected' : '' }}>Female</option>
						</select>
							<p class="text-danger">{{ $errors->first('sex') }}</p>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
							{!! Form::label('patient_phone_number', 'Patient Number of Patient/Family Member:', array('class' =>'col-md-12 ')) !!}
							{!! Form::text('patient_phone_number', old('patient_phone_number'), array('class' => 'form-control col-sm-12')) !!}
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
							{!! Form::label('phone_owner', 'Owner of Phone:', array('class' =>'col-md-12 ')) !!}
							{!! Form::text('phone_owner', old('phone_owner'), array('class' => 'form-control col-sm-12')) !!}
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
						{!! Form::label('patient_status', 'Status of Patient at Time of Case Report:', array('class' =>'col-md-12')) !!}
							<select class="form-control col-sm-4" name="patient_status" id="patient_status">
								<option value="">Select Patient Status...</option>
								<option value="Alive" {{ old('patient_status') == 'Alive'? 'selected' : '' }}>Alive</option>
								<option value="Dead" {{ old('patient_status') == 'Dead'? 'selected' : '' }}>Dead</option>
							</select>

							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>
						</div>

						<div class="col-md-4">
						<div class="form-group" name="deathDate" id="deathDate" {{ $errors->has('') ? 'has-error' : '' }}">
							{!! Form::label('deathDate', 'If Dead, Date of Death:', array('class' =>'col-md-12')) !!}
							{!! Form::text('deathDate', old('deathDate'), array('class' => 'form-control col-sm-4 standard-datepicker-nofuture ','id'=>'deathDate', 'required'=>1)) !!}

							<p class="text-danger">{{ $errors->first('') }}</p>
								</div>
								</div>

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('patient_permanent_residence', 'Permanent Residence:', array('class' =>'col-md-12')) !!}
										{!! Form::text('patient_permanent_residence', old('patient_permanent_residence'), array('class' => 'form-control col-sm-4')) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
									</div>

									<div class="col-md-4">
									<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('household_head', 'Head of Household:', array('class' =>'col-md-12')) !!}
										{!! Form::text('household_head', old('household_head'), array('class' => 'form-control col-sm-4')) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
								</div>

									<div class="col-md-4">
									<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('patient_village', 'Village / Town:', array('class' =>'col-md-12')) !!}
										{!! Form::text('patient_village', old('patient_village'), array('class' => 'form-control col-sm-4')) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('patient_parish', 'Parish:', array('class' =>'col-md-2')) !!}
										{!! Form::text('patient_parish', old('patient_parish'), array('class' => 'form-control col-sm-4')) !!}
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
										{!! Form::label('patient_district', 'District:', array('class' =>'col-md-12 ')) !!}
										{!! Form::select('patient_district',[""=>"select district of residence..."]+$districts,'',['id'=>'patient_district', 'class'=>'form-control']) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('country_of_residence', 'Country of Residence:', array('class' =>'col-md-12 ')) !!}
										{!! Form::text('country_of_residence', old('country_of_residence'), array('class' => 'form-control col-sm-4')) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('patient_occupation', 'Occupation:', array('class' =>'col-md-12 ')) !!}
										<select class="form-control col-sm-4 select-field" name="sex" style="width:100%">
										<option value="" selected="selected">Select...</option>
											<option value="Farmer" {{ old('patient_occupation') == 'Farmer'? 'selected' : '' }}>Farmer</option>
											<option value="Butcher" {{ old('patient_occupation') == 'Butcher'? 'selected' : '' }}>Butcher</option>
											<option value="Hunter/trader of game meat" {{ old('patient_occupation') == 'Hunter/trader of game meat'? 'selected' : '' }}>Hunter/trader of game meat</option>
											<option value="Miner" {{ old('patient_occupation') == 'Miner'? 'selected' : '' }}>Miner</option>
											<option value="Religious leader" {{ old('patient_occupation') == 'Religious leader'? 'selected' : '' }}>Religious leader</option>
											<option value="Housewife" {{ old('patient_occupation') == 'Housewife'? 'selected' : '' }}>Housewife</option>
											<option value="Pupil/Student" {{ old('patient_occupation') == 'Pupil/Student'? 'selected' : '' }}>Pupil/Student</option>
											<option value="Child" {{ old('patient_occupation') == 'Child'? 'selected' : '' }}>Child</option>
											<option value="Businessman/woman" {{ old('patient_occupation') == 'Businessman/woman'? 'selected' : '' }}>Businessman/woman</option>
											<option value="Transporter" {{ old('patient_occupation') == 'Transporter'? 'selected' : '' }}>Transporter</option>
											<option value="Healthcare worker" {{ old('patient_occupation') == 'Healthcare worker'? 'selected' : '' }}>Healthcare worker</option>
											<option value="Traditional healer" {{ old('patient_occupation') == 'Traditional healer'? 'selected' : '' }}>Traditional healer</option>
											<option value="Other" {{ old('patient_occupation') == 'Other'? 'selected' : '' }}>Other</option>
									</select>
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group" name="hwt" id="hwt" style="display:none;">
										{!! Form::label('health_worker_facility', 'Healthcare Facility:', array('class' =>'col-md-12')) !!}
												{!! Form::select('health_worker_facility',[""=>"select health worker&#39;s facility"]+$facilities,old('health_worker_facility'),['id'=>'health_worker_facility', 'class'=>'form-control', 'style' => 'width:100%']) !!}
											</div>
										</div>
									</div>

									<div class="panel-body">
									<p><b>Location where patient became ill:</b></p>

									<div class="col-md-4">

										<div class="form-group" {{ $errors->has('') ? 'has-error' : '' }}">
											{!! Form::label('village_where_patient_fell_ill_from', 'Village / Town:', array('class' =>'col-md-12')) !!}
													{!! Form::text('village_where_patient_fell_ill_from',old('village_where_patient_fell_ill_from'),['id'=>'village_where_patient_fell_ill_from', 'class'=>'form-control']) !!}
												</div>
											</div>

									<div class="col-md-4">

										<div class="form-group" {{ $errors->has('') ? 'has-error' : '' }}">
											{!! Form::label('subcounty_where_patient_fell_ill_from', 'Sub-County:', array('class' =>'col-md-12')) !!}
													{!! Form::text('subcounty_where_patient_fell_ill_from',old('subcounty_where_patient_fell_ill_from'),['id'=>'subcounty_where_patient_fell_ill_from', 'class'=>'form-control']) !!}
												</div>
											</div>

									<div class="col-md-4">

										<div class="form-group" {{ $errors->has('') ? 'has-error' : '' }}">
											{!! Form::label('district_where_patient_fell_ill_from', 'District:', array('class' =>'col-md-12')) !!}
													{!! Form::select('district_where_patient_fell_ill_from',['' => 'select district...']+$districts,old('district_where_patient_fell_ill_from'),['id'=>'district_where_patient_fell_ill_from', 'class'=>'form-control']) !!}
												</div>
											</div>

									<div class="col-md-4">

										<div class="form-group" {{ $errors->has('') ? 'has-error' : '' }}">
											{!! Form::label('latitude_gps_coordinates_at_house', 'GPS Coordinates at House: Latitude', array('class' =>'col-md-12')) !!}
													{!! Form::text('latitude_gps_coordinates_at_house',old('latitude_gps_coordinates_at_house'),['id'=>'latitude_gps_coordinates_at_house', 'class'=>'form-control']) !!}
												</div>
											</div>

									<div class="col-md-4">

										<div class="form-group" name="longitude_gps_coordinates_at_house" id="longitude_gps_coordinates_at_house">
											{!! Form::label('longitude_gps_coordinates_at_house', 'GPS Coordinates at House: Longitude', array('class' =>'col-md-12')) !!}
													{!! Form::text('longitude_gps_coordinates_at_house',old('longitude_gps_coordinates_at_house'),['id'=>'longitude_gps_coordinates_at_house', 'class'=>'form-control']) !!}
												</div>
											</div>

									<div class="col-md-4">
										<div class="form-group" name="dates_resid" id="longitude_gps_coordinates_at_house">
											{!! Form::label('', 'If different from Permanent residence ,Dates residing at this location:', array('class' =>'col-md-12')) !!}
											<div class="col-md-6">
											{!! Form::label('resided_at_residence_from', 'From', array('class' =>'col-md-12')) !!}
											{!! Form::text('resided_at_residence_from',old('resided_at_residence_from'),['id'=>'resided_at_residence_from', 'class'=>'form-control standard-datepicker-nofuture']) !!}
												</div>
											<div class="col-md-6">
											{!! Form::label('resided_at_residence_to', 'To', array('class' =>'col-md-12')) !!}
											{!! Form::text('resided_at_residence_to',old('resided_at_residence_to'),['id'=>'resided_at_residence_to', 'class'=>'form-control col-sm-4 standard-datepicker-nofuture']) !!}
												</div>
												</div>
											</div>
								</div>
								</div>

								<div class="panel panel-primary">
									<div class="panel-heading "><strong>Section 2: Clinical Signs and Symptoms</strong></div>
									<div class="panel-body">

										<div class="form-group">
											<div class="col-md-4">

										{!! Form::label('symptom_onset_date', 'Date of Initial Symptom Onset:', array('class' =>'col-md-12')) !!}
										{!! Form::text('symptom_onset_date', old('symptom_onset_date'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture')) !!}
											<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4">
										{!! Form::label('symptoms', 'Please select all symptoms that apply:', array('class' =>'col-md-12')) !!}
										<select style="width: 100%;" name ="symptoms[]" id="symptoms" multiple="multiple">
											<?php foreach ($disease_symptoms as $key => $value): ?>
												<option value={{$value}}>{{$value}}</option>
											<?php endforeach; ?>
										</select>
										<p class="text-danger">{{ $errors->first('') }}</p>
										</div>

									<div class="col-md-4">
											{!! Form::label('has_unexplained_bleeding', 'Unexplained bleeding from any site?', array('class' =>'col-md-12')) !!}
											<select class="form-control col-sm-4"  name ="has_unexplained_bleeding" id="has_unexplained_bleeding">
												<option value=""></option>
												<option value="Yes" {{ old('has_unexplained_bleeding') == 'Yes'? 'selected' : '' }}>Yes</option>
												<option value="No" {{ old('has_unexplained_bleeding') == 'No'? 'selected' : '' }}>No</option>
											</select>
											<p class="text-danger">{{ $errors->first('') }}</p>
										</div>

									<div class="col-md-4">
											{!! Form::label('bleeding_symptoms', 'If yes to unexplained bleeding select options', array('class' =>'col-md-12')) !!}
											<select class="form-control col-sm-4"  name ="bleeding_symptoms" id="bleeding_symptoms" multiple>
												<option value="select bleeding options"></option>
												<option value="Bleeding of gums" {{ old('bleeding_symptoms') == 'Bleeding of gums'? 'selected' : '' }}>Bleeding of gums</option>
												<option value="Bleeding from injection site" {{ old('bleeding_symptoms') == 'Bleeding from injection site'? 'selected' : '' }}>Bleeding from injection site</option>
												<option value="Nose bleed (epistaxis)" {{ old('bleeding_symptoms') == 'Nose bleed (epistaxis)'? 'selected' : '' }}>Nose bleed (epistaxis)</option>
												<option value="Bloody or black stools (melena)" {{ old('bleeding_symptoms') == 'Bloody or black stools (melena)'? 'selected' : '' }}>Bloody or black stools (melena)</option>
												<option value="Blood or coffee grounds in vomit (hematemesis)" {{ old('bleeding_symptoms') == 'Blood or coffee grounds in vomit (hematemesis)'? 'selected' : '' }}>Blood or coffee grounds in vomit (hematemesis)</option>
												<option value="Coughing up blood (hemoptysis)" {{ old('bleeding_symptoms') == 'Coughing up blood (hemoptysis)'? 'selected' : '' }}>Coughing up blood (hemoptysis)</option>
												<option value="Bleeding from vagina other than menstruation" {{ old('bleeding_symptoms') == 'Bleeding from vagina other than menstruation'? 'selected' : '' }}>Bleeding from vagina other than menstruation</option>
												<option value="Bruising of the skin (Petechiae / ecchymosis)" {{ old('bleeding_symptoms') == 'Bruising of the skin (Petechiae / ecchymosis)'? 'selected' : 'Bruising of the skin (Petechiae / ecchymosis)' }}>Bruising of the skin (Petechiae / ecchymosis)</option>
												<option value="Blood in urine (hematuria)" {{ old('bleeding_symptoms') == 'Blood in urine (hematuria)'? 'selected' : 'Blood in urine (hematuria)' }}>Blood in urine (hematuria)</option>
												<option value="Other hemorrhagic symptoms" {{ old('bleeding_symptoms') == 'Other hemorrhagic symptoms'? 'selected' : 'Other hemorrhagic symptoms' }}>Other hemorrhagic symptoms</option>
											</select>
											<p class="text-danger">{{ $errors->first('') }}</p>
										</div>

										<div class="col-md-4">
											{!! Form::label('other_hemorrhagic_symptoms', 'Other Hemorrhagic Symptoms', array('class' =>'col-md-16')) !!}
											<select class="form-control col-sm-4"  name ="other_hemorrhagic_symptoms" id="other_hemorrhagic_symptoms">
												<option value=""></option>
												<option value="Yes" {{ old('other_hemorrhagic_symptoms') == 'Yes'? 'selected' : '' }}>Yes</option>
												<option value="No" {{ old('other_hemorrhagic_symptoms') == 'No'? 'selected' : '' }}>No</option>
											</select>
											<p class="text-danger">{{ $errors->first('') }}</p>
										</div>


										<div class="col-md-4">
											{!! Form::label('other_nonhemorrhagic_symptoms', 'Other Non-hemorrhagic Clinical Symptoms', array('class' =>'col-md-16')) !!}
											<select class="form-control col-sm-4"  name ="other_nonhemorrhagic_symptoms" id="other_nonhemorrhagic_symptoms">
												<option value=""></option>
												<option value="Yes" {{ old('other_nonhemorrhagic_symptoms') == 'Yes'? 'selected' : '' }}>Yes</option>
												<option value="No" {{ old('other_nonhemorrhagic_symptoms') == 'No'? 'selected' : '' }}>No</option>
											</select>
											<p class="text-danger">{{ $errors->first('') }}</p>
										</div>

									</div>
								</div>
							</div>

							<div class="panel panel-primary">
								<div class="panel-heading "><strong>Section 3: Hospitalization Information</strong></div>
								<div class="panel-body">

								<div class="form-group">

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('is_patient_admitted', 'Is Patient Hospitalized or Admitted To Hospital?', array('class' =>'col-md-16')) !!}
										<select class="form-control col-sm-4"  name ="is_patient_admitted" id="is_patient_admitted">
											<option value=""></option>
											<option value="Yes" {{ old('is_patient_admitted') == 'Yes'? 'selected' : '' }}>Yes</option>
											<option value="No" {{ old('is_patient_admitted') == 'No'? 'selected' : '' }}>No</option>
										</select>
									</div>

									<div class="col-md-4">
										{!! Form::label('hospital_admission_date', 'Date of hospital admission:', array('class' =>'col-md-16')) !!}
										{!! Form::text('hospital_admission_date', old('hospital_admission_date'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture')) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('facility_admitted_at', 'Health Facility Name', array('class' =>'col-md-16')) !!}
										{!! Form::select('facility_admitted_at',[""=>"select facility"]+$facilities,old('hospital_admitted_at'),['id'=>'facility_admitted_at', 'class'=>'form-control', 'style' => 'width:100%']) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('facility_town', 'Village / Town', array('class' =>'col-md-16')) !!}
										{!! Form::text('facility_town', old('facility_town'), array('class' => 'form-control col-sm-4','id'=>'facility_town', 'required'=>1)) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('facility_subcounty', 'Sub-County', array('class' =>'col-md-16')) !!}
										{!! Form::text('facility_subcounty',old('facility_subcounty'),['id'=>'facility_subcounty', 'class'=>'form-control']) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('facility_district', 'District', array('class' =>'col-md-16')) !!}
										{!! Form::text('facility_district',old('facility_district'),['id'=>'facility_district', 'class'=>'form-control']) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('is_patient_isolated', 'Patient in isolation or currently there?', array('class' =>'col-md-16')) !!}
										<select class="form-control col-sm-4"  name ="is_patient_isolated" id="is_patient_isolated">
											<option value=""></option>
											<option value="Yes" {{ old('is_patient_isolated') == 'Yes'? 'selected' : '' }}>Yes</option>
											<option value="No" {{ old('is_patient_isolated') == 'No'? 'selected' : '' }}>No</option>
										</select>
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4">
								{!! Form::label('date_isolated', 'If yes, date of isolation:', array('class' =>'col-md-12')) !!}
								{!! Form::text('date_isolated', old('date_isolated'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture')) !!}
							</div>
								</div>
							</div>
							<div class="panel-body">
								<div class="col-md-4">
							{!! Form::label('was_patient_previously_hospitalized', 'Was the patient hospitalized or did he/she visit a health clinic previously for this illness?:', array('class' =>'col-md-12')) !!}
							<select class="form-control col-sm-4"  name ="patient_previously_hospitalized" id="patient_previously_hospitalized">
								<option value=""></option>
								<option value="Yes" {{ old('patient_previously_hospitalized') == 'Yes'? 'selected' : '' }}>Yes</option>
								<option value="No" {{ old('patient_previously_hospitalized') == 'No'? 'selected' : '' }}>No</option>
								<option value="Unknown" {{ old('patient_previously_hospitalized') == 'Unknown'? 'selected' : '' }}>Unknown</option>
							</select>
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>
								<div class="col-md-8">
							{!! Form::label('hospitalization_info', 'If yes, complete a line of information for each previous hospitalization', array('class' =>'col-md-12')) !!}
							<table class="table table-bordered" id="hospitalization_info_table">
								<tr>
										<th class="text:center" style="width:5%">Dates of Hospitalization</th>
										<th class="text:center">Health Facility Name</th>
										<th class="text:center">Village</th>
										<th class="text:center">District</th>
										<th class="text:center">Was the patient isolated?</th>
										<th><button type="button" name="add" class="btn btn-success btn-sm add"><span class="glyphicon glyphicon-plus"></span></button></th>
									</tr>
								</table>
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>
							</div>
							</div>

							<div class="panel panel-primary">
								<div class="panel-heading "><strong>Section 4: Epidemiological Risk Factors and Exposures</strong></div>
								<div class="panel-body">
									<p><i><u><b>IN THE PAST ONE(1) MONTH PRIOR TO SYMPTOM ONSET:</b></u></i></p>

								<div class="form-group">

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('did_patient_contact_known_suspect', '1. Did the patient have contact with a known or suspect case, or with any sick person before becoming ill?', array('class' =>'col-md-16')) !!}
										<select class="form-control col-sm-4"  name ="did_patient_contact_known_suspect" id="did_patient_contact_known_suspect">
											<option value=""></option>
											<option value="Yes" {{ old('did_patient_contact_known_suspect') == 'Yes'? 'selected' : '' }}>Yes</option>
											<option value="No" {{ old('did_patient_contact_known_suspect') == 'No'? 'selected' : '' }}>No</option>
											<option value="Unknown" {{ old('did_patient_contact_known_suspect') == 'Unknown'? 'selected' : '' }}>Unknown</option>
										</select>
									</div>
									</div>
									</div>

									<div class="panel-body">
										<div class="form-group">
									<div class="col-md-12">
								{!! Form::label('date_isolated', 'If yes, complete a line of information for each sick contact', array('class' =>'col-md-12')) !!}
								<table class="table table-bordered" id="patient_contacts_table">
									<tr>
											<th class="text:center" style="width:15%">Name of Contact</th>
											<th class="text:center" style="width:15%">Relation to Patient</th>
											<th class="text:center" style="width:15%">Date of Exposure</th>
											<th class="text:center" style="width:15%">Village</th>
											<th class="text:center" style="width:15%">District</th>
											<th class="text:center" style="width:15%">Was person dead or alive?</th>
											<th class="text:center" style="width:15%">Contact Types?</th>
											<th><button type="button" name="add_contact" class="btn btn-success btn-sm add_contact"><span class="glyphicon glyphicon-plus"></span></button></th>
										</tr>
									</table>
								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>

									<div class="col-md-4">
										{!! Form::label('did_patient_attend_funeral', '2. Did patient attend a funeral before becoming ill?', array('class' =>'col-lg-18')) !!}
										<select class="form-control col-sm-4"  name ="did_patient_attend_funeral" id="did_patient_attend_funeral">
											<option value=""></option>
											<option value="Yes" {{ old('did_patient_attend_funeral') == 'Yes'? 'selected' : '' }}>Yes</option>
											<option value="No" {{ old('did_patient_attend_funeral') == 'No'? 'selected' : '' }}>No</option>
											<option value="Unknown" {{ old('did_patient_attend_funeral') == 'Unknown'? 'selected' : '' }}>Unknown</option>
										</select>
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-12">
								{!! Form::label('funeral_', 'If yes, complete a line of information for each funeral attended', array('class' =>'col-md-12')) !!}
								<table class="table table-bordered" id="funerals_attended_table">
									<tr>
											<th class="text:center" style="width:15%">Name of Contact</th>
											<th class="text:center" style="width:15%">Relation to Patient</th>
											<th class="text:center" style="width:15%">Dates of Funeral Attendance</th>
											<th class="text:center" style="width:15%">Village</th>
											<th class="text:center" style="width:15%">District</th>
											<th class="text:center" style="width:20%">Did the patient participate <br> (carry or touch the body)?</th>
											<th><button type="button" name="add_funerals_attended" class="btn btn-success btn-sm add_funerals_attended"><span class="glyphicon glyphicon-plus"></span></button></th>
										</tr>
									</table>
								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('did_patient_travel_outside_home', '3. Did the patient travel outside their home or village/town beofre becoming ill?', array('class' =>'col-md-16')) !!}
										<select class="form-control col-sm-4"  name ="did_patient_travel_outside_home" id="did_patient_travel_outside_home">
											<option value=""></option>
											<option value="Yes" {{ old('did_patient_travel_outside_home') == 'Yes'? 'selected' : '' }}>Yes</option>
											<option value="No" {{ old('did_patient_travel_outside_home') == 'No'? 'selected' : '' }}>No</option>
											<option value="Unknown" {{ old('did_patient_travel_outside_home') == 'Unknown'? 'selected' : '' }}>Unknown</option>
										</select>

										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
								</div>
							</div>

							<div class="panel-body">
								<div class="form-group">
									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('village_traveled_to', 'Village / Town', array('class' =>'col-md-16')) !!}
										{!! Form::text('village_traveled_to', old('village_traveled_to'), array('class' => 'form-control col-sm-4','id'=>'village_traveled_to', 'required'=>1)) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>
									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('district_traveled_to', 'District', array('class' =>'col-md-16')) !!}
										{!! Form::text('district_traveled_to',old('district_traveled_to'),['id'=>'district_traveled_to', 'class'=>'form-control']) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('dates_of_travel', 'Dates', array('class' =>'col-md-16')) !!}
										{!! Form::text('dates_of_travel',old('dates_of_travel'),['id'=>'dates_of_travel', 'class'=>'form-control']) !!}
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4" {{ $errors->has('') ? 'has-error' : '' }}">
										{!! Form::label('paid_visit_or_hospitalized_before_illness', '4. Patient hospitalized/Visit anyone in hospital before illness?', array('class' =>'col-md-16')) !!}
										<select class="form-control col-sm-4"  name ="paid_visit_or_hospitalized_before_illness" id="paid_visit_or_hospitalized_before_illness">
											<option value=""></option>
											<option value="Yes" {{ old('paid_visit_or_hospitalized_before_illness') == 'Yes'? 'selected' : '' }}>Yes</option>
											<option value="No" {{ old('paid_visit_or_hospitalized_before_illness') == 'No'? 'selected' : '' }}>No</option>
											<option value="Unknown" {{ old('paid_visit_or_hospitalized_before_illness') == 'Unknown'? 'selected' : '' }}>Unknown</option>
										</select>
										<p class="text-danger">{{ $errors->first('') }}</p>
									</div>

									<div class="col-md-4">
								{!! Form::label('name_of_patient_visited', 'Patient Visited:', array('class' =>'col-md-12')) !!}
								{!! Form::text('name_of_patient_visited', old('name_of_patient_visited'), array('class' => 'form-control col-sm-4')) !!}
							</div>

							<div class="col-md-4">
								{!! Form::label('patient_visit_dates', 'Dates(s):', array('class' =>'col-md-12')) !!}
								{!! Form::text('patient_visit_dates', old('patient_visit_dates'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture')) !!}
									<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
							</div>
							</div>

							<div class="panel-body">
								<div class="form-group">

							<div class="col-md-4">
								{!! Form::label('facility_where_patient_visited', 'Health Facility Name:', array('class' =>'col-md-12')) !!}
								{!! Form::text('facility_where_patient_visited', old('facility_where_patient_visited'), array('class' => 'form-control col-sm-4')) !!}
									<p class="text-danger">{{ $errors->first('') }}</p>
							</div>

							<div class="col-md-4">
								{!! Form::label('village_where_patient_visited', 'Village:', array('class' =>'col-md-12')) !!}
								{!! Form::text('village_where_patient_visited', old('village_where_patient_visited'), array('class' => 'form-control col-sm-4')) !!}
									<p class="text-danger">{{ $errors->first('') }}</p>
							</div>

							<div class="col-md-4">
								{!! Form::label('district_where_patient_visited', 'District', array('class' =>'col-md-12')) !!}
								{!! Form::select('district_where_patient_visited',['' => 'select district...']+$districts,old('district_where_patient_visited'),['class'=>'form-control']) !!}
									<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
								</div>
							</div>
							<div class="panel-body">
								<div class="form-group">
								<div class="col-md-4">
							{!! Form::label('patient_visited_healer', '5. Did patient consult a traditional healer before illness?', array('class' =>'col-md-12')) !!}
							<select class="form-control col-sm-4"  name ="patient_visited_healer" id="patient_visited_healer">
								<option value=""></option>
								<option value="Yes" {{ old('patient_visited_healer') == 'Yes'? 'selected' : '' }}>Yes</option>
								<option value="No" {{ old('patient_visited_healer') == 'No'? 'selected' : '' }}>No</option>
								<option value="Unknown" {{ old('patient_visited_healer') == 'Unknown'? 'selected' : '' }}>Unknown</option>
							</select>
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>

						<div class="col-md-4">
							{!! Form::label('name_of_healer', 'Name of Healer:', array('class' =>'col-md-12')) !!}
							{!! Form::text('name_of_healer',old('name_of_healer'),['class'=>'form-control']) !!}
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>

						<div class="col-md-4">
							{!! Form::label('village_of_healer', 'Village:', array('class' =>'col-md-12')) !!}
							{!! Form::text('village_of_healer',old('village_of_healer'),['class'=>'form-control']) !!}
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>

						<div class="col-md-4">
							{!! Form::label('district_of_healer', 'District', array('class' =>'col-md-12')) !!}
							{!! Form::select('district_of_healer',['' => 'select district...']+$districts,old('district_of_healer'),['class'=>'form-control']) !!}
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>

						<div class="col-md-4">
							{!! Form::label('healer_visit_date', 'Date:', array('class' =>'col-md-12')) !!}
							{!! Form::text('healer_visit_date', old('healer_visit_date'), array('class' => 'form-control col-sm-4  standard-datepicker-nofuture')) !!}
							<p class="text-danger">{{ $errors->first('') }}</p>
						</div>
					</div>
					</div>

					<div class="panel-body">
						<div class="form-group">

							<div class="col-md-4">
								{!! Form::label('had_animal_contact', '6. Did the patient have direct contact with animals or uncooked meat before illness?:', array('class' =>'col-md-12')) !!}
								<select class="form-control col-sm-4"  name ="had_animal_contact" id="had_animal_contact">
									<option value=""></option>
									<option value="Yes" {{ old('had_animal_contact') == 'Yes'? 'selected' : '' }}>Yes</option>
									<option value="No" {{ old('had_animal_contact') == 'No'? 'selected' : '' }}>No</option>
									<option value="Unknown" {{ old('had_animal_contact') == 'Unknown'? 'selected' : '' }}>Unknown</option>
								</select>
								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>

							<div class="col-md-4">
								<label>If yes to Qn. 6, <br>please tick all that apply</label>
								<table class="table table-responsive-sm table-striped table-bordered table-sm" id="logistics">
									<thead>
										<tr>
											<th class="text-center">Animal</th>
											<th class="text-center">Status (check one only)</th>
										</tr>
									</thead>
									<tbody>
										<tr>
										<td><input type="checkbox" id="type_of_animal" name="type_of_animal[]" value="Bats or bat feces/urine"> Bats or bat feces/urine</td>
										<td><input type="radio" id="animal_condition" name="animal_condition" value="Healthy"> Healthy<br><input type="radio" id="animal_condition" name="animal_condition" value="Sick/Dead"> Sick / Dead</td>
									</tr>
										<tr>
										<td> <input type="checkbox" id="type_of_animal" name="type_of_animal[]" value="Primates (Monkeys)"> Primates (Monkeys)</td>
										<td><input type="radio" id="animal_condition" name="animal_condition" value="Healthy"> Healthy<br><input type="radio" id="animal_condition" name="animal_condition" value="Sick/Dead"> Sick / Dead</td>
									</tr>
										<tr>
										<td> <input type="checkbox" id="type_of_animal" name="type_of_animal[]" value="Rodents or rodent feces/urine"> Rodents or rodent feces/urine</td>
										<td><input type="radio" id="animal_condition" name="animal_condition" value="Healthy"> Healthy<br><input type="radio" id="animal_condition" name="animal_condition" value="Sick/Dead"> Sick / Dead</td>
									</tr>
										<tr>
										<td><input type="checkbox" id="type_of_animal" name="type_of_animal[]" value="Pigs"> Pigs</td>
										<td><input type="radio" id="animal_condition" name="animal_condition" value="Healthy"> Healthy<br><input type="radio" id="animal_condition" name="animal_condition" value="Sick/Dead"> Sick / Dead</td>
									</tr>
										<tr>
										<td> <input type="checkbox" id="type_of_animal" name="type_of_animal[]" value="Chickens or wild birds"> Chickens or wild birds</td>
									<td><input type="radio" id="animal_condition" name="animal_condition" value="Healthy"> Healthy<br><input type="radio" id="animal_condition" name="contact_type" value="Sick/Dead"> Sick / Dead</td>
									</tr>
										<tr>
										<td><input type="checkbox" id="type_of_animal" name="type_of_animal[]" value="Cows, goats or sheep"> Cows, goats or sheep</td>
										<td><input type="radio" id="animal_condition" name="animal_condition" value="Healthy"> Healthy<br><input type="radio" id="animal_condition" name="animal_condition" value="Sick/Dead"> Sick / Dead</td>
									</tr>
									</tbody>
								</table>

							</div>
							</div>

							<div class="col-md-4">
								{!! Form::label('patient_bitten_by_tick', '7. Did the patient get bitten by a tick in the past 2 weeks?', array('class' =>'col-md-12')) !!}
								<select class="form-control col-sm-4"  name ="patient_bitten_by_tick" id="patient_bitten_by_tick">
									<option value=""></option>
									<option value="Yes" {{ old('patient_bitten_by_tick') == 'Yes'? 'selected' : '' }}>Yes</option>
									<option value="No" {{ old('patient_bitten_by_tick') == 'No'? 'selected' : '' }}>No</option>
									<option value="Unknown" {{ old('patient_bitten_by_tick') == 'Unknown'? 'selected' : '' }}>Unknown</option>
								</select>
								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
				</div>
				</div>


				<div class="panel panel-primary">
					<div class="panel-heading "><strong>Section 5: Clinical Specimen Colection</strong></div>
				@if(Auth::user()->ref_lab != 55)
				<div class="panel-body">
					<div class="form-group">

						<div class="col-md-4">
							{!! Form::label('sample_type', 'Sample Type:', array('class' => 'col-md-12')) !!}
							<select style="width: 100%;" name ="sample_type[]" id="sample_type" multiple>
							<option value="Whole Blood">Whole Blood</option>
									<option value="Post-mortem heart blood">Post-mortem heart blood</option>
									<option value="Skin Biopsy">Skin Biopsy</option>
									<option value="Other specimen type">Other specimen type</option>
							</select>
						</div>

							<div class="col-md-4">
							{!! Form::label('sample_collection_date', 'Sample Collection Date:', array('class' =>'col-md-12 ')) !!}
							{!! Form::text('sample_collection_date', old('specimen_collection_date'), array('class' => 'form-control col-sm-4 text-line standard-datepicker-nofuture')) !!}
						</div>

						<div class="col-md-4">
							{!! Form::label('sp_ulin', 'Sample ID:', array('class' =>'col-md-12 ')) !!}
							{!! Form::text('sp_ulin', old('specimen_ulin'), array('class' => 'form-control col-sm-4 text-line')) !!}
						</div>
					</div>
					</div>

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
								<td><select style="width: 100%;" name ="disease_symptoms[]" id="samples" multiple>
									<?php foreach ($disease_symptoms as $key => $value): ?>
										<option value={{$value}}>{{$value}}</option>
									<?php endforeach; ?>
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
				</div>

				<div class="panel panel-primary">
					<div class="panel-heading "><strong>Section 6. Case Report Form Completed By:</strong></div>

					<div class="panel-body">
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('interviewer_name') ? 'has-error' : '' }}">
								{!! Form::label('interviewer_name', 'Name:', array('class' =>'col-md-12 ')) !!}
								@if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user())
								{!! Form::text('interviewer_name', Auth::user()->family_name, array('class' => 'form-control col-sm-4','id'=>'interviewer_name', 'required'=>1)) !!}<br><br><br>
								@else
								{!! Form::text('interviewer_name', old('interviewer_name'), array('class' => 'form-control col-sm-4','id'=>'interviewer_name', 'required'=>1)) !!}<br><br><br>
								@endif

								<p class="text-danger">{{ $errors->first('interviewer_name') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('interviewer_phone') ? 'has-error' : '' }}">
								{!! Form::label('interviewer_phone', 'Phone:', array('class' =>'col-md-12 ')) !!}
								@if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user())
								{!! Form::text('interviewer_phone', Auth::user()->telephone, array('class' => 'form-control col-sm-4','id'=>'interviewer_phone', 'required'=>1)) !!}<br><br><br>
								@else
								{!! Form::text('interviewer_phone', old('interviewer_phone'), array('class' => 'form-control col-sm-4','id'=>'interviewer_phone', 'required'=>1)) !!}<br><br><br>
								@endif
								<p class="text-danger">{{ $errors->first('interviewer_phone') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('interviewer_email') ? 'has-error' : '' }}">
								{!! Form::label('interviewer_email', 'Email:', array('class' =>'col-md-12 ')) !!}
								@if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user())
								{!! Form::text('interviewer_email', Auth::user()->email, array('class' => 'form-control col-sm-4','id'=>'interviewer_email', 'required'=>1)) !!}<br><br><br>
								@else
								{!! Form::text('interviewer_email', old('interviewer_email'), array('class' => 'form-control col-sm-4','id'=>'interviewer_email', 'required'=>1)) !!}<br><br><br>
								@endif
								<p class="text-danger">{{ $errors->first('interviewer_email') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
								{!! Form::label('interviewer_position', 'Position:', array('class' =>'col-md-12 ')) !!}
								{!! Form::text('interviewer_position', old('interviewer_position'), array('class' => 'form-control col-sm-4','id'=>'interviewer_position', 'required'=>1)) !!}<br><br><br>

								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
								{!! Form::label('interviewer_district', 'District:', array('class' =>'col-md-12 ')) !!}
								{!! Form::text('interviewer_district', old('interviewer_district'), array('class' => 'form-control col-sm-4','id'=>'interviewer_position', 'required'=>1)) !!}<br><br><br>

								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('interviewer_facility') ? 'has-error' : '' }}">
								{!! Form::label('interviewer_facility', 'Health Facility:', array('class' =>'col-md-12 ')) !!}

								@if(MyHTML::is_site_of_collection_user() || MyHTML::is_site_of_collection_editor() || MyHTML::is_rdt_site_user() || MyHTML::is_facility_dlfp_user())
								{!! Form::text('interviewer_facility', MyHTML::getUserSiteOfCollection()['facility_name'], array('class' => 'form-control col-sm-4','id'=>'interviewer_facility', 'required'=>1)) !!}<br><br><br>
								@else
								{!! Form::text('interviewer_facility', old('interviewer_facility'), array('class' => 'form-control col-sm-4','id'=>'interviewer_facility', 'required'=>1)) !!}<br><br><br>
								@endif

								<p class="text-danger">{{ $errors->first('interviewer_facility') }}</p>
							</div>
						</div>

		<div class="col-md-4">
			<div class="form-group">
					{!! Form::label('info_provided_by', 'Information provide by:', array('class' =>'')) !!}
					<select class="form-control col-sm-4 select-field" name="info_provided_by">
						<option value="" selected="selected"></option>
						<option value="Patient">Patient</option>
						<option value="Proxy">Proxy</option>
				</select>
				</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('proxy_name', 'Name of Proxy:', array('class' =>'col-md-12 ')) !!}
					{!! Form::text('proxy_name', old('proxy_name'), array('class' => 'form-control col-sm-12')) !!}
			</div>
			</div>

			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('proxy_relation_to_patient', 'Relation to Patient:', array('class' =>'col-md-12 ')) !!}
					{!! Form::text('proxy_relation_to_patient', old('proxy_relation_to_patient'), array('class' => 'form-control col-sm-12')) !!}
			</div>
			</div>
		</div>
	</div>
	</div>



{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('save'),
array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
</div>

</div>
</div>
<script type="text/javascript">
$(document).ready(function(){


$(document).on('click', '.add', function(){
	var row_count = parseInt($('#row_count').val())+1;
	$('#row_count').val(row_count);
	var keyup = 'onkeyup="compute('+row_count+')"';

	var html = '';
	html += '<tr>';
				html += '<td><input type="text" name="previous_hospitalization_date[]" '+keyup+' class="form-control standard-datepicker-nofuture" id="previous_hospitalization_date'+row_count+'" /></td>';
				html += '<td><input type="text" name="previously_hospitalized_at[]" '+keyup+' class="form-control" id="previously_hospitalized_at'+row_count+'"  /></td>';
				html += '<td><input type="text" name="previous_village_of_hospitalization[]" '+keyup+' class="form-control" id="previous_village_of_hospitalization'+row_count+'" /></td>';
				html += '<td><select class="form-control" name ="previous_district_of_hospitalization[]" id = "previous_district_of_hospitalization"> <option value="">Select district...</option>@foreach($districts as $key =>$value)<option value="{{$value}}">{{$value}}</option> @endforeach</td>';
				html += '<td><select class="form-control" name ="patient_isolated_at_previous_hospitalization[]" id = "patient_isolated_at_previous_hospitalization"> <option value="">Select...</option><option value="Yes">Yes</option><option value="No">No</option></td>';
				html += '<td><button type="button" name="remove" class="btn btn-danger btn-sm remove"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
				$('#hospitalization_info_table').append(html);

			});

$(document).on('click', '.add_contact', function(){
	var row_count = parseInt($('#row_count').val())+1;
	$('#row_count').val(row_count);
	var keyup = 'onkeyup="compute('+row_count+')"';

	var html = '';
	html += '<tr>';
				html += '<td><input type="text" name="name_of_patient_contact[]" '+keyup+' class="form-control standard-datepicker-nofuture" id="name_of_patient_contact'+row_count+'" /></td>';
				html += '<td><input type="text" name="patient_contact_relationship[]" '+keyup+' class="form-control" id="patient_contact_relationship'+row_count+'"  /></td>';
				html += '<td><input type="text" name="date_of_exposure[]" '+keyup+' class="form-control" id="date_of_exposure'+row_count+'"  /></td>';
				html += '<td><input type="text" name="village_of_contact[]" '+keyup+' class="form-control" id="village_of_contact'+row_count+'"  /></td>';
				html += '<td><select class="form-control" name ="district_of_contact[]" id = "district_of_contact"> <option value="">Select district...</option>@foreach($districts as $key =>$value)<option value="{{$value}}">{{$value}}</option> @endforeach</td>';
				html += '<td><input style="border: 0px;width: 10%; height: 1em;" type="radio" name="status_of_contact[]" '+keyup+' class="form-control col-sm-1" id="status_of_contact'+row_count+'"/> Alive <br> <input style="border: 0px;width: 10%; height: 1em;" type="radio" name="status_of_contact[]" '+keyup+' class="form-control col-sm-1" id="status_of_contact'+row_count+'" />Dead <br> <label>Date of Death</label><input class="form-control col-sm-4  standard-datepicker-nofuture hasDatepicker" name="contact_death_date" type="text" id="contact_death_date"></td>';
				html += '<td> <input type="checkbox" id="contact_type" name="contact_type" value="Touched the body fluids of the case (blood,vomit,saliva,urine,feces)">Touched the body fluids of the case <br> <input type="checkbox" id="contact_type" name="contact_type" value="Had direct physical contact with the body of the case">Had direct physical contact with the body of the case <br> <input type="checkbox" id="contact_type" name="contact_type" value="Touched or shared the linens,clothes or dishes/eating utensils of the case">Touched or shared the linens,clothes or dishes/eating utensils of the case <br> <input type="checkbox" id="contact_type" name="contact_type" value="Slept,ate or spent time in the same household or room as case">Slept,ate or spent time in the same household or room as case </td>';
				html += '<td><button type="button" name="remove_contact" class="btn btn-danger btn-sm remove_contact"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
				$('#patient_contacts_table').append(html);
			});

			$(document).on('click', '.remove_contact', function(){
				$(this).closest('tr').remove();
			});

$(document).on('click', '.add_funerals_attended', function(){
	var row_count = parseInt($('#row_count').val())+1;
	$('#row_count').val(row_count);
	var keyup = 'onkeyup="compute('+row_count+')"';

	var html = '';
	html += '<tr>';
				html += '<td><input type="text" name="name_of_deceased[]" '+keyup+' class="form-control standard-datepicker-nofuture" id="name_of_deceased'+row_count+'" /></td>';
				html += '<td><input type="text" name="deceased_relation_to_patient[]" '+keyup+' class="form-control" id="deceased_relation_to_patient'+row_count+'"  /></td>';
				html += '<td><input type="text" name="funeral_dates[]" '+keyup+' class="form-control" id="funeral_dates'+row_count+'"  /></td>';
				html += '<td><input type="text" name="village_of_funeral[]" '+keyup+' class="form-control" id="village_of_funeral'+row_count+'"  /></td>';
				html += '<td><select class="form-control" name ="district_of_funeral[]" id = "district_of_funeral"> <option value="">Select district...</option>@foreach($districts as $key =>$value)<option value="{{$value}}">{{$value}}</option> @endforeach</td>';
				html += '<td> <input type="checkbox" id="did_patient_participate" name="did_patient_participate" value="Yes">Yes <br> <input type="checkbox" id="did_patient_participate" name="did_patient_participate" value="No">No</td>';
				html += '<td><button type="button" name="remove_funeral_attended" class="btn btn-danger btn-sm remove_funeral_attended"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
				$('#funerals_attended_table').append(html);
			});

			$(document).on('click', '.remove_funeral_attended', function(){
				$(this).closest('tr').remove();
			});
		});

</script>
	@endsection
