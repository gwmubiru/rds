
@extends('Admin.app')

@section('content')

	<div id='s7' class="row">
		<div class="col-md-2">	
		 		
			<ul class="nav nav-pills nav-stacked">
				<li role="presentation" class="">{!! link_to('locations/regions','Regions') !!}</li>
				<li role="presentation" class="">{!! link_to('locations/districts','Districts') !!}</li>
				<li role="presentation" class="">{!! link_to('locations/hubs','Hubs') !!}</li>				
			</ul>
		</div>
		<div class="col-md-9">			
			@yield('location_content')
		</div>
	</div>

@endsection
