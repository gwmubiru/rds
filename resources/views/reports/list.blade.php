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
    
</ul>

@include('flash-message')

{!! Form::open(array('url'=>'/reports/list','id'=>'other_filters_form', 'class'=>'')) !!}
   <div class="well firstrow list">

      <div class="row">
        <div class="col-md-12">
         <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
              <label for="test_fro">Tested between:</label>
              <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_fro" type="text" id="test_from" value="{{ $test_fro}}">
              <label for="test_to">and </label>
              <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="test_to" type="text" id="test_to" value="{{ $test_to }}">
              
             <span class="hidden">|</span>
             
              <select class="form-control input-sm hidden" name="printed" value="" id="printed">
                <option value="">Printing Status</option>
                
              </select>
              <input type="submit" value="Search" class="btn btn-primary btn-sm" style="margin-top: 5px;">          

          </div>
          </div>
      </div>
    </div>
{!! Form::close() !!}
<div id="my-tab-content" class="">
    <div class="tab-pane active" id="print"> 
       
        
        <div class="table-responsive">
          <table id="results-table" class="table table-condensed table-bordered  table-striped">
          <thead>
              <tr> 
                  <th>Lab No.</th>
                  <th>Site of Collection</th>   
                  <th>Collection Date</th>
                  <th>Name of Client</th> 
                  <th>Age of Client</th>  
                  <th>Sex of Client</th>  
                  <th>Test Date</th>               
                  <th>Result</th>
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
    var test_fro = '{{$test_fro}}';
    var test_to = '{{$test_to}}';
    $('#results-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: '/reports/list_data?test_fro='+test_fro+'&test_to='+test_to,
        order: [[ 0, "desc" ]],
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