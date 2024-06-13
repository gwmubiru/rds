@extends('layouts/layout')

@section('content')

<style type="text/css">
.nav-tabs {
	margin-bottom: 5px;
}
.btn-primary {
	margin-bottom: 5px;
}
.printed_1{
	color:#5cb85c;
}
.form-control{
	width: 173px;
}
</style>
<ul class="breadcrumb">
	<li><a href="/">Home</a></li>
	<li>{!! App\Closet\MyHTML::anchor("/outbreaks/list?type=".base64_encode(1),'All results',52) !!}</li>
</ul>

@include('flash-message')
@if($type == 'pending_results' || $type == 'lab_numbers')
	<br>
<form  method="get" action="/covid_download/">
	<div class='form-inline'>

		<label for="fro">From:

			{!! Form::text('test_date_fro', old('test_date_fro'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}

			<label for="fro">To: </label>

			{!! Form::text('test_date_to', old('test_date_to'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture')) !!}


			<input type='submit' value='Generate CSV' cl  ass="btn btn-success">
		</div>
	</form>
@endif
	<br>

	<div id="my-tab-content" class="tab-printed">
		<div class="tab-pane active" id="print">

			@if($type == 'lab_numbers')
			{!! Form::open(array('url'=>'/mass_patient_info','id'=>'mass_update_info', 'name'=>'mass_update_info')) !!}
			@elseif($type == 'lab_numbers_results')
			{!! Form::open(array('url'=>'/mass_assign_lab_numbers_results','id'=>'mass_assign_lab_numbers_results', 'name'=>'mass_assign_lab_numbers_results')) !!}
			@else
			{!! Form::open(array('url'=>'/mass_assign_results','id'=>'mass_assign_results', 'name'=>'mass_assign_results')) !!}
			@endif

			<!-- @if($type != '')
			<p class="text-center"><input type="submit" class='btn  btn-danger' value="Submit" class="results_update"/></p>
			@endif -->
			<div class="table-responsive">
				<table id="results-table" class="table table-condensed table-bordered  table-striped">
					<thead>
						<tr>
							<th>Patient ID</th>
							<th>Case Name</th>
							<th>Gender</th>
							<th>Age</th>
							<th>Nationality</th>
							<th>Collection Point</th>
							<th>Swabbing District</th>
							<th>@if(Auth::user()->ref_lab == 2891)
								Locator ID
								@else
								Lab No.
								@endif</th>
								<th>Collection Date</th>
								<th>Sample Type</th>
								<th>Result</th>
								<th>Test Date</th>
								<th>Lab Tech</th>
								<th>Lab Tech Contact</th>
								<th>Test Method</th>

							</tr>
						</thead>
					</table>
					@if($type != '')
					<p class="text-center"><input type="submit" class='btn  btn-danger' value="Submit" class="results_update"/></p>
					@endif
					{!! Form::close() !!}
				</div>
			</div>
		</div>
		<style type="text/css">
			#id-search{
				width: 700px;
			}
		</style>

		<script type="text/javascript">
			$(document).ready(function() {
				$(".rest_dr").select2({ allowClear:true });
				$(".standard-datepicker-nofuture").datepicker({
					dateFormat: "yy-mm-dd",
					maxDate: 0
				});
			});


			//don't show  some colums if the user is updating district

			$(function() {
				var type = '{{$type}}';
				if(type == 'pending_results' || type == 'review_results'){
					$('#results-table').DataTable({
						processing: true,
						serverSide: true,
						pageLength: 25,
						ajax: '/cases/list_data?type='+type,
						paging:true,
						"columnDefs": [
						{
							"targets": [2,3,9],
							"visible": false
						}
						],
						createdRow:function(row,data,index){
							$(".date_field",row).datepicker({
								dateFormat: "yy-mm-dd",
								maxDate: 0
							})
						},
						order: [[ 7, "asc" ]],
					});
				}else if(type == 'lab_numbers'){
					$('#results-table').DataTable({
						processing: true,
						serverSide: true,
						pageLength: 25,
						ajax: '/cases/list_data?type='+type,
						paging:true,
						"columnDefs": [
						{
							"targets": [4,10,11,12,13,14],
							"visible": false
						}
						],
						createdRow:function(row,data,index){
							$(".date_field",row).datepicker({
								dateFormat: "yy-mm-dd",
								maxDate: 0
							});
							$(".rest_dr",row).select2({ allowClear:false });
							$(".select2.select2-container",row).css("width", "150px");
						},
						order: [[ 0, "desc" ]],
					});
				}else if(type == 'lab_numbers_results'){
					$('#results-table').DataTable({
						processing: true,
						serverSide: true,
						pageLength: 25,
						ajax: '/cases/list_data?type='+type,
						paging:true,

						"columnDefs": [
						{
							"targets": [2,3,4],
							"visible": false
						}
						],
						createdRow:function(row,data,index){
							$(".date_field",row).datepicker({
								dateFormat: "yy-mm-dd",
								maxDate: 0
							})
						},
						order: [[ 0, "desc" ]],
					});
				}
				else{
					$('#results-table').DataTable({
						processing: true,
						serverSide: true,
						pageLength: 25,
						ajax: '/cases/list_data?type='+type,
						paging:true,
						"columnDefs": [
						{
							"targets": [12,13,14],
							"visible": false
						}
						],
						order: [[ 0, "desc" ]],

					});
				}

			});
		</script>
		@endsection()
