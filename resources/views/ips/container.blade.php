
@extends('Admin.app')

@section('content')

	<div id='s5' class="row">
		<div class="col-md-2">	
		 	<ul class="nav nav-pills nav-stacked">
				<li role="presentation" class="">{!! link_to('ips/create','Create New IP') !!}</li>
				<li role="presentation" class="">{!! link_to('ips/index','List of IPs') !!}</li>
			</ul>
			
		</div>
		<div class="col-md-9">			
			@yield('ip_content')
		</div>
	</div>

@endsection
