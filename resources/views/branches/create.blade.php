@extends('Admin.app')
@section('admin-content')
<link   href="{{ asset('/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('/js/select2.min.js') }}" type="text/javascript"></script>
<div id="d2" class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(['url'=>'branches/store','id'=>'form_id']) !!}

		<table class='table table-bordered'>			
			<tr>
				<td class='td_label'><label for='b'> Name:</label></td>
				<td>{!! Form::text('name',$branch? $branch->name: old('last_name'),['class'=>'form-control','id'=>'name','required'=>1]) !!} </td>
			</tr>
			
			<tr>
				<td class='td_label'><label for='user_r'>User role:</label></td>
				<td>
					@if($branch)
					{!! Form::select('district_id',[""=>""]+$distr_arr,$branch->district_id,['id'=>'district_id','required'=>1]) !!}
					@else
					{!! Form::select('district_id',[""=>""]+$distr_arr,"",['id'=>'district_id','required'=>1]) !!}					
					@endif </td>
			</tr>
			
			<tr><td></td><td>{!! MyHTML::submit('Submit') !!}  </td></tr>
		</table>
		{!!Form::hidden('id',$branch? $branch->id:'')!!}
		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">


 $(document).ready(function() {
 	document.getElementById('name').focus();
 	$("#district_id").select2({	placeholder:"Select branch", allowClear:true, width: '40%' });
 });

</script>
@endsection



