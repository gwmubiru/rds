@extends('layouts/layout')
@section('meta-title', 'RDS')

@section('content')
<div class="container">
  <div class="card card-container">
    <div class='top-part'>
      <img  src="{{ asset('images/coa2.png') }}" >
      <br><h4>Results Dispatch System</h4>
    </div>
    
    <form method="POST" action="{{ route('login') }}" class="form-signin">
      @csrf
    <span class='error'>{!! Session::get('flash_message') !!}</span>
    <input name='username' type="text" id="inputUsername" class="form-control glyphicon gyphicon-asterisk" placeholder="Username" required autofocus>
   
    <input name='password' type="password" id="inputPassword" class="form-control" placeholder="Password" required>
    
    <button class="btn btn-primary btn-lg " type="submit">Log in</button>
    </form> 
    <p style="font-size:12px;border:1px solid lightgray;margin: 10px;padding: 10px; text-align:center">Click on the link beneath this note to access a quick guide for the integration of your Laboratory Information Management System with RDS.
      <br><br>
      <a href="/uploads/rds_integration_guide.pdf" download="RDS Integration Guide">Download the integration guide from here</a><br>
      <a data-toggle="modal" id="smallButton" data-target="#districtFileUpload"
      data-attr="" title="show">
      <i class="fas fa-eye text-success  fa-lg"></i>Upload District Administrative Units
    </a>
  </p>
  <!-- small modal -->
  <div class="modal fade" id="districtFileUpload" tabindex="-1" role="dialog" aria-labelledby="districtFileUploadLabel"  aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        {!! Form::model('', array('root' => 'HatController@uploadDistrictFile', 'files' => true, 'method'=>'post')) !!}
        <div class="modal-body" id="districtFileUpload">
          <img  src="{{ asset('images/excel_icon.jpg') }}" width="25%" style=" display: block; margin-left: auto; margin-right: auto;"><h3 style="text-align:center">Upload Excel</h3></img>
          <div class="panel-body">
            <div class="col-md-12">
              {!! Form::label('uploaded_by',  'Your Names', array('class' =>'col-md-12 ')) !!}
              {!! Form::text('uploaded_by', old('uploaded_by'), array('class' => 'form-control col-sm-12')) !!}
            </div><br><br><br><br>

            <div class="col-md-12">
              {!! Form::label('uploader_email', 'Email', array('class' =>'col-md-12 ')) !!}
              {!! Form::text('uploader_email', old('uploader_email'), array('class' => 'form-control col-sm-12')) !!}
            </div>
          </div>

            <p style="font-size:12px;border:1px solid lightgray;margin: 10px;padding: 10px; text-align:center; width:50%; display: block; margin-left: auto; margin-right: auto;">
              <tr class='even'>
                <td colspan='1'>Select file to upload:</td>
                <input type="file" name="import_file" class="form-control" accept=".csv,.xlsx,.xls">
                <!-- <td colspan='2'>{!! Form::file("file", $attributes = array()) !!}</td> -->
              </tr>
              <br>
                  <button class="btn btn-success btn-md " type="submit">Upload</button>
                  
            </p>
          {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--
  <a href="#" class="forgot-password">
    Forgot the password?
  </a> -->
</div><!-- /card-container -->
</div><!-- /container -->


<style type="text/css">
  /*
  * Specific styles of signin component
  */
  /*
  * General styles
  */
  body, html {
    height: 100%;
    background-repeat: no-repeat;
    background-color: #BDBDBD;
  }

  .card-container.card {
    max-width: 400px;
    padding: 40px 40px;
    border-radius: 10px;
  }


  /*
  * Card component
  */
  .card {
    background-color: #F7F7F7;
    /* just in case there no content*/
    padding: 20px 25px 30px;
    margin: 0 auto 25px;
    margin-top: 50px;
    /* shadows and rounded borders */
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
  }


  .form-signin #inputUsername,
  .form-signin #inputPassword {
    direction: ltr;
    height: 44px;
    font-size: 16px;
  }

  .form-signin .input-group-addon{
    margin-bottom: 10px;
    height: 40px;
  }

  .form-signin input[type=email],
  .form-signin input[type=password],
  .form-signin input[type=text],
  .form-signin button {
    width: 100%;
    display: block;
    margin-bottom: 10px;
    z-index: 1;
    position: relative;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
  }

  .form-signin .form-control:focus {
    border-color: rgb(104, 145, 162);
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
  }

  .forgot-password {
    color: rgb(104, 145, 162);
  }

  .forgot-password:hover,
  .forgot-password:active,
  .forgot-password:focus{
    color: rgb(12, 97, 33);
  }

  .top-part{
    text-align: center;
  }
</style>

<script>
  // display a modal (small modal)
  $(document).on('click', '#smallButton', function(event) {
    event.preventDefault();
    let href = $(this).attr('data-attr');
    $.ajax({
      url: href,
      beforeSend: function() {
        $('#loader').show();
      },
      // return the result
      success: function(result) {
        $('#districtFileUpload').modal("show");
        // $('#smallBody').html(result).show();
      },
      complete: function() {
        $('#loader').hide();
      },
      error: function(jqXHR, testStatus, error) {
        console.log(error);
        alert("Page " + href + " cannot open. Error:" + error);
        $('#loader').hide();
      },
      timeout: 8000
    })
  });
</script>

@stop

