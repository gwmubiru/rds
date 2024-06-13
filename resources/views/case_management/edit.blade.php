@extends('layouts/layout')

@section('content')
<div class="container-fluid">
	@include('flash-message')
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Edit Resuts</div>
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

					<form class="form-horizontal" role="form" method="POST" action="/cases/update/{{$patient->id}}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="id" value="{{$patient->id}}">

						<div class="form-group">
					{!! Form::label('creater', 'Created By', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{{MyHTML::getUserByID($patient->createdby)}}
							</div>
						</div>
						<div class="form-group">
					{!! Form::label('epidNo', 'Patient ID', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('epidNo', $patient->epidNo, array('class' => 'form-control text-line')) !!}
							</div>
						</div>

						@if($sample)
						<div class="form-group">

					{!! Form::label('specimen_ulin', 'Lab Number/Locator ID', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('specimen_ulin', $sample->specimen_ulin, array('class' => 'form-control text-line')) !!}
								{!! Form::hidden('sample_id', $sample->id )!!}
							</div>
						</div>

						@endif
						<div class="form-group">

					{!! Form::label('passportNo', 'Passport Number', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('passportNo', $patient->passportNo, array('class' => 'form-control text-line')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('patient_surname', 'Surname', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('patient_surname', $patient->patient_surname, array('class' => 'form-control text-line')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('nationality', 'Nationality', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('nationality', $patient->nationality, array('class' => 'form-control text-line')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('sex', 'Sex', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! MyHTML::select('sex',$gender_arr,$patient->sex,'sex','form-control input-sm') !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('age', 'Age', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('age', $patient->age, array('class' => 'form-control text-line')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('nameWhere_sample_collected_from', 'Site of Collection', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('nameWhere_sample_collected_from', $patient->nameWhere_sample_collected_from, array('class' => 'form-control text-line')) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('swabing_district', 'Swabbing District', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! MyHTML::select('swabing_district',$districts,$patient->swabing_district,'','form-control input-sm') !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('dob', 'Date of birth', array('class' =>'col-md-2')) !!}

							<div class="col-md-6">
								{!! Form::text('dob', $patient->dob, array('class' => 'form-control  standard-datepicker-nofuture text-line')) !!}
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
									Update
								</button>
							</div>
						</div>
					</form>
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
	});
</script>
@endsection
