
@extends('Admin.app')

@section('content')

	<div id='s3' class="row">
		<div class="col-md-2">	
		 	<ul class="nav nav-pills nav-stacked">
				<li role="presentation" class="">{!! link_to('users/create','Create New User') !!}</li>
				<li role="presentation" class="">{!! link_to('users/index','List of Users') !!}</li>
			</ul>
			
		</div>
		<div class="col-md-9">			
			@yield('user_content')
		</div>
	</div>

@endsection