@extends('Admin.app')
@section('admin-content')
<div id="d2" class="panel panel-default">
	<div class="panel-body">
		{!! link_to('users/create','Create new user',['class'=>'btn btn-primary btn-side']) !!}
		<br><br>
		{!! Session::get('msge') !!}
		
		<table class='table table-striped table table-condensed' id='tab_id'>
		  <thead>
			<tr>
				<td>First Name</td>
				<td>Family Name</td>
				<th>Username</th>
				<th>User Role</th>
				<th>Email</th>
				<th>Telephone</th>
				<th>Deactivated</th>
				<th></th>
			</tr>
		  </thead>
		  <tbody>
			@foreach ($users AS $user)					
			<?php
			if($user->deactivated==1){
				$status=0;
				$d_label='Activate Account';
				$d_clr="style='color:red'";
			}else{
				$status=1;
				$d_label='Deactivate Account';
				$d_clr="";
			}
			echo "<tr $d_clr>";

			echo "<td>$user->other_name</td>"; 
			echo "<td>$user->family_name</td>";
			echo "<td>$user->username</td>";
			echo "<td>$user->user_role</td>";
			echo "<td>$user->email</td>";
			echo "<td>$user->telephone</td>";
			echo "<td>$user->deactive</td>";
			
			?>
			<td>
			<div class="btn-group">
				<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					Options <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>{!! link_to("users/show/$user->id","View") !!}</li>
					<li>{!! link_to("users/edit/$user->id","Edit") !!}</li>
					<li>{!! link_to("users/change_password/$user->id","Change Password") !!}</li>
					<li>{!! link_to("users/deactivate_account/$user->id/$status",$d_label) !!}</li>
				</ul>
			</div>
		    </td>
			</tr>		 
			@endforeach
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  	$('#tab_id').DataTable();
  });

</script>
@endsection

