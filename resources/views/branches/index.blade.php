@extends('Admin.app')
@section('admin-content')
<div id="d2" class="panel panel-default">
	<div class="panel-body">
		{!! link_to('branches/create','Create new branch',['class'=>'btn btn-primary btn-side']) !!}
		<br><br>
		{!! Session::get('msge') !!}
		
		<table class='table table-striped table table-condensed' id='tab_id'>
		  <thead>
			<tr>
				<td>Name</td>
				<td>District</td>
				<th></th>
			</tr>
		  </thead>
		  <tbody>
			@foreach ($branches AS $branch)					
			
			<tr>

			<td>{{$branch->name}}</td> 
			<td>{{$branch->district}}</td>
			
			
			<td>
			<div class="btn-group">
				<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					Options <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>{!! link_to("branches/edit/$branch->id","Edit") !!}</li>
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

</script>
@endsection

