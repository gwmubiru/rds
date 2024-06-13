@extends('layouts/otp')
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-primary">

				<div class="panel-heading"><text class="glyphicon glyphicon-alert"></text><text style="font-size:15px;"> Enter the token you have received on your registered email address: <text style="font-size:20px;color:yellow"><b> {{$email}} </b></text> </text>
				</div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<!-- <form class="form-horizontal" role="form" method="POST" url="/auth/otp"> -->
            {!! Form::open(array('url'=>'/auth/otp','id'=>'form_id')) !!}
						<!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->

						<div class="form-group">
              <text style="color:#337aba"></text>
							<label class="col-md-4 control-label"></label>
							<div class="col-md-12">
								<input type="text" class="form-control" name="otp" placeholder="Enter the token you received here">
							</div>
						</div><br><br><br>

						<div class="form-inline">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-left: 25px;">
									Submit
								</button>

								<a type="submit" class="btn btn-danger" href="/logout" style="margin-left: 25px;">
									Cancel
								</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

