@extends('layouts/layout')
@if($page_type == 'approved')
	@section('page_title', 'Intervention Window - Approved Results')
@else
	@section('page_title', 'Intervention Window - Rescheduled Results')
@endif

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
	<div id='page_title' class="panel panel title">
            @yield('page_title')
        </div>
       	<div class="alert alert-info fade in alert-dismissible">
		    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
		    <strong>Info!</strong> <br>
		    @if($page_type == 'approved')
		    	This page contains approved results which have not yet been synced to RDS.  You can reverse the approval. A reversed result is no longer part of those which can be synced to RDS,  so it can be rescheduled or can receive a new result
		    @else
		    This page contains all rescheduled results. You can approve it (if it was unintentionally rescheduled).
		    @endif
		</div>
	<ul class="breadcrumb">	<li><a href="/">HOME</a></li></ul>

	<div id='d3' class="panel panel-default">

		<div class="panel-body">

			<div class="panel-body">
				{!! Form::open(array('url'=>'/outbreaks/release_retain','id'=>'approve_form', 'name'=>'approve_form')) !!}
				<input type="text" name="type" class="hidden" id="type">
				<a href="#" class="btn btn-xs btn-primary " id="select_all">Select all visible</a>
       			@if($page_type == 'approved')
          		<input class="btn  btn-xs btn-success" name="pdf" type="button" value="Reverse all selected" onclick="approveSelected('reverse');">
       			@else
       				<input class="btn  btn-xs btn-danger" name="pdf" type="button" value="Approve all selected" onclick="approveSelected('approve');">
       			@endif
        		
        		 
					<table class="table table-bordered" width="100%">
					 <thead>
							<tr>
							<th>#</th>
							<th></th>
							<th>Sample ID</th>
							<th>Sample Locator ID</th>
							<th>Worksheet Locator ID</th>
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
							<td><input type="checkbox" class="samples" name="worksheet_sample_ids[]" value="{{$value->worksheet_sample_id}}"></td>
							<td>{{ $value->sample_id }}</td>
							<td>{{ $value->specimen_ulin}}</td>
							<td><span id="locator_id_text_{{$value->worksheet_sample_id}}">{{ $value->locator_id}}</span>
								<input type="text" name="worsheet_samples_{{$value->worksheet_sample_id}}" value="{{$value->locator_id}}" id="locator_id_{{$value->worksheet_sample_id}}" class="samples form-control hidden"></td>
							<td><span id="tube_id_text_{{$value->worksheet_sample_id}}">{{ $value->tube_id}}</span>
								<input type="text" name="tube_{{$value->worksheet_sample_id}}" value="{{$value->tube_id}}" id="tube_id_{{$value->worksheet_sample_id}}" data-sampleid = "{{$value->sample_id}}" class="samples form-control hidden"></td>
							</td>
							<td>{{ $value->result1 }}</td>
							<td>{{ $value->result2 }}</td>
							<td>
								
								@if($page_type == 'approved')
									<a href="{{url('/outbreaks/release_retain?type=reverse&id='.$value->id)}}" class="btn btn-sm btn-success"><i class="fa fa-edit"></i>Reverse</a>
								@else
								 	<a href="{{url('/outbreaks/release_retain?type=approve&id='.$value->id)}}" class="btn btn-sm btn-danger"><i class="fa fa-edit"></i>Approve</a>
							 	@endif
							</td>

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


