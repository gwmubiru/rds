
@extends('Admin.app')

@section('content')

	<div id='s2' class="row">
		<div class="col-md-2">	
		 	<ul class="nav nav-pills nav-stacked">
				<li role="presentation" class="">{!! link_to('user_roles/create','Create New User Role') !!}</li>
				<li role="presentation" class="">{!! link_to('user_roles/index','List of User Roles') !!}</li>
			</ul>
			
		</div>
		<div class="col-md-9">			
			@yield('user_role_content')
		</div>
	</div>

@endsection
