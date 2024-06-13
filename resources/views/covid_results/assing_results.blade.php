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

	margin: 0;

	padding: 0;

	color: #333;

	background-color: #fff;

}

</style>

<br><br>

<div class="panel-body">

	<ul class="breadcrumb">	<li><a href="/">HOME</a></li></ul>



	<div id='d3' class="panel panel-default">

		<div class="panel-body">

			<div class="panel-body">



				<form method="get" action="/worksheet_csv/">

					

					<table class="table table-bordered" width="100%">
					 <thead>
							<tr>
							<th>#</th>
							<th>Locator ID</th>
							<td>Tube Id</td>
							<th>Target 1</th>
							<th>Target 2</th>
							<th>Action</th>
						</tr>
					</thead> 
					<tbody>
						<?php $row=1; ?>
						@foreach($ww as $value)
						<tr>
							<td>{{ $row }}</td>
							<td>{{ $value->locator_id}}</td>
							<td>{{ $value->tube_id}}</td>
							<td>{{ $value->result1 }}</td>
							<td>{{ $value->result2 }}</td>
							<td>
								@if(!$value->is_completed)<a href="{{url('/outbreaks/release_retain?type=approve&id='.$value->id)}}" class="btn btn-sm btn-success pull-left"><i class="fa fa-edit"></i>Approve</a>
								 <a href="{{url('/outbreaks/release_retain?type=retain&id='.$value->id)}}" class="btn btn-sm btn-danger pull-left"><i class="fa fa-edit"></i>Reschedule</a>
								 @else
									 @if(!$value->is_approved)
									 	Approved
									 @else
									 	Rescheduled
									 @endif
								@endif</td>

						</tr>
						<?php $row++; ?>
						@endforeach
					</tbody>
					</table>

					{!! Form::close() !!}

				</div>

			</div>

		</div>

		<script>



			$(function() {

				$('#cov_worksheets').DataTable({

					processing: true,

					serverSide: true,

					pageLength:10,



					ajax: '{!! route('worksheets') !!}',



					columns: [

					{ data: 'worksheet_number', name: 'worksheet_number' },

					{ data: 'locator_id', name: 'locator_id' },

					{ data: 'tube_id', name: 'tube_id' },

					{ data: 'action', name: 'action' }

					],

				});

				table.search('mi').draw();

			});



			$(".standard-datepicker-nofuture").datepicker({

				dateFormat: "yy-mm-dd",

				maxDate: 0

			});



		</script>

		@endsection()


