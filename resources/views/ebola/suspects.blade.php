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

</style>
<div class="panel-body">
<br><br>
	<ul class="breadcrumb">	<li><a href="/">HOME</a></li></ul>

	<div id='d3' class="panel panel-default">
		<div class="panel-body">
			<div class="panel-body">
				<form method="get" action="/covid_download/">

					<div class='form-inline'>
						<label for="fro">From:
							{!! Form::text('test_date_fro', old('test_date_fro'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

							<label for="fro">To: </label>
							{!! Form::text('test_date_to', old('test_date_to'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

							<input type='submit' value='Generate CSV' class="btn btn-success">
						</form>
					</div>

					<table class="table table-responsive-sm table-striped table-bordered table-sm" id="covid">
						<thead>
							<tr>
								<!-- <th>#</th> -->
								<th>Patient Name</th>
								<th>Gender</th>
								<th>Age</th>
								<th>Nationality</th>
								<th>Patient Contact</th>
								<th>Registration Date</th>
								<th>Registered & Collected By</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
		<script>
			$(function() {
				$('#covid').DataTable({
					processing: true,
					serverSide: true,
					pageLength:10,

					ajax: '{!! route('getSuspectData') !!}',
					columns: [
					{ data: 'patient_surname', name: 'patient_surname' },
					{ data: 'sex', name: 'sex' },
					{ data: 'age', name: 'age' },
					{ data: 'nationality', name: 'nationality' },
					{ data: 'patient_contact', name: 'patient_contact' },
					{ data: 'request_date', name: 'request_date' },
					{ data: 'interviewer_name', name: 'interviewer_name' },
					{ data: 'action', name: 'action' }
					],

					columnDefs: [{
						"targets": [ 2 ],
						"render": function ( data, type, row ) {
							return data +'  '+ row.age_units +' ' ;
						}

					}]

				});
			});

			$(document).ready( function () {
				var  DT1 = $('#covid').DataTable();
				$(".selectAll").on( "click", function(e) {
					if ($(this).is( ":checked" )) {
						DT1.rows(  ).select();
					} else {
						DT1.rows(  ).deselect();
					}
				});
			} );

			$('#datepicker').datepicker({
				autoclose: true
			});
			$(".standard-datepicker-nofuture").datepicker({
				dateFormat: "yy-mm-dd",
				maxDate: 0
			});

			/* Custom filtering function which will search data in column four between two values */
			$.fn.dataTableExt.afnFiltering.push(
			function( oSettings, aData, iDataIndex ) {
				var iMin = document.getElementById('min').value * 1;
				var iMax = document.getElementById('max').value * 1;
				var iVersion = aData[3] == "-" ? 0 : aData[3]*1;
				if ( iMin == "" && iMax == "" )
				{
					return true;
				}
				else if ( iMin == "" && iVersion < iMax )
				{
					return true;
				}
				else if ( iMin < iVersion && "" == iMax )
				{
					return true;
				}
				else if ( iMin < iVersion && iVersion < iMax )
				{
					return true;
				}
				return false;
			}
			);

			$(document).ready(function() {
				/* Initialise datatables */
				var oTable = $('#covid').dataTable();

				/* Add event listeners to the two range filtering inputs */
				$('#min').keyup( function() { oTable.fnDraw(); } );
				$('#max').keyup( function() { oTable.fnDraw(); } );
			} );


			$(".standard-datepicker-nofuture").datepicker({
				dateFormat: "yy-mm-dd",
				maxDate: 0
			});
		</script>



		@endsection()
