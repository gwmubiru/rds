@extends('layouts/layout')

@section('content')
<?php
if($type=='scd'){
	$scd_class = "btn-primary";
	$eid_class = "btn-default";
	$rejects_class = "btn-default";
	$followup_class = "btn-default";
}elseif ($type=='rejects') {
	$scd_class = "btn-default";
	$eid_class = "btn-default";
	$rejects_class = "btn-primary";
	$followup_class = "btn-default";
}elseif ($type=='followup') {
	$scd_class = "btn-default";
	$eid_class = "btn-default";
	$rejects_class = "btn-default";
	$followup_class = "btn-primary";
}else {
	$scd_class = "btn-default";
	$eid_class = "btn-primary";
	$rejects_class = "btn-default";
	$followup_class = "btn-default";
}

?>
<!-- <div class="row">
	<ul class=" col-md-4 breadcrumb" style="font-size:12px;padding:4px;">
	    <li><a href="/">HOME</a></li>
	    <li><a href="/results">FACILITIES</a></li>
	    <li action="active">{{ $facility->facility }}</li>
	</ul>
	<div class="col-md-6">
		<a href="?type=eid" class='btn {{$eid_class}}' style='width:200px;'>EID</a>
		<a href="?type=scd" class='btn {{$scd_class}}' style='width:200px;'>SCD</a>
	</div>
</div> -->

<!--  -->
<div class="panel panel-default">
	<div class="panel-heading">{{ $facility->facility }}</div>
	<div class="panel-body">
		<div style="margin-bottom:10px;">
			<a href="?type=eid" class='btn {{$eid_class}}' style='width:200px;'>
				EID	<span class="badge">{{ $facility->eid_pending }}</span>
			</a>
			<a href="?type=scd" class='btn {{$scd_class}}' style='width:200px;'>
				SCD <span class="badge">{{ $facility->scd_pending }}</span>
			</a>
			<a href="?type=rejects" class='btn {{$rejects_class}}' style='width:200px;'>
				REJECTS <span class="badge">{{ $facility->rejects_pending }}</span>
			</a>
			<a href="?type=followup" class='btn {{$followup_class}}' style='width:200px;'>FOLLOW UP</a>
		</div>

		<ul id="tabs_a" class="nav nav-tabs">
		    <li @if($dispatched==0) class='active' @endif>
		    	<a href="?dispatched=0&type={{ $type }}">Pending</a>
		    </li>
		    <li @if($dispatched==1) class='active' @endif>
		    	<a href="?dispatched=1&type={{ $type }}">Printed/Downloaded</a>
		    </li>
		</ul>
	
		<div class="tab-content">
			<div class="tab-pane active"> 
				<table id="results-table" class='table table-bordered table-striped table table-condensed' >
					<thead>
						<tr>
							<th>Envelope Number</th>               
							<th>Batch Number</th>
							<th>Date&nbsp;received at&nbsp;CPHL</th>
							<th>Date released</th>
							<th>Date downloaded/printed</th>
							<th>Print&nbsp;TAT</th>
							<th>Options</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
.d{
	text-align: center;
}

.tab-content {
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    padding: 10px;
}

.nav-tabs {
    margin-bottom: 0;
}
</style>

<script type="text/javascript">
$('#results').addClass('active');
var datatable = $('#results-table').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    ajax: '/results_data/{{ $facility_id }}/?type={{ $type }}&dispatched={{ $dispatched }}',
    order: [[ 2, "asc" ]],
});

// $(".link").on("click",function(){
// 	datatable.ajax.reload();
// });
</script>

@endsection()