@extends('Admin.app')
@section('admin-content')
<?php
$links=[];
if(session('is_admin')==1){
	$links[]=['url'=>'/user_roles/index','lbl'=>'Manage User Roles','icon'=>'tag'];
}
$links[]=['url'=>'/users/index','lbl'=>'Manage Users','icon'=>'user'];
if(session('is_admin')==1){
	$links[]=['url'=>'/ips/index','lbl'=>'Manage IPs','icon'=>'asterisk'];
	$links[]=['url'=>'/facilities/index','lbl'=>'Manage Facilities','icon'=>'header'];
	$links[]=['url'=>'/locations/regions','lbl'=>'Manage Regions','icon'=>'globe'];
	$links[]=['url'=>'/locations/districts','lbl'=>'Manage Districts','icon'=>'move'];
	$links[]=['url'=>'/locations/hubs','lbl'=>'Manage Hubs','icon'=>'screenshot'];
}
//$links[]=['url'=>'#','lbl'=>'View User Logs','icon'=>'eye-open'];
?>

<div class="panel panel-default">
	<div class="panel-heading">System Administration</div>
	<div class="list-group">
		@foreach ($links as $link)
		<a href="{!! $link['url'] !!}" class="list-group-item"><span class="blue-icon-md glyphicon glyphicon-{!! $link['icon'] !!}"></span> {!! $link['lbl'] !!} </a>
		@endforeach
	</div>
</div>
@endsection