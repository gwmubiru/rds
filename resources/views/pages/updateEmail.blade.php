@extends('layouts/otp')
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-success">
				<div class="panel-heading"><text class="glyphicon glyphicon-alert"></text>  Please enter your valid email address below to receive a token </div>
				<div class="panel-body">
					@if (count($errors) > 0)
						<div class="alert alert-success">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<!-- <form class="form-horizontal" role="form" method="POST" url="/auth/otp"> -->
            {!! Form::open(array('url'=>'/update/email','id'=>'form_id', 'name'=>'email_form')) !!}
						<!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->

						<div class="form-group">
              <text style="color:#337aba"></text>
							<label class="col-md-4 control-label"></label>
							<div class="col-md-12">
								<input type="text" class="form-control" name="email" placeholder="Enter your email address here">
							</div>
						</div><br><br><br>

						<div class="form-inline">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary" style="margin-left: 25px;" onclick="ValidateEmail(document.email_form.email)">
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
<script>
function ValidateEmail(inputText)
{
var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
if(inputText.value.match(mailformat))
{
document.email_form.email.focus();
return true;
}
else
{
alert("You have entered an invalid email address!");
document.email_form.email.focus();
return false;
}
}
</script>
@endsection

