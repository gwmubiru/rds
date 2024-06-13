@extends('layouts/layout')
@section('content')
<?php 
$params = "";
$limit = "?";
if(\Request::has('h')) $limit .= "h=". \Request::get('h');

$pending_actv="";
$completed_actv="";
if(isset($tab)){
    if($tab=='pending'){
        $pending_actv="class=active";
    }else{
        $completed_actv="class=active";    
    }
    $limit .= "&tab=$tab";
} 

$pending_url = "/results?tab=pending";
$completed_url = "/results?tab=completed";

$tab_limit = isset($tab)?"?tab=$tab":"";
?>

<ul class="breadcrumb">
    <li><a href="/">HOME</a></li>
    <li action="active">FACILITIES</li>
</ul>



<table id="results-table" class="table table-condensed table-bordered  table-striped">
<thead>
    <tr>
       <!--  @if(empty(Auth::user()->hub_id))<th>Hub</th> @endif  -->
        <th>Facility</th>  
        <th>Hub</th>                   
        <th>Contact Person</th>
        <th>Phone</th>
        <th>Email</th>

        <th># Pending</th>
        <th># Printed/ Downloaded</th>
        <th>Last dispatch date</th>
        <th>Action</th>
       <!--  <th># Downloaded</th>
        @if($sect == 'admin')<th># Last Printed/ Downloaded</th>@endif 
        @if($sect == 'results')<th>Action&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>@endif
     -->
    </tr>
</thead>
</table>  

<style type="text/css">
#id-search{
    width: 480px;
}
</style> 

<script type="text/javascript">
$(function() {
    $('#results').addClass('active');
    $('#results-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: '/direct/facility_data/',
        drawCallback: function( settings ) {
           $(".has-account").parent().parent().attr("style","background-color:#F5A9A9");
        },
        
    }); 

});

drpdwn= $(".live_drpdwn");

function get_data(q,drpdwn,link){
    if(q && q.length>=3){   
        //console.log("this is what you have just typed:"+ q+"link"+link);  
        $.get(link+"?txt="+q, function(data){
            drpdwn.show();
            drpdwn.html(data);
        });
    }else{
        drpdwn.hide();
        drpdwn.html("");
    }
}

$("#hub").keyup(function(){
    var q = $(this).val();
    var dd = $("#worksheet_dropdown");
    get_data(q, dd, "/searchbyhub/");
});

$("#id-search").keyup(function(){
    var q = $(this).val();
    var dd = $("#id-dropdown");
    get_data(q, dd, "/direct/search_result/");
});

</script>

<style type="text/css">
.has_account{
    color: red;
    background-color:#F5A9A9;
}
</style>
@endsection()