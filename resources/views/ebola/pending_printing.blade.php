@extends('layouts/layout')
@section('content')
<style type="text/css">
.nav-tabs {
	margin-bottom: 5px;
}
.btn-primary {
	margin-bottom: 5px;
}

body {
	/* font: 90%/1.45em "Helvetica Neue", HelveticaNeue, Verdana, Arial, Helvetica, sans-serif; */
	margin: 0;
	padding: 0;
	color: #333;
	background-color: #fff;
}
table {
	width: 100%;
	word-break: break-all;
	border-style: solid;
}
</style>

<div class="panel-body">
	<div class="container">
		<div class="tab-pane table-responsive">
			<h3 style="margin-top:52px;">EVD Pending Printing</h3>
			<table class="table  table-striped table-bordered table-sm" id="summaries_and_contacts_table">
				<thead>
					<tr>
						<th>District</th>
						<th>Total Pending</th>
						<th>Contacts</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($results_pending_printing AS $result)
					<tr>
						<td width="150px;">{{$result->district}}</td>
						<td>{{$result->number_of_results}}</td>
						<td>{{$result->contacts}}</td>
						
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
		

<script>

	$(function() {
		$('#summaries_and_contacts_table').DataTable({
		});
		
	});


</script>
@endsection()

