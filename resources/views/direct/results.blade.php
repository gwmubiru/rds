@extends('layouts/layout')

@section('content')
<?php 
$params = "";
$limit = "?";
if(\Request::has('h')) $limit .= "h=". \Request::get('h');

$pending_actv="";
$completed_actv="";
$search_actv="";

if(\Request::has('search')){
    $search_actv="class=active";
}elseif($tab=='completed'){
    $completed_actv="class=active"; 
}else{
    $pending_actv="class=active";
}

$facility_str = str_replace(" ", "_", $facility_name);
$facility_str = str_replace("/", "", $facility_str);
$facility_str = str_replace("'", "", $facility_str);

if(\Request::has('search')){
    $valids_class = "btn-default";
    $invalids_class = "btn-default";
    $rejects_class = "btn-default"; 
}elseif($type=='rejects'){
    $valids_class = "btn-default";
    $invalids_class = "btn-default";
    $rejects_class = "btn-primary"; 
}elseif ($type=='invalids') {
    $valids_class = "btn-default";
    $invalids_class = "btn-primary";
    $rejects_class = "btn-default"; 
}else{
    $valids_class = "btn-primary";
    $invalids_class = "btn-default";
    $rejects_class = "btn-default"; 
}
?>
<style type="text/css">
	.nav-tabs {
    margin-bottom: 5px;
}
.btn-primary {
    margin-bottom: 5px;
}
</style>
<ul class="breadcrumb">
    <li><a href="/">HOME</a></li>
    <li><a href="/direct/facility_list">FACILITIES</a></li>
    <li action="active">{{ $facility_name }}</li>
</ul>

<div style="margin-bottom:10px;">
            <a href="?type=valids" class='btn {{ $valids_class }}' style='width:200px;'>VALID RESULTS</a>
            <a href="?type=invalids" class='btn {{ $invalids_class }}' style='width:200px;'>INVALID RESULTS</a>
            <a href="?type=rejects" class='btn {{ $rejects_class }}' style='width:200px;'>REJECTED SAMPLES</a>
        </div>

<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
    <li {{$pending_actv}} ><a href="?tab=pending&type={{ $type }}">Print</a></li>
    <li {{$completed_actv}}><a href="?tab=completed&type={{ $type }}" >Printed/Downloaded</a></li>
    <li {{ $search_actv }} title='Search'><a href="?search=1&type={{ $type }}" >Search</a></li>
</ul>


<div id="my-tab-content" class="tab-content">
    <div class="tab-pane active" id="print"> 
        @if(\Request::has('search'))
        Search using Hep B Number or Form Number:
        <div class="row">
            <div class="col-md-4">
            {!! Form::text('search','', ['id'=>'id-search','class' => 'form-control input-sm  input_md', 'autocomplete'=>'off', 'placeholder'=>"Search..."] ) !!}
            </div>
            <div class="col-md-5">
                <!-- <span id="id-search-button" class="btn btn-danger btn-sm">search</span> -->
            </div>
        </div>
          
          <div class='live_drpdwn' id="id-dropdown" style='display:none;width:700px'></div>
          
        @else
        {!! Form::open(array('url'=>'/direct/result/','id'=>'view_form', 'name'=>'view_form', 'target' => 'Map' )) !!}
        <input type="hidden" name="facility_id" value="{{ $facility_id }}">
        <input type="hidden" name="facility" value="{{ $facility_str }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <a href="#" class='btn btn-xs btn-primary' id="select_all" >Select all visible</a>
        {!! MyHTML::submit('Download selected','btn  btn-xs btn-primary','pdf') !!}
        <input type="button" class='btn btn-xs btn-primary' value="Print selected" onclick="printSelected();"   /> 

        <table id="results-table" class="table table-condensed table-bordered  table-striped">
        <thead>
            <tr>
                <th>Select</th>
                <th>Form Number</th>                     
                <th>Hep Number</th>
                <th>Other ID</th>
                <th>Date Collected</th>
                <th>Date Received</th>
                <th>Date Released</th>
                <th>Date Printed</th>
                <th>Options</th>
            </tr>
        </thead>
        </table> 
       {!! Form::close() !!}
       @endif
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
    @if(!\Request::has('search'))
    $('#results-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: '/direct/results/data/{{ $facility_id }}/?tab={{ $tab }}&type={{ $type }}',
        order: [[ 6, "desc" ]],
    });
    @endif

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

function get_data(q,drpdwn,link){  
    if(q && q.length>=3){ 
        $.get(link+"?txt="+q+"&f="+{{ $facility_id }}, function(data){
            drpdwn.show();
            drpdwn.html(data);
        });   
    }else{
        drpdwn.hide();
        drpdwn.html("");
    }
}

$("#id-search").keyup(function(){
    var q = $(this).val();
    var dd = $("#id-dropdown");
    get_data(q, dd, "/direct/search_result/");
});
</script>
@endsection()