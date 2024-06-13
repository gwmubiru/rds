@extends('layouts/layout')
@section('content')
<?php 
$tail = "";
$activ = "$('#results').addClass('active');";
if(session('rtype') == 'rejected'){
	$tail = "&rj=y";
    $activ = "$('#rejected_results').addClass('active');";
}


?>
<div class="panel panel-default">
    <div class="panel-heading"> <h3 class="panel-title">Facilities :: {!! \Auth::user()->hub_name !!}</h3> </div>
    <div class="panel-body">
        <table id="results-table" class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th>Facility</th>               
                	<th width='10'>Contact Person</th>
                    <th width='10'>Phone</th>
                    <th width='10'>Email</th>
                    <th>#&nbsp;Pending (EID)</th>
                    <th>#&nbsp;Pending (SCD)</th>
                    <th>#&nbsp;Pending (Rejects)</th>
                    <th>Oldest Pending</th>
                    <th>Lastest Dispatch&nbsp;Date</th>
                    <th></th>
                </tr>
            </thead>        
            <tbody>
                @foreach($facilities AS $facility)
                 <tr>
                    <td><a href='/results/{{ $facility->id }}/'>{{ $facility->facility }}</a></td>               
                    <td>{{ $facility->contactPerson }}</td>
                    <td>{{ $facility->phone }}</td>
                    <td>{{ $facility->email }}</td> 
                    <td><a href='/results/{{$facility->id}}/'>{{ $facility->eid_pending }}</a></td> 
                    <td><a href='/results/{{$facility->id}}/?type=scd'>{{ $facility->scd_pending }}</a></td>
                    <td><a href='/results/{{$facility->id}}/?type=rejects'>{{ $facility->rejects_pending }}</a></td>
                    <td>{{ \MyHTML::dateDiff(date('Y-m-d'), $facility->oldest_pending) }}</td>
                    <td>{{ $facility->last_dispatch_date }}</td>                   
                    <td><?= "<a href='/results/$facility->id/'>view pending</a>" ?></td>
                </tr>
                @endforeach
            </tbody>
        </table>
 </div>
</div>

<script type="text/javascript">

{!! $activ !!};

$(function() { $('#results-table').DataTable(); });
</script>
@endsection