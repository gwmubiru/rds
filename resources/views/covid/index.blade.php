@extends('layouts/layout')

@section('content')



<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>



<link href="https://nightly.datatables.net/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />

<script src="https://nightly.datatables.net/js/jquery.dataTables.js"></script>

<link href="https://nightly.datatables.net/select/css/select.dataTables.css?_=766c9ac11eda67c01f759bab53b4774d.css" rel="stylesheet" type="text/css" />

<script src="https://nightly.datatables.net/select/js/dataTables.select.js?_=766c9ac11eda67c01f759bab53b4774d"></script>





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



<br><br><br>

<ul class="breadcrumb">	<li><a href="/">HOME</a></li></ul>

<!-- <div class="panel panel-primary"> -->

	<br>

	<div class="panel-body">

<a href="/cif/covid/form" class='btn btn-lg btn-success'>CIF Form</a>

<a href="/lif/covid/form" class='btn btn-lg btn-warning'>LIF Form</a>

<a href="/poe/covid/form" class='btn btn-lg btn-danger'>POE Form</a>

<br><br>

	<table class="table table-responsive-sm table-striped table-bordered table-sm" id="covid">

		<thead>

			<tr>

				<th>Patient ID</th>

				<th>Patient Name</th>

				<th>Gender</th>

				<th>Age</th>

				<th>Nationality</th>

				<th>Patient Contact</th>

				<th>Registration Date</th>

				<th>Registered & Collected By</th>

				<th>Specimen collected</th>

				<th>specimen ID</th>

				<th>Action</th>

			</tr>

		</thead>

	</table>

	{!! Form::close() !!}

</div>

<!-- </div> -->

<script>

	$(function() {

		$('#covid').DataTable({

			processing: true,

			serverSide: true,

			pageLength:10,



			ajax: '{!! route('getCovidData') !!}',

			columns: [

			{ data: 'epidNo', name: 'epidNo' },

			{ data: 'patient_surname', name: 'patient_surname' },

			{ data: 'sex', name: 'sex' },

			{ data: 'age', name: 'age' },

			{ data: 'nationality', name: 'nationality' },

			{ data: 'patient_contact', name: 'patient_contact' },

			{ data: 'request_date', name: 'request_date' },

			{ data: 'interviewer_name', name: 'interviewer_name' },

			{ data: 'specimen_type', name: 'specimen_type' },

			{ data: 'specimen_ulin', name: 'specimen_ulin' },

			{ data: 'action', name: 'action' }

			]

		});

	});





</script>







@endsection()	
