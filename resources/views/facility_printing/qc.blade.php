@extends('layouts/layout')

@section('content')
<?php
if($type=='scd'){
	$scd_class = "btn-primary";
	$eid_class = "btn-default";
	$rejects_class = "btn-default";
} elseif ($type=='rejects') {
	$scd_class = "btn-default";
	$eid_class = "btn-default";
	$rejects_class = "btn-primary";
}else {
	$scd_class = "btn-default";
	$eid_class = "btn-primary";
	$rejects_class = "btn-default";
}

?>
<div class="panel panel-default">
	<div class="panel-heading">QUALITY CONTROL</div>
	<div class="panel-body">
		<div style="margin-bottom:10px;">
			<a href="/qc_list/eid/" class='btn {{$eid_class}}' style='width:200px;'>EID</a>
			<a href="/qc_list/scd/" class='btn {{$scd_class}}' style='width:200px;'>SCD</a>
			<a href="/qc_list/rejects/" class='btn {{$rejects_class}}' style='width:200px;'>REJECTS</a>
		</div>

		<ul id="tabs_a" class="nav nav-tabs">
		    <li @if($qced=='NO') class='active' @endif>
		    	<a href="?qced=NO">Pending</a>
		    </li>
		    <li @if($qced=='YES') class='active' @endif>
		    	<a href="?qced=YES&type={{ $type }}">QC Done</a>
		    </li>
		</ul>
	
		<div class="tab-content">
			<div class="tab-pane active"> 
				<table id="results-table" class='table table-bordered table-striped table table-condensed' >
					<thead>
						<tr>
							<th>Hub</th>
							<th>Facility</th>
							<th>Envelope Number</th>               
							<th>Batch Number</th>
							<th>Date released</th>
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

.saved{
	padding: 5px;
	display: none;
}
</style>

<script type="text/javascript">
$('#l4').addClass('active');
var datatable = $('#results-table').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    ajax: '/qc_list_data/{{ $type }}/?qced={{ $qced }}',
    order: [[ 2, "desc" ]],
});

function release(batch_pk){
	var params = {_token:"{{ csrf_token() }}", type:"{{ $type }}"};
	$.post("/qc/"+batch_pk, params).done(function( data ) {
		 $("#saved"+batch_pk).show();
	});

}

// $(".link").on("click",function(){
// 	datatable.ajax.reload();
// });

	/*$(".release").click(function(){
		alert("dededed");
	 	var params = {_token:"{{ csrf_token() }}", batch_pk:$(this).val(), type:"{{ $type }}"};
	 	
	 	$.post("/qc/"+params.batch_pk, params).done(function( data ) {
		    $("#saved"+params.batch_pk).show();
		  });

	 });*/

</script>

@endsection()