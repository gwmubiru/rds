@if (Auth::guest())
@else
<style >
    .navbar-default .navbar-brand {
        color: #9d9d9d;
    }
    .navbar-default .navbar-brand:hover {
        color: #fff;
    }

    .dropdown-toggle .navbar-nav .open .dropdown-menu>li>a {
        color: #fff;
    }


</style>
<nav class="navbar navbar-default navbar-fixed-top navbar-custom">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"> <span class='glyphicon glyphicon-home'></span> RDS</a>
        </div>

        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">

	  @if(!App\Closet\MyHTML::restrictedAccess())
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Covid Results <span class="caret"></span></a>
              <ul class="dropdown-menu">
                                    <!-- EOC list for approved results-->
                    <li class="hidden">{!! App\Closet\MyHTML::anchor("/outbreaks/list?type=".base64_encode(0),"Pending Release",22) !!}</li>
                    <!-- EOC list for approved results-->

                    <li>{!! App\Closet\MyHTML::anchor("/outbreaks/list?type=MQ==&printed=2","All Results",'can_print_results') !!}</li>
                    <li>{!! App\Closet\MyHTML::anchor("/outbreaks/list?type=MQ==&printed=0","Pending Printing",'can_print_results') !!}</li>
                     <li>{!! App\Closet\MyHTML::anchor("/outbreaks/list?type=MQ==&printed=1","Printed",'can_print_results') !!}</li>
                     <li>{!! App\Closet\MyHTML::anchor("/outbreaks/list?type=MQ==&printed=2&is_update=1","Update Results",'edit_results') !!}</li>
              </ul>
            </li>
    @endif
    @if(!App\Closet\MyHTML::restrictedAccess())
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">VHF Results <span class="caret"></span></a>
              <ul class="dropdown-menu">
                                    <!-- EOC list for approved results-->
                    <li class="hidden">{!! App\Closet\MyHTML::anchor("/evd?type=".base64_encode(0),"Pending Release",22) !!}</li>
                    <!-- EOC list for approved results-->
                    
                    <li>{!! App\Closet\MyHTML::anchor("/evd?type=MQ==&printed=2","All Results",'can_print_results') !!}</li>
                    <li>{!! App\Closet\MyHTML::anchor("/evd?type=MQ==&printed=0","Pending Printing",'can_print_results') !!}</li>
                    <li>{!! App\Closet\MyHTML::anchor("/evd/pending_printing","District Contacts",'can_print_results') !!}</li>
                    <li>{!! App\Closet\MyHTML::anchor("/evd/export_data?type=MQ==&printed=1","Export Results",'can_print_results') !!}</li>
              </ul>
            </li>
    @endif
            @if(App\Closet\MyHTML::permit(11) || App\Closet\MyHTML::is_ref_lab())
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Data Entry <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li>{!! App\Closet\MyHTML::anchor("/lif/covid/form",'New Suspect','can_print_results') !!}</li>
                
                 <li class="">{!! App\Closet\MyHTML::anchor("/cases/list?type=view_rdt_results",'All RDT results','capture_samples_details') !!}</li>
                <li>{!! App\Closet\MyHTML::anchor("/cases/list?type=lab_numbers",'Accession Samples from eLIF pool','capture_samples_details') !!}</li>
                <li>{!! App\Closet\MyHTML::anchor("/cases/list?type=pending_results",'View Accessioned Samples','capture_samples_details') !!}</li>
              
              </ul>
            </li>
            @endif
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->username }} <span class="caret"></span></a>
                <ul class="dropdown-menu">

                    <li><a href="/user_pwd_change">Change Password</a></li>
                        <li> <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
  
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                        @csrf
                                    </form></li>
                </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
<script type="text/javascript">
$(document).ready(function () {

    for (var i = 1; i <= 9; i++) {
        var sect=$("#s"+i);
        if(sect.hasClass("mm")){
            var lnk=$("#l"+i);
            if (!lnk.hasClass('active')) {
                lnk.addClass('active');
            }
        }
    }
});
</script>
@endif
