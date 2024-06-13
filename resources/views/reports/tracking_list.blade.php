@extends('layouts/layout')
@section('content')

<div id="d2" class="panel panel-default">
	<div class="panel-body">
    {!! Form::open(array('url'=>'/reports/tracking','id'=>'other_filters_form')) !!}
       <div class="well firstrow list">

          <div class="row">
            <div class="col-md-12">
             <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                  <label for="test_fro">Received between:</label>
                  <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_fro" type="text" id="test_from" value="{{ $test_fro}}">
                  <label for="test_to">and </label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_to" type="text" id="test_to" value="{{ $test_to }}">
                  <input type="hidden" name="p_type" value="{!!$page_type!!}">
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
              <th>Package ID</th>
              <th>Collection Point</th>               
              <th>Collection Point Name</th>
              <th>District</th>
              <th>Hub</th>
              <th>Destination</th>
              <th>No. Samples</th>
              <th>Picked at</th>
              <th>Status</th>
              <th>Last seen on</th>
              <th>Last seet at</th>
              <th>Delivered on</th>
              <th>Received at</th>
              <th>TAT</th>
          </tr>
          </thead>
          
        </table>
      </div>

	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		//$('#listtable').DataTable();
      $(".standard-datepicker-nofuture").datepicker({
        dateFormat: "yy-mm-dd",
        maxDate: 0
      });
  });
  $(function() {
      var page_type =' {{$page_type}}';
      var test_fro = '{{$test_fro}}';
      var test_to = '{{$test_to}}';
      $('#listtable').DataTable({
          processing: true,
          serverSide: true,
          pageLength: 25,
          ajax: '/reports/tracking_data?type='+page_type+'&test_fro='+test_fro+'&test_to='+test_to,
          order: [[ 1, "desc" ]],
           "columnDefs": [
            { "orderable": true, "targets": [0,11] }
          ],
          paging:true,
          //lengthMenu: [10, 25, 50, 75, 100],
      });
  });
	

</script>
				@endsection
