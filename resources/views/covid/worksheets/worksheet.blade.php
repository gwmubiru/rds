<!DOCTYPE html>
<html>

<style>
table, th, td {
	border: 1px solid gray;
	padding-top: -13px;
	/* font-size:px; color:red; */
}
</style>
<font size="3" face="Courier New" >
	<table class="table table-bordered" width="100%">
		<!-- <thead>
		<tr>
		<th>#</th>
		<th>Locator ID</th>
		<th>Tube ID </th>
		<th>Has Sample </th>
	</tr>
</thead> -->
<tbody>
	<?php $row=1; ?>
	@foreach($ww as $value)
	<tr>
		<td>{{ $row }}</td>
		<td>{{ $value->locator_id}}</td>
		<td>{{ $value->tube}}</td>
		<!-- <td>{{ $value->assigned_sample == 0 ? "NO" : "YES" }}</td> -->

	</tr>
	<?php $row++; ?>
	@endforeach
</tbody>
</table>

</body>
</html>
