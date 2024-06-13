@extends('layouts/layout')
@section('content')
<link   href="{{ asset('/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('/js/select2.min.js') }}" type="text/javascript"></script>
<div class="panel panel-default">
   
    <div class="panel-body">
         <form action="/search/" method="GET">
            <div class="form-inline">     

                {!! Form::select('facility',[""=>"Select Facility"]+ \App\Models\Facility::facilitiesByDistrictsArr(),\Request::get('facility'),['id'=>'fclty', "required"=>"true", "class"=>"form-control input-sm"]) !!}
                &nbsp;
                {!! Form::select('filter_by',[""=>"Filter by", "batch_number"=>"Batch Number", "infant_name"=>"Infant Name", "infant_exp_id"=>"EXP ID"],\Request::get('filter_by'),['id'=>'filter_by', "required"=>"true", "class"=>"form-control input-sm"]) !!}
                &nbsp;
                <input type="text" name="search_value" value="{{ \Request::get('search_value') }}" class="form_control input-sm" placeholder="Enter what to search for" required="true">
                &nbsp;
                <input type="submit" name="" value="search" class="btn btn-primary btn-sm">
            </div>
        </form>
         <br>
        <table id="results-table" class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th>Facility</th>               
                	<th>District</th>
                    <th>Batch Number</th>
                    <th>Infant Name</th>
                    <th>EXP ID</th>
                    <th>Gender</th>
                    <th>Date Collected</th>
                    <th>Date Received</th>
                    <th>Date Tested</th>
                    <th>Date Released</th>
                    <th>Results</th>
                    <th>Date Printed</th>
                </tr>
            </thead>        
            <tbody>
                @foreach($samples AS $sample)
                 <tr>
                    <td>{{ $sample->facility }}</td>               
                    <td>{{ $sample->district }}</td>
                    <td>{{ $sample->batch_number }}</td>
                    <td>{{ $sample->infant_name }}</td>
                    <td>{{ $sample->infant_exp_id }}</td> 
                    <td>{{ $sample->infant_gender }}</td>
                    <td>{{ $sample->date_dbs_taken }}</td>
                    <td>{{ $sample->date_rcvd_by_cphl }}</td>
                    <td>{{ $sample->date_dbs_tested }}</td>
                    <td>{{ $sample->qc_at }}</td>
                    <td>{{ $sample->accepted_result }}</td>
                    <td>{{ $sample->dispatch_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
 </div>
</div>

<script type="text/javascript">

$(function() { 
    //$("#fclty").select2({   placeholder:"Select facility", allowClear:true, width: '20%' });
     //$("#filter_by").select2({   placeholder:"Filter by", allowClear:true, width: '20%' });
    $('#results-table').DataTable(); 

});
</script>
@endsection