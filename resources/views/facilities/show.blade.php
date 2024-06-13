@extends('Admin.app')
@section('admin-content')
<div id='d5' class="panel panel-default">
	<div class="panel-body">

		@if(is_object($facility))
		{!! Session::get('msge') !!}
		<table class='table borderless'>
			<tr>
				<td class='td_label' width='20%'><label for='a'>Facility Code:</label></td>
				<td>{{ $facility->facilityCode }} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='b'>Facility Name:</label></td>
				<td>{{ $facility->facility }} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='c'>Facility Level:</label></td>
				<td>{{ $facility->facility_level }} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='d'>District:</label></td>
				<td>{{ $facility->district }} </td>
			</tr>
			<tr>
				<td class='td_label'><label for='e'>Hub:</label></td>
				<td>{{ $facility->hub }} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='f'>Phone:</label></td>
				<td>{{ $facility->phone }} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='g'>Email:</label></td>
				<td>{{ $facility->email }} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='h'>Physical Address:</label></td>
				<td>{{ $facility->physicalAddress }} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='i'>Return Address:</label></td>
				<td>{{ $facility->returnAddress }} </td>
			</tr>

			<tr>
				<td class='td_label'><label for='j'>Contact Person:</label></td>
				<td>{{ $facility->contactPerson }} </td>
			</tr>
			<tr>
				<td class='td_label'><label>Supporting IP(s)</label></td>
				<td>
					@foreach($facility_ips AS $f_ip)
					<p>
						{{ $f_ip->ip }} Started on {!! MyHTML::localiseDate($f_ip->start_date) !!}
						<?php  if($f_ip->stopped == 1) echo "Stopped on ".MyHTML::localiseDate($f_ip->stop_date) ?>

					</p>
					@endforeach
				</td>
			</tr>
			<tr><td/><td>{!! MyHTML::link_to("facilities/edit/".$facility->id,"Edit","btn btn-primary") !!} </td></tr>
		</table>
		@endif
		<?php //var_dump($facility) ?>
	</div>
</div>

@endsection

