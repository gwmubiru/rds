<div class="modal fade" id="worksheets" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Worksheet details</h4>
			</div>
			<div class="modal-body">

				<div class="row">
					<div class="panel-body">
						<th class="text:center" style="color:#337ab7">  worksheet No: {!! Form::text('worksheet_number', $d.old('worksheet_number'), array('class' => 'form-control col-sm-4')) !!}</th>
						<table class="table table-bordered" id="samples">
							<tr>
								<th class="text:center" style="color:#337ab7">#</th>
								<th class="text:center" style="color:#337ab7">Locator ID</th>
								<th class="text:center" style="color:#337ab7">Tube ID</th>
							</tr>
							@foreach($worksheets as $key => $value)
							<tr>
								<td style="color:#337ab7">{!! $value->worksheet_number !!}</td>
								<td style="color:#337ab7">{!! $value->locator_id !!}</td>
								<td style="color:#337ab7">{!! $value->tube_id !!}</td>

							</tr>
							@endforeach
						</table>
					</div>

				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>



<script>

$('#worksheet').on('show.bs.modal', function (event) {

	var button = $(event.relatedTarget)
	var wn = button.data('worksheet_number')
	var li = button.data('locator_id')
	var ti = button.data('tube_id')

	var modal = $(this)

	modal.find('.modal-body #worksheet_number').val(wn);
	modal.find('.modal-body #locator_id').val(li);
	modal.find('.modal-body #tube_id').val(ti);
})
</script>
