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
	width: 500px;
}
</style>

@include('flash-message')

<br>
<form  method="get" action="/audit_download/">
	<div class='form-inline'>


		@if(\Auth::user()->type == 16)
		{!! Form::label('ref_lab_name', 'Laboratory:', array('class' =>'col-sm-1 ')) !!}
		{!! Form::text('ref_lab_name', $ref_labs, array('class' => 'form-control col-sm-2 text-line','style'=>"width:250px")) !!}
		@else
		{!! Form::select('ref_lab_name',[""=>"Select testing laboratory"]+$ref_labs,'',['id'=>'testing_lab', 'class'=>'form-control',  'placeholder'=>'Select testing laboratory','style'=>"width:250px"]) !!}
		@endif

		<label for="fro">Tested between:</label>

			{!! Form::text('test_date_fro', old('test_date_fro'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture', 'placeholder'=>'Start Date','style'=>"width:200px")) !!}

			<label for="to">And: </label>

			{!! Form::text('test_date_to', old('test_date_to'), array('class' => 'form-control input-sm standard-datepicker standard-datepicker-nofuture', 'placeholder'=>'End Date','style'=>"width:200px")) !!}


			<input type='submit' value='Download Audit trail CSV' class="btn btn-success">
		</div>
	</form>
	<br>

	<div id="my-tab-content" class="tab-printed">
		<div class="tab-pane active" id="print">
			<div class="table-responsive">
				<table id="audit-table" class="table table-condensed table-bordered  table-striped">
					<thead>
						<tr>
							<th>Transaction Date</th>
							<th>Date of Collection</th>
							<th>Sample Received</th>
							<th>Sample Type</th>
							<th>Collection Site</th>
							<th>Swabbing District</th>
							<th>Client Identifier</th>
							<th>Client Name</th>
							<th>Passport Number</th>
							<th>Age</th>
							<th>sex</th>
							<th>Client Contact</th>
							<th>Nationality</th>
							<th>Reason for Testing</th>
							<th>Specimen Identifier</th>
							<th>Test Date</th>
							<th>Result</th>
							<th>Test Method</th>
							<th>Testing Laboratory</th>
							<th>Uploaded By</th>
							<th>Date Uploaded</th>
							<th>CSV File Used</th>
							<th>System Given CSV name</th>
							<th>Number of times downloaded</th>
							<th>Printed By</th>
							<th>Date Last Printed</th>
							</tr>
						</thead>
					</table>
					<p class="text-center"><input type="submit" class='btn  btn-danger' value="Submit" class="results_update"/></p>
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
				if(type == 'audit'){
					$('#audit-table').DataTable({
						processing: true,
						serverSide: true,
						pageLength: 25,
						ajax: '/audit/get_data?type='+type,
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
				}


			});
		</script>
		@endsection()
