@extends('layouts/layout')

@section('content')



<style type="text/css">

.nav-tabs {

	margin-bottom: 5px;

}

.btn{

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
				{!! Form::open(array('url'=>'/outbreaks/release_retain','id'=>'approve_form', 'name'=>'approve_form')) !!}
				<input type="text" name="type" class="hidden" id="type">
				<a href="#" class="btn btn-xs btn-primary " id="select_all">Select all visible</a>
       
          		<input class="btn  btn-xs btn-success" name="pdf" type="button" value="Approve all selected" onclick="approveSelected('approve');">
       
        		<input type="button" class="btn btn-xs  btn-danger " value="Reschedule all selected" onclick="approveSelected('retain');">
        		 
					<table class="table table-bordered" width="100%">
					 <thead>
							<tr>
							<th>#</th>
							<th></th>
							<td>Tube Id</td>
							<th>Locator ID</th>
							
							@if($worksheet->machine_type == 0)
								<th>Target 1</th>
								<th>Target 2</th>					
								
							@endif
							<td>Final Result</td>
							<th>Action</th>
						</tr>
					</thead> 
					<tbody>

						<?php $row=1; ?>
						@foreach($ww as $value)
						<tr>
							<td>{{ $row }}</td>
							<td><input type="checkbox" class="samples" name="worksheet_sample_ids[]" value="{{$value->worksheet_sample_id}}"></td>
							<td><span id="tube_id_text_{{$value->worksheet_sample_id}}">{{ $value->tube_id}}</span>
								<input type="text" name="tube_{{$value->worksheet_sample_id}}" value="{{$value->tube_id}}" id="tube_id_{{$value->worksheet_sample_id}}" data-sampleid = "{{$value->sample_id}}" class="samples form-control hidden"></td>
							</td>
							<td><span id="locator_id_text_{{$value->worksheet_sample_id}}">{{ $value->locator_id}}</span>
								<input type="text" name="worsheet_samples_{{$value->worksheet_sample_id}}" value="{{$value->locator_id}}" id="locator_id_{{$value->worksheet_sample_id}}" class="samples form-control hidden"></td>
							
							@if($worksheet->machine_type == 0)	
								<td>{{ $value->result1 }}</td>														
								<td>{{ $value->result2 }}</td>
							@endif
							<td>{{ $value->final_result }}</td>
							<td>
								@if(!$value->is_completed)
									@if( $value->final_result == '')
									<a class="btn btn-sm btn-primary" href="#" id="edit_{{$value->worksheet_sample_id}}" onclick="openEditForm({{$value->worksheet_sample_id}})">Edit</a>
									<a class="btn btn-sm btn-primary hidden" href="#" id="update_{{$value->worksheet_sample_id}}" onclick="updateWorkSheetSample({{$value->worksheet_sample_id}})">Update</a>
									@endif
									@if( $value->final_result != '')
									<a href="{{url('/outbreaks/release_retain?type=approve&id='.$value->id)}}" class="btn btn-sm btn-success"><i class="fa fa-edit"></i>Approve</a>
								 <a href="{{url('/outbreaks/release_retain?type=retain&id='.$value->id)}}" class="btn btn-sm btn-danger"><i class="fa fa-edit"></i>Reschedule</a>
								 	@endif
								 @else
									 @if($value->is_approved)
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
			function updateWorkSheetSample(wp_id){
				$(function(){
					//get the art numbers for a facility
					var locator_id = $('#locator_id_'+wp_id).val();
					var tube_id = $('#tube_id_'+wp_id).val();
					var sample_id = $('#tube_id_'+wp_id).attr('data-sampleid');
					var edit = 'edit_'+wp_id;
					var update = 'update_'+wp_id;
					var locator_id_text ='locator_id_text_'+wp_id;
					var tube_id_text = 'tube_id_text_'+wp_id;

					var url =  "/update_worksheet_samples/?locator_id="+locator_id+"&tube_id="+tube_id+"&id="+wp_id+'&sample_id='+sample_id;
					$.get(url, function(data, status){
						var data_arr = JSON.parse(data);
		           		//console.log(data_arr.tube_id);
		           		$('#locator_id_'+wp_id).val(data_arr.locator_id);
		           		$('#locator_id_text_'+wp_id).html(data_arr.locator_id);
		           		$('#tube_id_'+wp_id).val(data_arr.tube_id);
		           		$('#tube_id_text_'+wp_id).val(data_arr.tube_id);


		           		$('#locator_id_'+wp_id).addClass('hidden');
		           		$('#tube_id_'+wp_id).addClass('hidden');
						$('#update_'+wp_id).addClass('hidden');

						$('#locator_id_text_'+wp_id).removeClass('hidden');
						$('#tube_id_text_'+wp_id).removeClass('hidden');
						$('#edit_'+wp_id).removeClass('hidden');
		    		}); 
				});	

			}
			function openEditForm(wp_id){
				var locator_id = 'locator_id_'+wp_id;
				var tube_id = 'tube_id_'+wp_id;
				var update = 'update_'+wp_id;
				var edit = 'edit_'+wp_id;
				var locator_id_text ='locator_id_text_'+wp_id;
				var tube_id_text = 'tube_id_text_'+wp_id;
				$('#'+locator_id).removeClass('hidden');
				$('#'+tube_id).removeClass('hidden');
				$('#'+update).removeClass('hidden');

				$('#'+locator_id_text).addClass('hidden');
				$('#'+tube_id_text).addClass('hidden');
				$('#'+edit).addClass('hidden');

			}
			 $('#select_all').click(function(){
		        var status = $(this).html();
		        if(status == 'Select all visible'){
		            $(".samples").attr("checked", true);
		            $(this).html('Unselect all visible');
		        }else{
		            $(".samples").attr("checked", false);
		            $(this).html('Select all visible');
		        }    
		    });
			 function approveSelected(type) {  
				 //set the page type
				$('#type').val(type);   
				var the_form = document.getElementById("approve_form");   
			   	the_form.submit();   
			}

		</script>

		@endsection()


