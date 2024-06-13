@extends('layouts/layout')
@section('content')
<style>
  .tube{
    margin-bottom: 5px;
  }
</style>
<ul class="breadcrumb">
  <li><a href="/">HOME</a></li>
  <li><a href="/worksheet/list">Worksheets</a></li>
</ul>

<div id="d3" class="panel panel-primary">
  <div class="panel-body">
    {!! Session::get('msge') !!}
    {!! Form::open(array('url'=>'save/worksheet','id'=>'form_id')) !!}
    {!! Form::hidden('pool_type', $pool_type)!!}
    {!! Form::hidden('machine_type', $machine_type)!!}
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

    @if($computed_number_of_tubes < $number_of_tubes)
    <div class="alert alert-info alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button> 
      Although you wanted to use <strong>{{$number_of_tubes}} tubes</strong>, the available samples can only fit in <strong>{{$computed_number_of_tubes}} tubes </strong>
    </div>
    @endif
    <div class="row">

      <div class="panel-body">

        <th class="text:center" style="color:#337ab7">  <b>Worksheet No:</b> <br>{!! Form::text('worksheet_number', $worksheed_number, array('class' => 'form-control col-sm-4','style'=>'margin-bottom:10px; width:317px;')) !!}</th>

        <table class="table table-bordered" id="samples">

          <tr>

            <th class="text:center" style="color:#337ab7">#</th>
            <th class="text:center" style="color:#337ab7">Tube ID</th>
            <th class="text:center" style="color:#337ab7">Locator ID</th>

            

          </tr>
          <?php 
            //set counter for sample_ids
            $ids_counter = 0;
          ?>
          @for($i = 0; $i < $computed_number_of_tubes; $i++)

          <tr>

            <td style="color:#337ab7">{!! $i !!}</td>

            <td style="color:#337ab7">	{!! Form::text('worksheet_data['.$i.'][tube_id][]', old('tube_id'), array('class' => 'form-control col-sm-4')) !!} </td>

            <td style="color:#337ab7"> 	
              @for($j = 1; $j <= $total_pool; $j++)
              <!-- Check that the numbe of locator_ids correspond to number of tubes needed -->
                @if($ids_counter < count($locator_ids))
                  {!! Form::text('worksheet_data['.$i.'][locator_ids][]', $locator_ids[$ids_counter]->specimen_ulin, array('class' => 'form-control col-sm-4 tube', 'readonly'=>'readonly','tabindex' => '-1')) !!}
                  {!! Form::text('worksheet_data['.$i.'][sample_ids][]', $locator_ids[$ids_counter]->id, array('class' => 'form-control col-sm-4 hidden')) !!}
                @endif
                <?php 
                 //Increment sample id counter at this point
                //
                  $ids_counter++;
                 ?>
              @endfor

               </td>

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


