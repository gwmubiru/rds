@extends('layouts/layout')
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			{!! Form::open(array('url' => 'saveLogisticsReport', 'id' => 'form-facilityrequest', 'method' => 'POST')) !!}
			<div class="panel panel-default">
				<div class="panel-body">
					@if (count($errors) > 0)
					<div class="alert alert-danger">
						<strong>Whoops!</strong> Some errors.<br><br>
						<ul>
							@foreach ($errors->all() as $error)
							<li>{!! $error !!}</li>
							@endforeach
						</ul>
					</div>
					@endif
					<div class="form-group col-xs-3">
						<br>
						{!! Form::label('facility', 'Reporting Facility', array('class' => 'required')) !!}
						{!! Form::text('facility',$facility_and_district[0]->facility,array('class' => 'form-control','readonly')) !!}
						{!! Form::text('facility_id',$facility_and_district[0]->facilityID,array('class' => 'form-control hidden')) !!}
						</div>

					<div class="form-group col-xs-3">
						<br>
						{!! Form::label('district', 'District',  array('class'=>'col-sm-6')) !!}
						{!! Form::text('district',$facility_and_district[0]->district,array('class' => 'form-control ','readonly')) !!}
						{!! Form::text('district_id',$facility_and_district[0]->districtID,array('class' => 'form-control hidden')) !!}
					</div>

					<div class="form-group col-xs-2">
						<br>
						{!! Form::label('start_date', 'Start Date',  array('class'=>'col-sm-4s')) !!}

						{!! Form::text('start_date', old('start_date'),array('class' => 'form-control standard-datepicker standard-datepicker-nofuture','placeholder' =>'Start Date...','required'=>'required', 'id'=>'start_date')) !!}
					</div>
					<div class="form-group col-xs-2">
						<br>
						{!! Form::label('end_date', 'End Date',  array('class'=>'col-sm-4s')) !!}
						{!! Form::text('end_date', old('end_date'),array('class' => 'form-control standard-datepicker-nofuture','placeholder' =>'End Date...','required'=>'required','id'=>'_date')) !!}
					</div>


					<div class="form-group col-xs-2">
						<br>
						{!! Form::label('date_submitted', 'Submitted', array('class' =>'col-sm-2 ')) !!}
						{!! Form::text('date_submitted', old('date_submitted'),array('class' => 'form-control standard-datepicker-nofuture',	'placeholder' =>'Date Submitted','required'=>'required','id'=>'date_submitted', 'required')) !!}
					</div>
				</td>
			</tr>

				<form method="post" id="insert_form"><input type="hidden" name="row_count" id="row_count" value=0>
					<div class="table-repsonsive">
						<span id="error"></span>
						<table class="table table-bordered table-responsive" id="item_table">
							<tr>
								<th class="text:center" width='22%'>Commodity Category</th>
								<th class="text-center" width='20%'>Item/Commodity</th>
								<th class="text-center" width='10%'>Opening Balance <small><br>at start <br>of opening cycle</small></th>
								<th class="text-center" width='10%'>Qty Received <small><br>during the <br> reporting period</small></th>
								<th class="text-center" width='10%'>Total Consumption <small><br>during the <br> reporting period</small></th>
								<th class="text-center" width='10%'>Losses / Adjustments<br><samll>(-/+)</small></th>
									<th class="text-center" width='10%'>Total Closing Balance</th>
									<th class="text-center">Comments</th>
									<th><button type="button" name="add" class="btn btn-success btn-sm add"><span class="glyphicon glyphicon-plus"></span></button></th>
								</tr>
							</table>
							<div align="center">
								{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('Submit'),
								array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
							</div>
							<br>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$(".standard-datepicker-nofuture").datepicker({
		maxDate: 0
	});
	//Date picker
	$('#datepicker').datepicker({
		autoclose: true
	})
	$(document).ready(function(){

		$(document).on('click', '.add', function(){
			var row_count = parseInt($('#row_count').val())+1;
			$('#row_count').val(row_count);
			var keyup = 'onkeyup="compute('+row_count+')"';

			var html = '';
			html += '<tr>';
				html += '<td><select style="width: 100%" name ="commodity_category[]" id = "commodity_category"> <option value="">Category of commodity...</option><option value="1">RDT testing kit</option><option value="2">Sample collection kit</option><option value="3">PCR testing commodity</option></td>';
				html += '<td><select style="width: 100%" name ="commodity[]" id = "commodity"> <option value="">Select Item.....</option><option value="Nasopharyngeal swab">Nasopharyngeal swab</option><option value="Oropharyngeal swab">Oropharyngeal swab</option><option value="STANDARD Q">STANDARD Q</option><option value="ABBOT PANBIO">ABBOT PANBIO</option></td>';
					html += '<td><input type="text" name="opening_balance[]" '+keyup+' class="form-control opening_balance" id="opening_balance'+row_count+'" /></td>';
					html += '<td><input type="text" name="qty_received[]" '+keyup+' class="form-control qty_received" id="qty_received'+row_count+'"  /></td>';
					html += '<td><input type="text" name="total_consumption[]" '+keyup+' class="form-control total_consumption" id="total_consumption'+row_count+'" /></td>';
					html += '<td><input type="text" name="losses_adjustments[]" '+keyup+' class="form-control losses_adjustments" id="losses_adjustments'+row_count+'" /></td>';
					html += '<td><input type="text" name="total_closing_balance[]"  '+keyup+' class="form-control total_closing_balance" id="total_closing_balance'+row_count+'" readonly="true"/></td>';
					html += '<td><input type="text" name="comment[]" class="form-control comments" /></td>';
					html += '<td><button type="button" name="remove" class="btn btn-danger btn-sm remove"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
					$('#item_table').append(html);

				});

				$(document).on('click', '.remove', function(){
					$(this).closest('tr').remove();
				});
			});

			//$(".opening_balance").change(function(){ alert("dee");});
			function compute(row_count){

				//variables to store values enterd for the fields.
				var ob = parseInt($("#opening_balance"+row_count).val());
				var qr = parseInt($("#qty_received"+row_count).val());
				var tc = parseInt($("#total_consumption"+row_count).val());
				var la = parseInt($("#losses_adjustments"+row_count).val());
				var sr = parseInt($("#stock_required"+row_count).val());

				//computes the total closing balance
				$("#total_closing_balance"+row_count).val((ob+qr)-(tc - la));

				//gets the computed total closing balance
				var tcc =	((ob+qr)-(tc - la));

				//computes the maximum stock quantity required
				$("#stock_required"+row_count).val(tc*2);

				//computes the quantity to be ordered
				// var x = sr-tcc;
				$("#qty_to_order"+row_count).val(sr-tcc);
			}


			// Generates auto-fill of distri, hub and IP text boxes
			$("#facility_id").change(function(){
				$.ajax({
					url: "/facilities/" + $(this).val(),
					type: 'GET',

					success: function(data) {
						$('#hub_id').val(data.hubID);
						$('#hub_id').attr('readonly','true');

						$('#district_id').val(data.districtID);
						$('#district_id').attr('readonly','true');

						$('#ip_id').val(data.ipID);
						$('#ip_id').attr('readonly','true');

						console.log(data);
					}
				});
			});

			//Auto-fill UOM
			$("#commodity_id").change(function(){
				$.ajax({
					url: "/items/" + $(this).val(),
					type: 'GET',
					success: function(data) {

						$('#issue_unit').val(data.metric_id);
						$('#issue_unit').attr('readonly','true');
					}
				});
			});

			$(document).ready(function(){
				$(".standard-datepicker-nofuture").datepicker({
					dateFormat: "yy-mm-dd",
					maxDate: 0
				});
				$("#swabing_district").select2();
			});
		</script>
		@endsection
