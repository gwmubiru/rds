@extends('layouts/layout')
@section('content')

<div id="d2" class="panel panel-default">
  <div class="panel-body">
    {!! Form::open(array('url'=>'/reports/tracking/view_details','id'=>'view_det')) !!}
       <div class="well firstrow list">

          <div class="row">
            <div class="col-md-12">
             <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                  
                  
                  <label for="code">Tracking Code </label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="code" type="text" id="code" value="{{$code}}">
                  <input type="submit" value="Search" class="btn btn-primary btn-sm" style="margin-top: 5px;">          

              </div>
              </div>
          </div>
        </div>
    {!! Form::close() !!}
    
    {!! Session::get('msge') !!}
    <div class="table-responsive">
      <table id="listtable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Locator ID</th>
              <th>Case Name</th>
              <th>Test Date</th>
              <th>Test Result</th>
              
          </tr>
          </thead>
          <tbody>
            @foreach($rows as $row)
            <tr>
              <td>{{$row->specimen_ulin}}</td>
              <td>{{$row->patient_surname.' '.$row->patient_firstname}}</td>
              <td>{{$row->test_date}}</td>
              <td>{{$row->test_result}}</td>
            </tr>
            @endforeach
          </tbody>
          
        </table>
      </div>

  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#listtable').DataTable();
     
  });

</script>
        @endsection
