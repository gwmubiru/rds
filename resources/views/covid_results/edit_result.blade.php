@extends('layouts/layout')
@section('content')

<ul class="breadcrumb">
    <li><a href="/">HOME</a></li>
</ul>

<div id="d3" class="panel panel-primary">
	<div class="panel-body">
		{!! Session::get('msge') !!}
    {!! Form::model($patient, array('route' => array('update.result', $patient->id), 'method' => 'POST','id' => 'form-editresult')) !!}
	<div class="panel panel-danger">
			<div class="panel-body">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="panel-heading" style="color:#337ab7;  background-color: #f5f5f5;">
              Patient Name:<b> {!! $patient['attributes']['patient_firstname'] == '' ? $patient->patient_surname : $patient->patient_surname.' '.$patient['attributes']['patient_firstname']  !!}</b>
              || Sex::<b> {!! $patient->sex !!} </b>|| Age:: <b>{!! $patient->age_units == '' ? $patient->age : $patient->age.' '.$patient->age_units !!}</b>
              || Nationality::<b> {!! $patient->nationality == '' ? ' ' : $patient->nationality !!}</b> 	|| Contact::<b> {!! $patient->patient_phone !!}</b>
              || Form Identifier::<b> {!! $patient->caseID !!} </b> || Registered::<b> {!! $patient->request_date !!}</b>
            </div>
          </div>
        </div>

                        <div class="form-inline">
          						{!! Form::label('test_result', 'Test Result:', array('class' =>'col-md-2 ')) !!}
                      <select style="width: 18%;" name ="test_result" id='test_result'>
                      <option value=""></option>
                      <option value="Negative">Negative</option>
                    <option value="Positive">Positive</option>
                    </select> <br><br>

                    	{!! Form::label('test_type', 'Test Type:', array('class' =>'col-md-2 ')) !!}
                      <select style="width: 18%;" name ="test_method">
                      <option value=""></option>
                      <option value="Gene-Xpert">Gene-Xpert</option>
                      <option value="PCR">PCR</option>
                      <option value="RDT">RDT</option>
                    </select> <br><br>
                  </div>

                <div class="form-inline">
                    {!! Form::label('tested_by', 'Tested By (Lab Tech):', array('class' =>'col-md-2 ')) !!}
                    {!! Form::text('tested_by', old('tested_by'), array('class' => 'form-control col-sm-4 text-line')) !!}
                    {!! Form::label('lab_tech_phone', 'Lab Tech Phone:', array('class' =>'col-md-2 ')) !!}
                    {!! Form::text('lab_tech_phone', old('lab_tech_phone'), array('class' => 'form-control col-sm-4 text-line')) !!}
                  </div><br><br><br>

                    <div class="form-inline">

                    {!! Form::label('result_date', 'Test Date:', array('class' =>'col-md-2 ')) !!}
                    <input type="hidden" name="patient_id" value="{!!$patient->id!!}">
                  <input type="hidden" name="sample_id" value="{!!$sample_id!!}">
                  {!! Form::text('result_date', old('result_date'), array('class' => 'form-control col-sm-4 text-line standard-datepicker-nofuture')) !!}
                  </div><br>
				</div>
			</div>
		</div>

		<div align="center">
			{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('save'),	array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
	</div>
	</div>
	<script>

		$(".standard-datepicker-nofuture").datepicker({
		dateFormat: "yy-mm-dd",
			maxDate: 0
		});

    $("#test_result").change(function () {
   if ($(this).val() == "Positive") {
       $('#myModal').modal('show');
     }
 });
	</script>
	@endsection
