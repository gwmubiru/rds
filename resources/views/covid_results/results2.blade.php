@extends('layouts/layout')

@section('content')

<style type="text/css">
	.nav-tabs {
    margin-bottom: 5px;
}
.btn-primary {
    margin-bottom: 5px;
}
.printed_1{
  color:#5cb85c;
}
</style>
<ul class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li>{!! MyHTML::anchor("/outbreaks/list?type=".base64_encode(1),'All results',52) !!}</li> 
    @if(MyHTML::permit(52))<li><a href="/outbreaks/list?type=MQ==&printed={{$printed}}" class="btn btn-xs btn-danger">Reset Filters</a></li>@endif  
</ul>
@if(Auth::user()->type ==15 and $is_synced)
  <div class='form-inline' style="padding-bottom: 15px;">
    <a class="btn btn-primary btn-xs" href="{{\MyHTML::getSyncRUR()}}/outbreaks/sync_results/{{base64_encode(Auth::user()->ref_lab)}}?return={{base64_encode(url())}}"> Sysnc Results with RDS</a>
  </div>  
@endif
@include('flash-message')
@if($page_type == 1)
  <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
      <li class="@if($printed==0 || $printed=='')active @endif"><a href="?type={{base64_encode(1)}}&amp;printed=0">Pending Printing</a></li>
      <li class="@if($printed==1)active @endif"><a href="?type={{base64_encode(1)}}&amp;printed=1">Printed/Downloaded</a></li>
  </ul>
@endif
{!! Form::open(array('url'=>'/outbreaks/list','id'=>'other_filters_form')) !!}
       <div class="well firstrow list">

          <div class="row">
            <div class="col-md-12">
             <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                  <label for="test_fro">Tested between:</label>
                  <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_fro" type="text" id="test_from" value="{{ $test_fro}}">
                  <label for="test_to">and </label>
                  <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_to" type="text" id="test_to" value="{{ $test_to }}">
                  
                 <span class="">|</span>
                 
                  <select class="form-control input-sm" name="printed" value="{{$printed}}" id="printed">
                    <option value="">Printing Status</option>
                    <option value="1" @if($printed == 1) selected="selected" @endif>Printed</option>
                    <option value="0" @if($printed ==0) selected="selected" @endif>Pending</option>
                    <option value="2" @if($printed ==2) selected="selected" @endif>All</option>
                  </select>
                  <input type="hidden" name="p_type" value="{!!$page_type!!}">
                  <input type="hidden" name="is_synced" value="{!!$is_synced!!}">
                  <input type="submit" value="Search" class="btn btn-primary btn-sm" style="margin-top: 5px;">          

              </div>
              </div>
          </div>
        </div>
        {!! Form::close() !!}
<div id="my-tab-content" class="tab-content printed_{{$printed}}">
    <div class="tab-pane active" id="print"> 
       
        @if($page_type == 0)
        {!! Form::open(array('url'=>'/outbreaks/release_retain','id'=>'view_form', 'name'=>'view_form')) !!}
        @else
        {!! Form::open(array('url'=>'/outbreaks/result/','id'=>'view_form', 'name'=>'view_form', 'target' => 'Map' )) !!}
        @endif
        @if($page_type == 1)
        <a href="#" class='btn btn-xs @if($printed)btn-success @else btn-primary @endif' id="select_all" >Select all visible</a>
        @if($printed)
          {!! MyHTML::submit('Download selected','btn  btn-xs btn-success','pdf') !!}
        @else
          {!! MyHTML::submit('Download selected','btn  btn-xs btn-primary','pdf') !!}
        @endif
        <input type="button" class='btn btn-xs @if($printed)btn-success @else btn-primary @endif' value="Print all selected" onclick="printSelected();" /> 
        @endif
         @if($page_type == 0)
          <a href="#" class='btn btn-xs btn-primary' id="select_all" >Select all visible</a>
          <input type="button" class='btn btn-xs btn-primary' value="Release all selected" onclick="approveSelected();" />
         @endif
        <div class="table-responsive">
          <table id="results-table" class="table table-condensed table-bordered  table-striped">
          <thead>
              <tr>
                  <th>Select</th>
                  <th>Patient ID</th>  
                  <th>Lab No.</th>
                  <th>Patient District</th>   
                  <th>Site of Collection</th>   
                  <th>Collection Date</th>
                   <th>Name of Client</th> 
                    <th>Age of Client</th>  
                    <th>Sex of Client</th>  
                    <th>Test Date</th>               
                  <th>Result</th>
                  <th>Options</th>
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
    var page_type =' {{$page_type}}';
    var printed = '{{$printed}}';
    var is_synced = '{{$is_synced}}';
    var test_fro = '{{$test_fro}}';
    var test_to = '{{$test_to}}';
    $('#results-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: '/outbreaks/list_data?type='+page_type+'&printed='+printed+'&is_synced='+is_synced+'&test_fro='+test_fro+'&test_to='+test_to,
        order: [[ 9, "desc" ]],
        paging:true,
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