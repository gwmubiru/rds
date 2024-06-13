@extends('layouts/layout')
@section('content')

<ul class="breadcrumb">
    <li><a href="/">HOME</a></li>
		<li><a href="/cif/covid/form">CIF Form</a></li>
		<li><a href="/lif/covid/form">LIF Form</a></li>
</ul>

<div id="d3" class="panel panel-primary">
	<div class="panel-body">
		{!! Session::get('msge') !!}
    {!! Form::model($patient, array('route' => array('covid.update', $patient->id), 'method' => 'POST','id' => 'form-edit-commodity')) !!}

		<h3 style="text-align:center"><strong>Boarder-Point Of Entry</strong></h3>
		<h3 class="panel-title" style="text-align:center"><strong>PORT HEALTH SERVICES</strong></h3>
		<h1 class="panel-title" style="text-align:center">Covid-19 drivers screening and sample collection form</h1>
    <br>
    <div class="form-inline">
      {!! Form::label('pointOfEntry', 'Point Of Entry:', array('class' =>'col-md-2 ')) !!}
      {!! Form::text('pointOfEntry', $patient->pointOfEntry, array('class' => 'form-control col-sm-4')) !!}
</div>
<br><br>

		<div class="panel panel-danger">
			<div class="panel-body">
				<div class="form-inline">
					{!! Form::label('epidNo', 'EPID No:(for UVRI only)', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('epidNo', $patient->epidNo, array('class' => 'form-control col-sm-4')) !!}

					{!! Form::label('caseID', 'Case ID:', array('class' =>'col-md-1 ')) !!}
					{!! Form::text('caseID', $patient->caseID, array('class' => 'form-control col-md-4')) !!}

					{!! Form::label('request_date', 'Date:', array('class' =>'col-md-1')) !!}
					{!! Form::text('request_date', $patient->request_date, array('class' => 'form-control col-sm-4 standard-datepicker-nofuture ')) !!}

				</div><br><br>

				<div class="form-inline "><p style="color:white;"></p></div>

				<div class="form-inline">
					{!! Form::label('patient_surname', 'Patient Name:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('patient_surname', $patient->patient_surname, array('class' => 'form-control col-sm-4 text-line')) !!}

					{!! Form::label('sex', 'Sex:', array('class' =>'col-md-1 ')) !!}
					<div class="radio-inline">{!! Form::radio('sex', 'Male', $patient->sex == "Male" ? true : false) !!} <span class="input-tag">M</span></div>
					<div class="radio-inline">{!! Form::radio('sex', 'Female', $patient->sex == "Female" ? true : false) !!} <span class="input-tag">F</span></div>

				</div><br>

				<div class="form-inline">
					{!! Form::label('age', 'Age:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('age',$patient->age, array('class' => 'form-control col-sm-4 text-line')) !!}

				</div><br>

				<div class="form-inline "><p style="color:white;">.</p></div>

				<div class="form-inline">
					{!! Form::label('nationality', 'Nationality:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('nationality', $patient->nationality, array('class' => 'form-control col-sm-4 text-line')) !!}

					{!! Form::label('patient_contact', 'Mobile Number:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('patient_contact', $patient->patient_contact, array('class' => 'form-control col-sm-4 text-line')) !!}

				</div><br>
				<div class="form-inline "><p style="color:white;">.</p></div>

				<div class="form-inline">
					{!! Form::label('passportNo', 'Passport Number:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('passportNo', $patient->passportNo, array('class' => 'form-control col-sm-4 text-line')) !!}

				</div><br>

				<div class="form-inline "><p style="color:white;">.</p></div>
				<div class="form-inline">
					{!! Form::label('truckNo', 'Truck Number:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('truckNo', $patient->truckNo, array('class' => 'form-control col-sm-4 text-line')) !!}

					{!! Form::label('truckDestination', 'Destination:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('truckDestination', $patient->truckDestination, array('class' => 'form-control col-sm-4 text-line')) !!}

				</div><br>

				<div class="form-inline "><p style="color:white;">.</p></div>
				<div class="form-inline">
					{!! Form::label('truckEntryDate', 'Entry Date:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('truckEntryDate', $patient->truckEntryDate, array('class' => 'form-control col-sm-4 standard-datepicker-nofuture')) !!}

					{!! Form::label('tempReading', 'Temperature reading:', array('class' =>'col-md-2 ')) !!}
					{!! Form::text('tempReading', $patient->tempReading, array('class' => 'form-control col-sm-4 text-line')) !!}

				</div><br>

				<div class="form-inline "><p style="color:white;">.</p></div>
				<div class="form-inline">
					{!! Form::label('sampleCollected', 'Sample Collected?', array('class' => 'col-md-2')) !!}
					<select style="width: 18%;" name="sampleCollected" id="sampleCollected">
						<option value="" selected="selected">Select...</option>
						<option value="Yes" {!! $patient->sampleCollected == "Yes" ? 'selected' : '' !!}>Yes</option>
						<option value="No" {!! $patient->sampleCollected == "No" ? 'selected' : '' !!}>No</option>
					</select> <br><br>

					<div class="form-inline" id="sample">
						{!! Form::label('sampletype', 'Sample Information:', array('class' => 'col-md-2')) !!}
            <table class="table table-bordered" id="item_table">
              <thead>
                <tr>
                  <th class="text:center">Specimen Type <small></th>
                    <th class="text:center">Specimen ID</th>
                    <th class="text:center">Date collected</th>
                    <th class="text:center">Result</th>
                    <th class="text:center">Result Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sample as $samples)
                  <tr>
                    <td>{!! $samples->specimen_type !!}</td>
                    <td>{!! $samples->specimen_ulin !!}</td>
                    <td>{!! $samples->request_date !!}</td>
                    <td>Negative  </td>
                    <td>{!! $samples->request_date !!}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

					<div class="form-inline "><p style="color:white;">.</p></div>
					<div class="form-inline">
						{!! Form::label('interviewer_name', 'Sample Collected By:', array('class' =>'col-md-2')) !!}
						{!! Form::text('interviewer_name', $patient->interviewer_name, array('class' => 'form-control col-sm-4 ')) !!}

						{!! Form::label('interviewer_phone', 'Telephone:', array('class' =>'col-md-2 ')) !!}
						{!! Form::text('interviewer_phone', $patient->interviewer_phone, array('class' => 'form-control col-sm-4 text-line')) !!}

					</div><br>
				</div>
			</div>
		</div>

		<div align="center">
			{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('save'),	array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
		</div>
	</div>

	<script>

		$('#datepicker').datepicker({
			autoclose: true
		});

		$(".standard-datepicker-nofuture").datepicker({
			dateFormat: "yy-mm-dd",
			maxDate: 0
		});

	</script>
	@endsection
