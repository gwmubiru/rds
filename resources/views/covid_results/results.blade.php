@extends('layouts/layout')

@section('content')

<style type="text/css">
	.nav-tabs {
    margin-bottom: 5px;
}
.btn-primary {
    margin-bottom: 5px;
}
.printed {
  color: #28a745!important;
}

</style>
<ul class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li>{!! App\Closet\MyHTML::anchor("/outbreaks/list?type=MQ==&printed=2",'All results','can_print_results') !!}</li>
    @if(App\Closet\MyHTML::permit('can_print_results') && !$is_update)<li><a href="/outbreaks/list?type=MQ==&printed=2" class="btn btn-xs btn-danger">Reset Filters</a></li>@endif

</ul>

@include('flash-message')


<div id="my-tab-content" class="">
  @if(!$is_update)
    <div class="" id="print">
		 @can('export_results')	 
		<div class="well firstrow list">
                {!! Form::open(array('url'=>'/outbreaks/export_to_csv','id'=>'export_to_csv')) !!}

	           <div class="row">
	             <div class="col-md-12">
	              <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">

	 							 <label for="exp_fro">Samples Collected between:</label>
	 							 {!! Form::open(array('url'=>'/outbreaks/export_to_csv','id'=>'export_to_csv')) !!}

	                   <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="exp_fro" type="text" id="exp_fro">
	                   <label for="exp_to">and </label>
	                   <input  class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="exp_to" type="text" id="exp_to">

	 									<input type="submit" value="Export to CSV for mTrack" class="btn btn-primary btn-sm" style="margin-top: 5px;">
										        {!! Form::close() !!}
	               </div>
	               </div>
	           </div>


			{!! Form::open(array('url'=>'/outbreaks/export_data')) !!}
	           <div class="row">
	             <div class="col-md-12">
	              <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">

	 							 <label for="exp_fro">All samples tested between:</label>
	 								{!! Form::open(array('url'=>'/outbreaks/export_data')) !!}

	                   <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="surv_fro" type="text" id="surv_fro">
	                   <label for="exp_to">and </label>
	                   <input  class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="surv_to" type="text" id="surv_to">

	                   <input type="submit" value="Export CSV for survailance" class="btn btn-primary btn-sm" style="margin-top: 5px;">

										 {!! Form::close() !!}
	               </div>
	               </div>
	           </div>
	         </div>
            @endcan
           

        {!! Form::open(array('url'=>'/outbreaks/list','id'=>'other_filters_form')) !!}
       <div class="well firstrow list">
           <div class="row">
            <div class="col-md-12">
             <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                  <label for="fro">Collected between:</label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="fro" type="text" id="from" value="{{ $fro}}">
                  <label for="to">and </label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="to" type="text" id="to" value="{{ $to }}">
                  <input type="hidden" name="p_type" value="{!!$page_type!!}">
                  <span class="">|</span>
                  <input class="form-control input-sm" name="patient_id" type="text" id="patient_id" placeholder="Patient ID" value="{{$patient_id}}">
                  <span class="">|</span>
                  <input class="form-control input-sm" name="district" type="text" id="district" placeholder="District" value="{{$district}}">
                  <span class="">|</span>
                  <select class="form-control input-sm" name="test_result" value="{{$test_result}}" id="test_result">
                    <option value="">Result</option>
                    <option value="Positive" @if($test_result == 'Positive') selected="selected" @endif>Positive</option>
                    <option value="Negative" @if($test_result == 'Negative') selected="selected" @endif>Negative</option>
                  </select>

              </div>
              </div>
          </div>

          <div class="row">
            <div class="col-md-12">
             <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                  <label for="test_fro">Tested between:</label>
                  <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_fro" type="text" id="test_from" value="{{ $test_fro}}">
                  <label for="test_to">and </label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_to" type="text" id="test_to" value="{{ $test_to }}">

                 <span class="">|</span>

                  {!!App\Closet\MyHTML::select('ref_lab',$ref_labs,$ref_lab,'ref_lab','form-control input-sm')!!}
                  <select class="form-control input-sm" name="printed" value="{{$printed}}" id="printed">
                    <option value="">Printing Status</option>
                    <option value="1" @if($printed == 1) selected="selected" @endif>Printed</option>
                    <option value="0" @if($printed ==0) selected="selected" @endif>Pending</option>
                    <option value="2" @if($printed ==2) selected="selected" @endif>All</option>
                  </select>

                  <input type="submit" value="Search" class="btn btn-primary btn-sm" style="margin-top: 5px;">

              </div>
              </div>
          </div>
        </div>
        @endif
        {!! Form::close() !!}
        @if($is_update)
         {!! Form::open(array('url'=>'/mass_update_results','id'=>'mass_update', 'name'=>'mass_update')) !!}
         <p class="text-center"><input type="submit" class='btn  btn-danger' value="Submit" class="results_update"/></p>
        @else
          @if($page_type == 0)
            {!! Form::open(array('url'=>'/outbreaks/release_retain','id'=>'view_form', 'name'=>'view_form')) !!}
          @else
          {!! Form::open(array('url'=>'/outbreaks/result/','id'=>'view_form', 'name'=>'view_form', 'target' => 'Map' )) !!}
          @endif

           @if($page_type == 1)
            <a href="#" class='btn btn-xs btn-primary ' id="select_all" >Select all visible</a>

            {!! App\Closet\MyHTML::submit('Download selected','btn  btn-xs btn-primary','pdf') !!}

            <input type="button" class='btn btn-xs btn-primary ' value="Print all selected" onclick="printSelected();" />
          @endif
          @if($page_type == 0)
            <a href="#" class='btn btn-xs btn-primary' id="select_all" >Select all visible</a>
            <input type="button" class='btn btn-xs btn-primary' value="Release all selected" onclick="approveSelected();" />
          @endif
        @endif   <!-- End if page is not updating -->



        <div class="table-responsive">
          <table id="results-table" class="table table-condensed table-bordered  table-striped">
          <thead>
              <tr>
                  <th>Select</th>
                  <th>Patient ID</th>
                  <th>Lab Number</th>
                  <th>Swabbing District</th>
                  <th>Site of Collection</th>
                  <th>Collection Date</th>
                 <th>Name of Client</th>
                  <th>Age of Client</th>
                  <th>Sex of Client</th>
                  <th>Test Date</th>
                  <th>Uploaded On</th>
                  <th>Result</th>
                  <th>Test Method</th>
                  <th>Testing Lab</th>
                  <th>Options</th>
                  <th class="">this</th>
              </tr>
          </thead>
          </table>
           @if($is_update)
            <p class="text-center"><input type="submit" class='btn  btn-danger' value="Submit" class="results_update" style="margin-bottom: 20px" /></p>
           @endif
         {!! Form::close() !!}
       </div>
    </div>
</div>
<style type="text/css">
#id-search{
    width: 700px;
}
</style>

<script type="text/javascript">
  $(document).ready(function() {
    $("#ref_lab, #test_result, #printed").select2();
    $(".rest_dr").select2({ allowClear:true });
 });
$(".standard-datepicker-nofuture").datepicker({
      dateFormat: "yy-mm-dd",
      maxDate: 0
    });
$(function() {
    var page_type = {{$page_type}};
    var printed = {{$printed}};
    var fro = '{{$fro}}';
    var to = '{{$to}}';
    var patient_id = '{{$patient_id}}';
    var district = '{{$district}}';
    var test_result = '{{$test_result}}';
    var test_fro = '{{$test_fro}}';
    var test_to = '{{$test_to}}';
    var ref_lab = '{{$ref_lab}}';
    var is_update = '{{$is_update}}';
    var col_visible = true;
    //don't show  some colums if the user is updating district
    if(is_update){
      col_visible = false;
    }
    $('#results-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: '/outbreaks/list_data?type='+page_type+'&printed='+printed+'&fro='+fro+'&to='+to+'&patient_id='+patient_id+'&district='+district+'&test_result='+test_result+'&test_fro='+test_fro+'&test_to='+test_to+'&ref_lab='+ref_lab+'&is_update='+is_update,
        order: [[ 9, "desc" ]],
        paging:true,
        "columnDefs": [
            {
                "targets": [ 15],
                "visible": false
            },
            //don't show the checkbox and options columns when updating swab district
            {
                "targets": [ 0 ],
                "visible": col_visible
            },
            {
                "targets": [ 11 ],
                "visible": col_visible
            },
            {
                "targets": [ 12 ],
                "visible": col_visible
            },
            {
                "targets": [ 13 ],
                "visible": col_visible
            },
            { "orderable": false, "targets": [0,13,15] }
        ],
        "createdRow": function( row, data, dataIndex ) {
             if ( data[15] == 1 ) {
               $(row).addClass('printed');

             }
        }
        //lengthMenu: [10, 25, 50, 75, 100],
    });

    $('#select_all').click(function(){
        var status = $(this).html();
        if(status == 'Select all visible'){
            $(".samples").attr("checked", true);
            $(this).html('Unselect all visible');
        }else{
            $(".samples").attr("checked", false);
            $(this).html('Select all visible');
        }
    });

});

function approveSelected() {
   var the_form = document.getElementById("view_form");
      the_form.submit();
}
function printSelected() {
   var mapForm = document.getElementById("view_form");
   map = window.open("","Map","width=1100,height=1000,menubar=no,resizable=yes,scrollbars=yes");
   //map=window.open("","Map","status=0,title=0,height=600,width=800,scrollbars=1");

   if (map) {
      mapForm.submit();
   } else {
      alert('You must allow popups for this map to work.');
   }
}

var drpdwn= $(".live_drpdwn");


</script>
@endsection()
