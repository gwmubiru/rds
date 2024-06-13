@extends('Admin.app')
@section('admin-content')
<div id="d4" class="panel panel-default">
	<div class="panel-body">
		@if(is_object($ip))
		{!! Session::get('msge') !!}
		<table class='table borderless'>
			<tr>
				<td class='td_label' width='20%'><label >IP:</label></td>
				<td>{{ $ip->ip }} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Full Name:</label></td>
				<td>{{ $ip->full_name }} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Address:</label></td>
				<td>{{ $ip->address }} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Focal Person:</label></td>
				<td>{{ $ip->focal_person }} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Focal Person Contact:</label></td>
				<td>{{ $ip->focal_person_contact }} </td>
			</tr>

			<tr>
				<td class='td_label'><label>Description:</label></td>
				<td>{{ $ip->description }} </td>
			</tr>

			<tr>
				<td class='td_label'><label>Funding Source:</label></td>
				<td>{{ $ip->funding_source }} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Email:</label></td>
				<td>{{ $ip->email }}</td>
			</tr>
			<tr><td/><td>{!! MyHTML::link_to("ips/edit/".$ip->id,"Edit","btn btn-primary") !!} </td></tr>
		</table>
		@endif
		<?php //var_dump($ip) ?>
	</div>
</div>

@endsection

