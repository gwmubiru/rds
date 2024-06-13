<div id="the_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <!-- <h4 class="modal-title" style="color:red">Worksheet Details</h4> -->
      </div>

        <!-- {!! Form::open(array('url'=>'/new/worksheet','id'=>'worksheet_detail', 'name'=>'worksheet_d','class'=>'form-horizontal')) !!} -->
      <div class="modal-body">

        <table class="table table-responsive-sm table-striped table-bordered table-sm" id="tab_id">
		  <thead>
			<tr>
				<td>Package ID</td>
				<td>Date created</td>
				<th></th>
			</tr>
		  </thead>
		  <tbody>
        <?php $xx = Covid::get(); ?>
			@foreach (xx AS $list)
			<?php
			echo "<td>$list->barcode</td>";
			echo "<td>$list->created_at	</td>";

			?>
			<td>
			<div class="btn-group">
				<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					Options <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>{!! link_to("users/show/$list->id","View") !!}</li>
				</ul>
			</div>
		    </td>
			</tr>
			@endforeach
			</tbody>
		</table>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Cancel</button>
      </div>
      <!-- {!! Form::close() !!} -->
    </div>
  </div>
</div>
