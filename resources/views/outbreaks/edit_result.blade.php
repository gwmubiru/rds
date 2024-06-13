@extends('layouts/layout')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Edit Results for {{$result->patient_id}}</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> Some errors.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					@if(App\Closet\MyHTML::permit(22))
					<form class="form-horizontal" role="form" method="POST" action="/update_outbreak_result">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="id" value="{{ $result->id }}">
						@if(!App\Closet\MyHTML::is_site_of_collection_editor())
						<div class="form-group">
							{!! Form::label('date_of_collection', 'Date of Collection', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('date_of_collection', $result->date_of_collection, array('class' => 'form-control  standard-datepicker-nofuture text-line')) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('sentinel_site', 'Sentinel Site', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('sentinel_site', $result->sentinel_site, array('class' => 'form-control text-line')) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('sentinel_site_other', 'Other Sentinel Site', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('sentinel_site_other', $result->sentinel_site_other, array('class' => 'form-control text-line')) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('patient_id', 'Patient ID', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('patient_id', $result->patient_id, array('class' => 'form-control text-line')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('district', 'Swabbing District', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">

								{!! Form::select('district',$districts,$result->district,['id'=>'swabing_district', 'class'=>'form-control']) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('patient_district', 'Patient District', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('patient_district', $result->patient_district, array('class' => 'form-control text-line')) !!}
							</div>
						</div>

						@endif
						<div class="form-group">
							{!! Form::label('case_name', 'Patient Name', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('case_name', $result->case_name, array('class' => 'form-control text-line')) !!}
								{!! Form::text('original_case_name', $result->case_name, array('class' => 'form-control text-line hidden')) !!}
							</div>
						</div>
						@if(!App\Closet\MyHTML::is_site_of_collection_editor())
						<div class="form-group">
							{!! Form::label('passport_number', 'Passport Number', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('passport_number', $result->passport_number, array('class' => 'form-control text-line')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('who_is_being_tested', 'Who is being tested?', array('class' =>'col-md-2')) !!}
							<div class="col-md-6">
								<select class="form-control col-sm-4" name="who_is_being_tested" id="who_is_being_tested">

									<option value="" selected="selected">Select...</option>
									<option value="Case" {{ $result->who_is_being_tested == 'Case'? 'selected' : '' }}>Case</option>
									<option value="Traveller" {{ $result->who_is_being_tested == 'Traveller'? 'selected' : '' }}>Traveller</option>
									<option value="Quarantine" {{ $result->who_is_being_tested == 'Quarantine'? 'selected' : '' }}>Quarantine</option>
									<option value="Alert" {{ $result->who_is_being_tested == 'Alert'? 'selected' : '' }}>Alert</option>
									<option value="Health Worker" {{ $result->who_is_being_tested == 'Health Worker'? 'selected' : '' }}>Health Worker</option>
									<option value="EAC Truck Driver" {{ $result->who_is_being_tested == 'EAC Truck Driver' ? 'selected' : '' }}>EAC Truck Driver</option>
									<option value="Routine" {{ $result->who_is_being_tested == 'Routine' ? 'selected' : '' }}>Routine</option>

								</select>
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('receipt_number', 'Receipt Number', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('receipt_number', $result->receipt_number, array('class' => 'form-control text-line')) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('email_address', 'Email Address', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('email_address', $result->email_address, array('class' => 'form-control text-line')) !!}
							</div>
						</div>


						<div class="form-group">
							{!! Form::label('age_years', 'Age in Years', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('age_years', $result->age_years, array('class' => 'form-control text-line')) !!}
							</div>
						</div>

						<div class="form-group">
							{!! Form::label('sex', 'Sex', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::select('sex', $genders, $result->sex, ['class' => 'form-control']) !!}
							</div>
						</div>

						<div class="form-group hidden">
							{!! Form::label('result', 'Result', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::select('result', $result_values, $result->result, ['class' => 'form-control']) !!}
							</div>
						</div>
						<div class="form-group hidden">
							{!! Form::label('test_date','Date of Testing',  array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('test_date', $result->test_date, array('class' => 'form-control  standard-datepicker-nofuture text-line')) !!}
							</div>
						</div>

						<div class="form-group hidden">
							{!! Form::label('is_released','Status',  array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::select('is_released', $result_status, $result->is_released, ['class' => 'form-control']) !!}
							</div>
						</div>
						@endif
						@if(App\Closet\MyHTML::isSpecialUser())
							<div class="form-group">
								{!! Form::label('is_classified', 'Is VVIP', array('class' =>'col-md-2')) !!}

								<div class="col-md-6">

									{!! Form::select('is_classified',['0'=>'No', '1'=>'Yes'],$result->is_classified,['id'=>'is_classified', 'class'=>'form-control']) !!}
								</div>
							</div>
						@endif
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									Update
								</button>
							</div>
						</div>
					</form>
					@else
						You do not have the permission to perform this action
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$(".standard-datepicker-nofuture").datepicker({
			dateFormat: "yy-mm-dd",
			maxDate: 0
		});
		$("#swabing_district").select2();
	});
</script>
@endsection
