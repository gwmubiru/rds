@extends('Admin.app')
@section('admin-content')
<div id="d4" class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(['enctype'=>'multipart/form-data','url'=>'ips/store','id'=>'form_id','onsubmit'=>'return chkForm(this)']) !!}

		<table class='table table-bordered'>
			<tr>
				<td class='td_label' width='20%'><label for='a'>IP:</label></td>
				<td>{!! Form::text('ip','',['class'=>'form-control','id'=>'a','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='b'>Full Name:</label></td>
				<td>{!! Form::text('full_name','',['class'=>'form-control','id'=>'b','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='c'>Address:</label></td>
				<td>{!! Form::text('address','',['class'=>'form-control','id'=>'c', 'required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='d'>Focal Person:</label></td>
				<td>{!! Form::text('focal_person','',['class'=>'form-control','id'=>'d', 'required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='e'>Focal Person Contact:</label></td>
				<td>{!! Form::text('focal_person_contact','',['class'=>'form-control','id'=>'e', 'required'=>1]) !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='f'>Description:</label></td>
				<td>{!! Form::text('description','',['class'=>'form-control','id'=>'f', 'required'=>1]) !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='g'>Funding Source:</label></td>
				<td>{!! Form::text('funding_source','',['class'=>'form-control','id'=>'g', 'required'=>1]) !!} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='h'>Email:</label></td>
				<td>{!! Form::email('email','',['class'=>'form-control','id'=>'h', 'required'=>1]) !!} </td>
			</tr>
		
			<tr><td/><td>{!! MyHTML::submit('Save') !!} {!! MyHTML::submit('Save & Create new IP','btn btn-primary','create_new') !!} </td></tr>
		</table>

		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
 function chkForm(d){
 	return true; 	
 }
</script>
@endsection



