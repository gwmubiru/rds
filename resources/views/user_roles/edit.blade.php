@extends('Admin.app')
@section('admin-content')
<div id='d1' class="panel panel-default">
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>'user_roles/update/'.$user_role->id,'id'=>'form_id')) !!}

		{!! Form::text('description',$user_role->description,['class'=>'form-control','id'=>'a','placeholder'=>'Enter name of the role','required'=>'true']) !!}
		<br>
		<div style="text-align: center;">
			<label onclick="selectAll(this,true,'.perms')" class="btn btn-primary "> Select all</label>
			{!! MyHTML::submit('Update User Role') !!} 
		</div>
		<br>
		<div class="row">
		<div class="col-md-6">
		<?php 
		extract($perm_arr);
		$role_perms=unserialize($user_role->permissions);
		 ?>
		@foreach( $perm_parents AS $p_id => $desc)
		@if($p_id==5)  </div><div class="col-md-6"> @endif
		 <u><b>{!! $desc !!}</b></u>
		 <br> <br><a class="blue-link" onclick="selectAll(this,true,'.perms{!! $p_id !!}')">Select all</a>
		  @foreach( $perm_children[$p_id] AS $id => $child_desc)
		  <?php 
		  $status="";
		  if(in_array($id, $role_perms)) $status="checked";
		   ?>
		  <div class='checkbox'>
		  	<label>
		  		<input class="perms perms{!! $p_id !!}" type='checkbox' name="role_permissions[]" value="{!! $p_id.'_'.$id !!}" {!! $status !!}> {!! $child_desc !!}
		  	</label>
		  </div>
		  @endforeach
		  <br>
		@endforeach
	    </div>
		</div>
		<div style="text-align: center;">{!! MyHTML::submit('Update User Role') !!} </div>

		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
function selectAll(that,check,clss){
	var items=document.querySelectorAll(clss);
	for(var i in items){
		items[i].checked=check;
    }
    if(check==true){
    	that.innerHTML="Unselect All";
    	that.setAttribute('onClick',"selectAll(this,false,'"+clss+"')");
    }else{
    	that.innerHTML="Select All";
    	that.setAttribute('onClick',"selectAll(this,true,'"+clss+"')");
    }

}
</script>
@endsection