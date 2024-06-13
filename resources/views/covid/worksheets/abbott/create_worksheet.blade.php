@extends('layouts/layout')
@section('content')

<ul class="breadcrumb">
  <li><a href="/">HOME</a></li>
  <li><a href="/worksheet/list">Worksheets</a></li>
</ul>

<div id="d3" class="panel panel-primary">
  <div class="panel-body">
    {!! Session::get('msge') !!}
    {!! Form::open(array('url'=>'save/worksheet','id'=>'form_id')) !!}
    @if(count($errors))
    <div class="alert alert-danger">
      <strong>Hey!</strong> There were some problems with your worksheet.
      <br/>
      <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <?php
    $d = date('ym');
    $cols = 94;
    $i = 'abCOVID';
    ?>
    <div class="row">
      {!! Form::text('machine_type', 'abbott', array('class' => 'form-control hidden')) !!}
      <div class="panel-body">

        <div class="form-group {{ $errors->has('worksheet_number') ? 'has-error' : '' }}">

          {!! Form::label('worksheet_number', 'Worksheet No.:', array('class' =>'col-md-12')) !!}
          {!! Form::text('worksheet_number', $d.$i.old('worksheet_number'), array('class' => 'form-control col-sm-4','id'=>'worksheet_number')) !!}<br><br><br>

          <p class="text-danger">{{ $errors->first('worksheet_number') }}</p>
        </div>

        <table class="table table-bordered" id="samples">
          <tr>
            <th class="text:center" style="color:#337ab7">#</th>
            <th class="text:center" style="color:#337ab7">Locator ID</th>
            <!-- <th class="text:center" style="color:#337ab7">Tube ID</th> -->
          </tr>

          @for($i = 1; $i <= $cols; $i++)
          <tr>
            <td style="color:#337ab7">{!! $i !!}</td>
            <td style="color:#337ab7">	{!! Form::text('locator_id[]', $d.'-'.old('locator_id'), array('class' => 'form-control col-sm-4')) !!} </td>
          </tr>
          @endfor
        </table>

      </div>
      <div align="center">
        {!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('Save & load next worksheet'),	array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
      </div><br>

    </div>
  </div>
</div>

<script>
  $(".standard-datepicker-nofuture").datepicker({
    dateFormat: "yy-mm-dd",
    maxDate: 0
  });
</script>
@endsection
