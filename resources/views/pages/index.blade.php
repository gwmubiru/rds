@extends('layouts/layout')

@section('content')

<div class="starter-template">
  <h1>{{ Auth::check() ? "Welcome, " . Auth::user()->username : "Welcome RDS" }}</h1>

  <br>
  <div class='row'>

    @if(session('is_admin')==1 || App\Closet\MyHTML::is_eoc() || App\Closet\MyHTML::is_facility_admin() || App\Closet\MyHTML::is_district_user() && \Auth::user()->district_id != 139 || App\Closet\MyHTML::is_facility_dlfp_user())
<div class='col-lg-2'><div class="home-links home" onclick="go('admin')"><br><span style="font-size:55px" class="glyphicon glyphicon-cog"></span><br><br>SYSTEM ADMIN</div></div>

    <div class='col-lg-1'><br></div>
@endif

  @if((App\Closet\MyHTML::is_ref_lab() && !App\Closet\MyHTML::is_facility_admin()) &&  !App\Closet\MyHTML::restrictedAccess() &&  !App\Closet\MyHTML::is_integrated() || App\Closet\MyHTML::is_rdt_site_user() || App\Closet\MyHTML::is_facility_dlfp_user() || App\Closet\MyHTML::is_cphl_lab())
   <div class='col-lg-2'><div class="home-links home" onclick="go('outbreakrlts?type=form')"><br><span style="font-size:55px" class="glyphicon glyphicon-cog"></span><br><br>UPLOAD RESULTS</div></div>

    <div class='col-lg-1'><br></div>@endif
	     @if(!App\Closet\MyHTML::data_analyst() && !App\Closet\MyHTML::is_moh() && !App\Closet\MyHTML::is_sample_archival_user() && !App\Closet\MyHTML::is_facility_admin() && !App\Closet\MyHTML::restrictedAccess() && !App\Closet\MyHTML::permit(46))
    <div class='col-lg-2'><div class="home-links home" onclick="go('outbreaks/list?type=MQ==&printed=2')"><br><span class="glyphicon glyphicon-folder-open home-glyphicon"></span><br><br>RESULTS MGT</div></div>
    @endif
    <div class='col-lg-1'><br></div>
     @if(App\Closet\MyHTML::isAuditUser())
    <div class='col-lg-2'><div class="home-links home" onclick="go('audit?type=audit')"><br><span class="glyphicon glyphicon-sort-by-alphabet home-glyphicon"></span><br><br>AUDIT TRAILS</div></div>
    @endif

  </div>
  @if(App\Closet\MyHTML::permit(22))
  <br><br>
  <div class="row">
     <div class='col-lg-2'><div class="home-links home" onclick="go('outbreaks/list?type=MQ==&printed=2&is_update=1')"><br><span class="glyphicon glyphicon-folder-open home-glyphicon"></span><br><br>UPDATE RESULTS</div></div>
  </div>
  <br><br>
  <div class="row">
     <div class='col-lg-2'><div class="home-links home" onclick="go('Logistics')"><br><span class="glyphicon glyphicon-save home-glyphicon"></span><br><br>LOGISTICS REPORTING</div></div>
  </div>
  @endif
  <br><br>
@if(session('is_admin')==1 || App\Closet\MyHTML::is_evd_user() || App\Closet\MyHTML::is_classified_user() || App\Closet\MyHTML::isSpecialUser() || \Auth::user()->id == 4321)
  <div class="row">
     <div class='col-lg-2'>
<div class="home-links home" onclick="go('evd')"><br><span class="glyphicon glyphicon-equalizer home-glyphicon"></span><br><br>EVD
</div>
</div>
  </div>
  @endif
  @if(App\Closet\MyHTML::permit(25))
  <br><br>
  <div class="row">
     <div class='col-lg-2'><div class="home-links home" onclick="go('hat')"><br><span class="glyphicon glyphicon-floppy-disk home-glyphicon"></span><br><br>H.A.T Data</div></div>
  </div>
  @endif

 @if(App\Closet\MyHTML::data_analyst() || App\Closet\MyHTML::is_moh() || App\Closet\MyHTML::is_sample_archival_user() || App\Closet\MyHTML::sero_survey_user() || session('is_admin')==1)
  <br><br>
  <div class="row">
     <div class='col-lg-12'>
           {!! Form::open(array('url'=>'/outbreaks/export_data','id'=>'export_to_csv')) !!}
       <div class="well firstrow list">

          <div class="row">
            <div class="col-md-12">
             <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                  <label for="exp_fro">Tested between:</label>
                  <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="exp_fro" type="text" id="exp_fro">
                  <label for="exp_to">and </label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="exp_to" type="text" id="exp_to">


                  <input type="submit" value="Export to CSV" class="btn btn-primary btn-sm" style="margin-top: 5px;">

              </div>
              </div>
          </div>
        </div>

        {!! Form::close() !!}
     </div>
  </div>
  @endif

</div>



<script type="text/javascript">
$(".standard-datepicker-nofuture").datepicker({
      dateFormat: "yy-mm-dd",
      maxDate: 0
    });
function go(url){

  return window.location.assign(url);

}

</script>

@stop
