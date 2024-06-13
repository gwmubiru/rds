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
.right_alined{
	margin-right: 10px;
}

</style>

<br><br>

<div class="panel-body">

	<ul class="breadcrumb">	<li><a href="/">HOME</a></li></ul>



	<div id='d3' class="panel panel-default">

		<div class="panel-body">

			<div class="panel-body">



				<form method="get" action="/worksheet_csv/">

					<div class='form-inline'>

						<label for="fro">From:

							{!! Form::text('created_from', old('created_from'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

							<label for="fro">To: </label>

							{!! Form::text('created_to', old('created_to'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

							<input type='submit' value='Generate CSV' cl	ass="btn btn-success">

						</form>

					</div>

					<!--<div align="left">

						<a href="/new/worksheet" class='btn btn-md btn-danger'>Create worksheet</a><br><br>

					</div>-->

					<table class="table table-responsive-sm table-striped table-bordered table-sm" id="cov_worksheets">

						<thead>

							<tr>

								<th>Worksheet number</th>


								<th>Action</th>

								<!-- <th></th> -->

							</tr>

						</thead>

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


					{ data: 'action', name: 'action' }

					],

				});

				table.search('mi').draw();

			});



			$(".standard-datepicker-nofuture").datepicker({

				dateFormat: "yy-mm-dd",

				maxDate: 0

			});

			@if($next)
				$(".w_create").click();
			@endif

		</script>

		@endsection()


