
@extends('Admin.app')

@section('content')

	<div id='s6' class="row">
		<div class="col-md-2">	
		 	<ul class="nav nav-pills nav-stacked">
				<li role="presentation" class="">{!! link_to('facilities/create','Create New Facility') !!}</li>
				<li role="presentation" class="">{!! link_to('facilities/index','List of Facilities') !!}</li>
			</ul>
			
		</div>
		<div class="col-md-9">			
			@yield('facility_content')
		</div>
	</div>

@endsection
