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
	<p>Package ID::
			<?php	foreach ($query as $value) {}	echo "<b>". $value->barcode ."</b>";	?> </p>
	<table class="table table-bordered" width="100%">
		<thead>
		<tr>
		<th>#</th>
		<th>Locator ID</th>
		<th>Priority Level</th>
	</tr>
</thead>
<tbody>
	<?php $row=1; ?>
	@foreach($query as $value)
	<tr>
		<td>{{ $row }}</td>
		<td>{{ $value->specimen_ulin }}</td>
		<td><?php echo $value->priority == 0 ? "LOW" : "<b style='color:red;'>".'HIGH'."</b>";  ?></td>

	</tr>
	<?php $row++; ?>
	@endforeach
</tbody>
</table>

</body>
</html>
