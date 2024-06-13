@extends('layouts/layout')

@section('content')

<style type="text/css">
	.nav-tabs {
    margin-bottom: 5px;
}
.has_result{
  color:#d9534f;
}
h1{
  background-color:#f5f5f5;
  border-radius: 4px;
  padding: 11px 14px;
  margin-top: 0;
  margin-bottom: 20px;
  font-size: 26px;
}
</style>
<h1>{{$page_title}}</h1>
<ul class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li>{!! MyHTML::anchor("/resultsqc/list?type=".base64_encode(1),'All results',52) !!}</li> 
    @if(MyHTML::permit(52))<li><a href="#" class="btn btn-xs btn-danger" onclick="location.reload()">Reset Filters</a></li>@endif  
</ul>

@include('flash-message')

{!! Form::open(array('url'=>'/resultsqc/list','id'=>'other_filters_form')) !!}
       <div class="well firstrow list hidden">

          <div class="row">
            <div class="col-md-12">
             <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                  <label for="test_fro">Tested between:</label>
                  <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_fro" type="text" id="test_from" value="{{ $test_fro}}">
                  <label for="test_to">and </label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_to" type="text" id="test_to" value="{{ $test_to }}">
                  
                 <span class="">|</span>
                 
                  <input type="hidden" name="p_type" value="{!!$page_type!!}">
                  <input type="submit" value="Search" class="btn btn-primary btn-sm" style="margin-top: 5px;">          

              </div>
              </div>
          </div>
        </div>
        {!! Form::close() !!}
<div id="my-tab-content" class="">
    <div class="tab-pane active" id="print"> 
       {!! Form::open(array('url'=>'/outbreaks/release_retain','id'=>'approve_form', 'name'=>'approve_form')) !!}
        <input type="text" name="type" class="hidden" id="type">
        <a href="#" class="btn btn-xs btn-primary " id="select_all">Select all visible</a>
        @if($page_type == 'approved')
          <input class="btn  btn-xs btn-success" name="pdf" type="button" value="Reverse all selected" onclick="approveSelected('reverse');">
        @else
          <input class="btn  btn-xs btn-success" name="pdf" type="button" value="Approve all selected" onclick="approveSelected('approve');">
        @endif
        <div class="table-responsive">
          <table id="res" class="table table-condensed table-bordered  table-striped">
          <thead>
              <tr>
                  <th>#</th>   
                  <th>Patient ID</th>,
                  <th>Name</th>
                  <th>Age</th>
                  <th>Sex</th>
                  <th>Contact</th>
                  <th>Passport No</th>
                  <th>Nationality</th>
                  <th>Swabbing District</th>  
                  <th>Worksheet</th> 
                  <td>Tube Id</td> 
                  <th>Locator ID</th>                  
                  <th>Target 1</th>
                  <th>Target 2</th>
                  <th>CT Value</th>
                  <th>Final Result</th>
                  <th>Current Result</th>
                  <th>Current Result</th>
                  <th>Action</th>
              </tr>
          </thead>
          </table> 
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
$(".standard-datepicker-nofuture").datepicker({
      dateFormat: "yy-mm-dd",
      maxDate: 0
    });
$(function() {
    var page_type ='{{$page_type}}';
    var test_fro = '{{$test_fro}}';
    var test_to = '{{$test_to}}';

    if(page_type == 'pending_approval'){
      $('#res').DataTable({
          processing: true,
          serverSide: true,
          pageLength: 25,
          ajax: '/resultsqc/list_data?page_type='+page_type+'&test_fro='+test_fro+'&test_to='+test_to,
          paging:true,
          "columnDefs": [
              {
                  "targets": [9,10,12,13,14],
                  "visible": false
              }
          ],
          order: [[ 2, "asc" ]],
          "createdRow": function( row, data, dataIndex ) {
             if( data[16] == '' ){       
               $(row).addClass('has_result');           
             }
        }
      });
    }else if(page_type == 'pending_patient_info'){
      $('#res').DataTable({
          processing: true,
          serverSide: true,
          pageLength: 25,
          ajax: '/resultsqc/list_data?page_type='+page_type+'&test_fro='+test_fro+'&test_to='+test_to,
          paging:true,
          "columnDefs": [
              {
                  "targets": [0,1,2,3,4,5,6,7,8,12,13,14,16,17],
                  "visible": false
              }
          ],
          order: [[ 2, "asc" ]],
      });
    }else if(page_type == 'retained' || page_type == 'approved'){
      $('#res').DataTable({
          processing: true,
          serverSide: true,
          pageLength: 25,
          ajax: '/resultsqc/list_data?page_type='+page_type+'&test_fro='+test_fro+'&test_to='+test_to,
          paging:true,
          "columnDefs": [
              {
                  "targets": [1,2,3,4,5,6,7,8,16,17],
                  "visible": false
              }
          ],
          order: [[ 2, "asc" ]],
      });
    }else{
       $('#res').DataTable({
          processing: true,
          serverSide: true,
          pageLength: 25,
          ajax: '/resultsqc/list_data?page_type='+page_type+'&test_fro='+test_fro+'&test_to='+test_to,
          paging:true,
          order: [[ 2, "asc" ]],
      });
    }//end if for page type
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
function approveSelected(type) {  
 //set the page type
$('#type').val(type);   
  var the_form = document.getElementById("approve_form");   
  the_form.submit();   
}


</script>
@endsection()