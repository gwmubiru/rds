@extends('layouts/layout')
@section('content')


<div id='d3' class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>'/sample_reception/update','id'=>'form_id')) !!}

		@if(count($errors))
		<div class="alert alert-danger">
			<strong>Hey!</strong> There were some problems with your input.
			<br/>
			<ul>
				@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif

		<div class="panel panel-primary">
			<div class="panel-heading "><p><i>Add Samples</i></p></div>

			<div class="col-md-4">
				<div class="form-group {{ $errors->has('barcode') ? 'has-error' : '' }}">
					{!! Form::label('barcode', 'Barcode:', array('class' =>'col-md-12')) !!}
					{!! Form::text('barcode', $query[0], array('class' => 'form-control col-sm-4')) !!}
					<p class="text-danger">{{ $errors->first('barcode') }}</p>
				</div>
			</div>
			<br><br><br>
			<div class="panel-body">
				<div class="form-inline">

					<table class="table table-bordered" id="item_table">
						<tr>
							<th class="text:center">Locator ID <small></th>
								<th class="text:center">High Priority?<small></th>
									<input type="text" id="row_count" name="abc" value="0" class="hidden">

								</tr>
							</table>
							<th><button type="button" name="add" class="btn btn-success btn-sm add"><span class="glyphicon glyphicon-plus"></span></button></th>
							<div align="center">
								{!! Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('save'),
								array('class' => 'btn btn-primary', 'onclick' => 'submit()')) !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script>
		//Date picker
		$('#datepicker').datepicker({
			autoclose: true
		})

		$(document).ready(function(){
			$('#row_count').val(0);

			$(document).on('click', '.add', function(){

				var row_count = parseInt($('#row_count').val());
				$('#row_count').val(row_count+1);
				var keyup = 'onkeyup="compute('+row_count+')"';

				var html = '';
				html += '<tr class="tableRow">';

					html += '<td><input type="text" name="data['+row_count+'][specimen_ulin]" class="f" style="width:100%" ></td>';
					html += '<td><input type="checkbox" name="data['+row_count+'][priority]" value=1 ></td>';

					html += '<td><button type="button" name="remove" class="btn btn-danger btn-sm remove"><span class="glyphicon glyphicon-minus"></span></button></td></tr>';
					$('#item_table').append(html);
				});

				$(document).on('click', '.remove', function(){
					$(this).closest('tr').remove();
				});
			});

			$(function(){
				$(".f").datepicker({
					dateFormat: "yy-mm-dd"
				})
			})

		</script>
		@endsection
