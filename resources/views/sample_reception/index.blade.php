@extends('layouts/layout')
@section('content')

<div id="d2" class="panel panel-default">
	<div class="panel-body">
		{!! link_to('/sample_reception/create','Accession Samples',['class'=>'btn btn-primary btn-side']) !!}
		<br><br>
		{!! Session::get('msge') !!}

		<table class="table table-responsive-sm table-striped table-bordered table-sm" id="tab_id">
			<thead>
				<tr>
					<td>#</td>
					<td>Package ID</td>
					<td>Date Received</td>
					<th></th>
				</tr>
			</thead>
			<tbody>

				<?php $row=1; ?>
				@foreach ($list_of_samples AS $list)
				<td>{{$row}}</td>
				<td>{{$list->barcode}}</td>
				<td>{{$list->created_at	}}</td>

				<td>
				<div class="btn-group">
				<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				Options <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
				<li>{!! link_to("/sample_reception/view/$list->id","View") !!}</li>
				<li>{!! link_to("/sample_reception/edit/$list->id","Edit") !!}</li>
				</ul>
				</div>
				</td>
				</tr>
					<?php $row++; ?>
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
