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
    <li><a href="/">HOME</a></li>
    @if(\MyHTML::is_ref_lab())
      <li>{!! MyHTML::anchor("/outbreakrlts?type=form",'Upload Results',21) !!}</li>
    @endif
</ul>

@include('flash-message')
@if($page_type == 1)
  <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
      <li class="@if($printed==0 || $printed=='')active @endif"><a href="?type={{base64_encode(1)}}&amp;printed=0">Pending Printing</a></li>
      <li class="@if($printed==1)active @endif"><a href="?type={{base64_encode(1)}}&amp;printed=1">Printed/Downloaded</a></li>
  </ul>
@endif

<div id="my-tab-content" class="tab-content printed_{{$printed}}">
    <div class="tab-pane active" id="print"> 
       
        @if($page_type == 0)
        {!! Form::open(array('url'=>'outbreakrlts/approve_retain','id'=>'view_form', 'name'=>'view_form')) !!}
        @else
        {!! Form::open(array('url'=>'/outbreakrlts/result/','id'=>'view_form', 'name'=>'view_form', 'target' => 'Map' )) !!}
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
        @if(!\MyHTML::is_ref_lab())
         @if($page_type == 0)
          <a href="#" class='btn btn-xs btn-primary' id="select_all" >Select all visible</a>
          <input type="button" class='btn btn-xs btn-primary' value="Approve all selected" onclick="approveSelected();" />
         @endif
         @endif
        <table id="results-table" class="table table-condensed table-bordered  table-striped">
        <thead>
            <tr>
                <th>Select</th>
                <th>Patient ID</th>  
                <th>District</th>  
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
<style type="text/css">
#id-search{
    width: 700px;
}
</style> 

<script type="text/javascript">

$(function() {
    $('#results').addClass('active');
    var page_type = {{$page_type}}
    var printed = {{$printed}}
    $('#results-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: '/outbreakrlts/list_data?type='+page_type+'&printed='+printed,
        order: [[ 1, "desc" ]],
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