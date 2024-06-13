<!DOCTYPE html>
<html>

<style>
table, th, td {
	border: 1px solid gray;
	padding-top: -13px;
}
</style>
<font size="3" face="Courier New" >

			<?php
			$wID = $ww[0]['attributes']['worksheet_id'];
			$qry = "select worksheet_number from worksheets where id = ".$wID;
			$wn = \DB::select($qry);
			?>

	Abbott WorkSheet No.
	<?php
	foreach ($wn as $key => $value) {
		echo $value->worksheet_number;
	}
	?>

	<table class="table table-bordered" width="100%">

		<?php $cols = 6; ?>
		<?php $rows = 0; ?>
		<?php $cell_count = $rows; ?>

		@foreach($ww as $value)

		<?php 	$rows++;  ?>
		<?php 	$cell_count++; ?>

		<td>
			<div align='right' style="margin: 0.1em;  padding-right:0.5em;">
				<small style="color:#337abe">{{ $cell_count }}</small>
			</div>

			<div style="margin:0.25em;">
				<?php echo \DNS1D::getBarcodeHTML($value->tube, "C128A", 1, 79);?>
				{{$value->tube}}
			</div>

		</div>
	</td>

	@if($rows === $cols)
</tr>
<tr>
	<?php $rows = 0; ?>
	@endif
	@endforeach

</table>

</body>
</html>

<!--


			¯\_(ツ)_/¯
It works on my machine


28/Jul/2020 0048hrs
-->
