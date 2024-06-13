@extends('Admin.app')
@section('admin-content')
<div id='d1' class="panel panel-default">
	<div class="panel-heading"><b>User Role:</b> {!! $user_role->description !!} </div>
	<div class="panel-body">
		{!! Session::get('msge') !!}
		<br>
		<div style="text-align: center;">
			{!! link_to("user_roles/edit/$user_role->id","Edit User role",["class"=>"btn btn-primary"]) !!}
		</div>
		<div class="row">
		<div class="col-md-6">
		<?php 
		extract($perm_arr); 
		$role_perms=unserialize($user_role->permissions);
		//print_r($role_perms);
		?>
		@foreach( $perm_parents AS $p_id => $desc)
		@if($p_id==5)  </div><div class="col-md-6"> @endif
		 <u><b>{!! $desc !!}</b></u>
		  @foreach( $perm_children[$p_id] AS $id => $child_desc)
		  {{ $status="" }}
		  @if( in_array($id, $role_perms)) <?php  $status="checked" ?> @endif
		  <div class='checkbox disabled'><label><input class="perms perms{!! $p_id !!}" type='checkbox' name="role_permissions[]" value="{!! $id !!}" disabled {!! $status !!}> {!! $child_desc !!}</label></div>
		  @endforeach
		  <br>
		@endforeach
	    </div>
		</div>
		<div style="text-align: center;">
			{!! link_to("user_roles/edit/$user_role->id","Edit User role",["class"=>"btn btn-primary"]) !!}
		</div>
	</div>
</div>
@endsection