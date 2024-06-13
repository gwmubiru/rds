@extends('layouts/layout')
@section('content')

<div id='d3' class="panel panel-danger">
	<div class="panel-body">
		@if ($message = Session::get('msge'))
		<div class="alert alert-success alert-block">
			<button type="button" class="close" data-dismiss="alert">×</button>
			{!! Session::get('msge') !!}
		</div>
		@endif

		{!! Form::open(array('url'=>'/save/mini_lif','id'=>'form_id')) !!}
		{!! Form::text('formType', "min", array('class' => 'form-control col-sm-4 hidden' )) !!}
		{!! Form::text('id', old('patient_id'), array('class' => 'form-control col-sm-4 hidden','id'=>'id' )) !!}

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

		<br>
		<div class="panel panel-primary">

			<div class="panel-body">
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
		</div>

		<div class="panel panel-primary">
			<div class="panel-body">
				<div class="row">
					<div class="panel-body">
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('epidNo') ? 'has-error' : '' }}">

								{!! Form::label('epidNo', 'Barcode:', array('class' =>'col-md-12 ')) !!}
								{!! Form::text('epidNo', old('epidNo'), array('class' => 'form-control col-sm-4','id'=>'epidNo')) !!}<br><br><br>

								<span class="text-danger">{{ $errors->first('epidNo') }}</span>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('ulin') ? 'has-error' : '' }}">
								{!! Form::label('ulin', 'Locator ID:', array('class' =>'col-md-12  ')) !!}
								{!! Form::select('ulin',[""=>""]+$locator_id,'',['id'=>'lab_number', 'class'=>'form-control', 'style'=>"width:100%"]) !!}

								<br>
								<span class="text-danger">{{ $errors->first('ulin') }}</span>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('serial_number') ? 'has-error' : '' }}">

								{!! Form::label('serial_number', 'Form Number:', array('class' =>'col-md-12 ')) !!}

								{!! Form::text('serial_number', old('serial_number'), array('class' => 'form-control col-sm-4','id'=>'serial_number')) !!}<br><br><br>
								<span class="text-danger">{{ $errors->first('serial_number') }}</span>
							</div>
						</div>

						<div class="col-md-4">
						<div class="form-group {{ $errors->has('who_being_tested') ? 'has-error' : '' }}"">
						{!! Form::label('who_being_tested', 'Who is being tested?:', array('class' =>'col-md-12')) !!}
							<select class="form-control col-sm-4" name="who_being_tested" id="who_being_tested">
								<option value="" selected="selected">Select...</option>

								<option value="Case" {{ old('who_being_tested') == 'Case'? 'selected' : '' }}>Case</option>
								<option value="Contact" {{ old('who_being_tested') == 'Contact'? 'selected' : '' }}>Contact</option>
								<option value="Traveler" {{ old('who_being_tested') == 'Traveler'? 'selected' : '' }}>Traveler</option>
								<option value="Quarantine" {{ old('who_being_tested') == 'Quarantine'? 'selected' : '' }}>Quarantine</option>
								<option value="Alert" {{ old('who_being_tested') == 'Alert'? 'selected' : '' }}>Alert</option>
								<option value="Health Worker" {{ old('who_being_tested') == 'Health Worker'? 'selected' : '' }}>Health Worker</option>
								<option value="EAC Truck Driver" {{ old('who_being_tested') == 'EAC Truck Driver'? 'selected' : '' }}>EAC Truck Driver</option>
								<option value="Other" {{ old('who_being_tested') == 'Other'? 'selected' : '' }}>Other</option>
							</select>
							<span class="text-danger">{{ $errors->first('who_being_tested') }}</span>
						</div>
						</div>
						<div class="col-md-4">
						<div class="form-group" name="hwt" id="hwt" style="display:none;">
							{!! Form::label('facility_name', 'Health Worker&#39;s Facility:', array('class' =>'col-md-12')) !!}
									{!! Form::select('health_care_worker_facility',[""=>"select health worker&#39;s facility"]+$facilities,old('health_care_worker_facility'),['id'=>'health_care_worker_facility', 'class'=>'form-control', 'style' => 'width:100%']) !!}
								</div>
						<div class="form-group" name="traveler" id="traveler" style="display:none; color:red;">
							{!! Form::label('receipt_number', 'Receipt Number:', array('class' =>'col-md-12')) !!}
									{!! Form::text('receipt_number',old('receipt_number'),['class'=>'form-control', 'style' => 'width:100%']) !!}
								</div>
						<div class="form-group" name="eac_driver_id" id="eac_driver_id" style="display:none; color:red;">
							{!! Form::label('eac_driver_id', 'RECDTS ID:', array('class' =>'col-md-12')) !!}
									{!! Form::text('eac_driver_id',old('eac_driver_id'),['class'=>'form-control', 'style' => 'width:100%']) !!}
								</div>
						</div>
					</div>
				</div>

				<div class="panel panel-primary"></div>
				<div class="panel panel-primary">


					<div class="panel-body">
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('patient_surname') ? 'has-error' : '' }}">
								{!! Form::label('patient_surname', 'Fullname:', array('class' =>'col-md-12 ')) !!}
								{!! Form::text('patient_surname', old('patient_surname'), array('class' => 'form-control col-sm-4','id'=>'patient_name')) !!}

								<p class="text-danger">{{ $errors->first('patient_surname') }}</p>

							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('pa') ? 'has-error' : '' }}">
								{!! Form::label('passportNo', 'Passport # / NIN:', array('class' =>'col-md-12 ')) !!}
								{!! Form::text('passportNo', old('passportNo'), array('class' => 'form-control col-sm-4', 'id' => 'passportNo')) !!}

								<p class="text-danger">{{ $errors->first('pas') }}</p>
							</div>
						</div>

						<div class="col-md-4">

							<div class="form-group {{ $errors->has('qas') ? 'has-error' : '' }}">
								{!! Form::label('sex', 'Sex:', array('class' =>'col-md-12 ')) !!}
								<select class="form-control col-sm-4 select-field" name="sex" id="gender">
									<option value="" selected="selected">Select...</option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
								<p class="text-danger">{{ $errors->first('a') }}</p>
							</div>
						</div>
					</div>

					<div class="panel-body">

						<div class="col-md-4">
							{!! Form::label('dob', 'Date of birth:', array('class' =>'col-md-12 ')) !!}
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

								{!! Form::label('nationality', 'Nationality:', array('class' =>'col-md-12	')) !!}
								<label for="Ug"><input type="radio" id="ug" name="origin" value="UGANDAN" onclick="ShowHideDiv()" /> Ugandan</label>
								<label for="noUg"><input type="radio" id="noug" name="origin" value="NON-UGANDAN" onclick="ShowHideDiv()" /> Non-Ugandan</label>
								<label for="noUg"><input type="radio" id="blank" name="origin" value="LEFT BLANK" onclick="ShowHideDiv()" /> Left blank</label>
								<p class="text-danger">{{ $errors->first('origin') }}</p>

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
					</div>

					<div class="panel-body">

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
								{!! Form::label('patient_contact', 'Patient Phone No:', array('class' =>'col-md-12 ')) !!}

								{!! Form::text('patient_contact', old('patient_contact'), array('class' => 'form-control col-sm-4', 'id'=>'patient_contact')) !!}
								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
								{!! Form::label('patient_NOK', 'Next of Kin:', array('class' =>'col-md-12')) !!}
								{!! Form::text('patient_NOK', old('patient_NOK'), array('class' => 'form-control col-sm-4', 'id'=>'patient_NOK')) !!}

								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('') ? 'has-error' : '' }}">
								{!! Form::label('nok_contact', 'Next Of Kin Phone:', array('class' =>'col-md-12')) !!}
								{!! Form::text('nok_contact', old('nok_contact'), array('class' => 'form-control col-sm-4')) !!}
								<p class="text-danger">{{ $errors->first('') }}</p>
							</div>
						</div>
					</div>
				</div>


				<div class="panel panel-primary"></div>
				<div class="panel panel-primary">
					<div class="panel-body">

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('specimen_type') ? 'has-error' : '' }}">
								{!! Form::label('sampletype', 'Sample Type:', array('class' => 'col-md-9')) !!}
								<select style="width: 100%;" name ="specimen_type" id="specimen_type">
									<option value="Nasopharyngeal Swab">Nasopharyngeal Swab</option>
									<option value="Oropharyngeal Swab">Oropharyngeal Swab</option>
								</select>
								<p class="text-danger">{{ $errors->first('specimen_type') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('specimen_collection_date') ? 'has-error' : '' }}">
								{!! Form::label('specimen_collection_date', 'Sample Collection Date:', array('class' =>'col-md-12 ')) !!}
								{!! Form::text('specimen_collection_date', old('specimen_collection_date'), array('class' => 'form-control col-sm-4 text-line standard-datepicker-nofuture')) !!}
								<p class="text-danger">{{ $errors->first('specimen_collection_date') }}</p>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ $errors->has('data_entry_date') ? 'has-error' : '' }}">
								{!! Form::label('data_entry_date', 'Date Received at CPHL:', array('class' =>'col-md-12 ')) !!}
								{!! Form::text('data_entry_date', old('data_entry_date'), array('class' => 'form-control col-sm-4 text-line standard-datepicker-nofuture')) !!}
								<p class="text-danger">{{ $errors->first('data_entry_date') }}</p>

							</div>
						</div>

					</div>
				</div>

		</div>
		</div>


				{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('save'),
				array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
			</div>

		</div>
				@endsection
