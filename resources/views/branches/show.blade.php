@extends('Admin.app')
@section('admin-content')
<div id="d2" class="panel panel-default">
	<div class="panel-heading"><b>Details of {!! $user->family_name." ".$user->other_name !!}</b></div>
	<div class="panel-body">
		{!! Session::get('msge') !!}

		<table class='table table-bordered'>
			<tr>
				<td class='td_label' width='20%'><label >First Name:</label></td>
				<td>{!! $user->other_name !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label >Family Name:</label></td>
				<td>{!! $user->family_name !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label >User name:</label></td>
				<td>{!! $user->username !!} </td>
			</tr>
			
			<tr>
				<td class='td_label'><label >User role:</label></td>
				<td>{!! $user->user_role !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Email:</label></td>
				<td>{!! $user->email !!} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Signature:</label></td>
				<td><img src="{{ asset($user->signature) }}" height="80" width="150" alt="signature_image"></td>
			</tr>
			<tr>
				<td class='td_label'><label>Telephone:</label></td>
				<td>{!! $user->telephone !!} </td>
			</tr>
			<tr class="hidden">
				<td class='td_label'><label>Limited by:</label></td>
				<td>
					<?php
					if(!empty($user->facility)) echo "<u>Facility</u>: $user->facility";
					if(!empty($user->hub)) echo "<u>Hub</u>: $user->hub";
					if(!empty($user->ip)) echo "<u>IP</u>: $user->ip";
					?>
				</td>
			</tr>
			<tr><td/><td>{!! MyHTML::link_to("users/edit/".$user->id,"Edit","btn btn-primary") !!} </td></tr>
		</table>
	</div>
</div>
@endsection



