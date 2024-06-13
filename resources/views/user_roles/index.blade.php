@extends('Admin.app')
@section('admin-content')
	<div id='d1' class='panel panel-default'>
	 <div class='panel-body'>	
		{!! link_to('user_roles/create','Create new user role',['class'=>'btn btn-primary btn-side']) !!}
		<br><br>
		<table class='table table-striped table table-condensed' id='tab_id'>
		  <thead>
			<tr>
				<th>Role</th>
				<th>Permissions</th>
				<th>Users</th>
				<th width='8%'></th>
				<!-- <th>Permissions</th> -->
				<!-- <th width='8%' /> -->
				
			</tr>
		  </thead>
		  <tbody>
			@foreach ($user_roles AS $user_role)		 
			<tr>
			<?php 
			echo "<td>$user_role->description</td>";
			$role_perms=unserialize($user_role->permissions);
			$perm_str="";
			foreach ($role_perms as $rperm) {
				$perm_str.=$perms_list[$rperm]."<br>";
			}

			$users_str="";
			$role_users=[];
			if(array_key_exists($user_role->id, $role_users_arr)){
				$role_users=$role_users_arr[$user_role->id];
			} 
			foreach ($role_users as $r_user) {
				$users_str.=$r_user."<br>";
			}

			if(empty($users_str)) $users_str="No users attached to role yet";
			//echo "<td>$perm_str</td>";
			//echo "<td>$user_role->permissions</td>";
			// echo "<td></td>";
			
			/*echo "<td>".link_to("user_roles/show/$user_role->id","View")."";
			echo " | ".link_to("user_roles/edit/$user_role->id","Edit")."</td>";*/
		
			?>
			<td>
				<a class="blue-link" onclick="showHide(this,'block','#perm{!! $user_role->id !!}')"> show</a>
				<p style="display:none" id='perm{!! $user_role->id !!}'>{!! $perm_str !!}</p>
			</td>
			<td>
				<a class="blue-link" onclick="showHide(this,'block','#r_users{!! $user_role->id !!}')"> show</a>
				<p style="display:none" id='r_users{!! $user_role->id !!}'>{!! $users_str !!}</p>
			</td>
			<td>
				<div class="btn-group">
					<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options <span class="caret"></span></button>
					<ul class="dropdown-menu" role="menu">
						<li>{!! link_to("user_roles/show/$user_role->id","View") !!}</li>
						<li>{!! link_to("user_roles/edit/$user_role->id","Edit") !!}</li>
						<?php //<li>{!! link_to("user_roles/deactivate_user_role/$user_role->id/$status",$d_label) !!}</li> ?>
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

function showHide(that,display,sect){

	$(sect).attr('style','display:'+display);
	if(display=='block'){ 
		that.setAttribute('onclick',"showHide(this,'none','"+sect+"')");
		that.innerHTML='hide';
	}else{
		that.setAttribute('onclick',"showHide(this,'block','"+sect+"')");
		that.innerHTML='show';
	}


}
</script>
@endsection

