@extends('Admin.app')
@section('admin-content')
<div id='d2' class="panel panel-default">
	<div class="panel-heading"><b>Changing password for {!! $user->family_name." ".$user->other_name !!}</b></div>
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(['url'=>'users/post_change_password/'.$id,'id'=>'form_id','onsubmit'=>'return chkForm(this)']) !!}

		<table class='table table-bordered'>
			<tr>
				<td class='td_label'><label for='c'>User name:</label></td>
				<td>{!! $user->username !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='d'>New Password:</label></td>
				<td>{!! Form::password('password',['class'=>'form-control','id'=>'d','required'=>1]) !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='e'>Confirm Password:</label></td>
				<td>{!! Form::password('confirm_password',['class'=>'form-control','id'=>'e','required'=>1]) !!} </td>
			</tr>
			
			<tr><td/><td>{!! MyHTML::submit('Save') !!} </td></tr>
		</table>

		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
 function chkForm(d){
 	if(d.password.value!=d.confirm_password.value){
 		alert('Password mismatch!!');
 		return false;
 	}else{
 		return true;
 	}
 	
 }
</script>
@endsection



