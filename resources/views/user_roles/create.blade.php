@extends('Admin.app')
@section('admin-content')
	<div id='d1' class='panel panel-default'>
	 <div class='panel-body'>
		{!! Session::get('msge') !!}
		<?php if(isset($messages)) echo $messages->first('description') ?>
		{!! Form::open(array('url'=>'user_roles/store','id'=>'form_id','onsubmit'=>'return chkform(this)')) !!}

		{!! Form::text('description','',['class'=>'form-control','id'=>'a','placeholder'=>'Enter name of the role','required'=>'true']) !!}
		<br>
		<div style="text-align: center;">
			<label onclick="selectAll(this,true,'.perms')" class="btn btn-primary "> Select all</label>
			{!! MyHTML::submit('Save User Role') !!} 
		</div>
		<br>
		<div class="row">
		<div class="col-md-6">
		<?php extract($perm_arr) ?>
		@foreach( $perm_parents AS $p_id => $desc)
		@if($p_id==5)  </div><div class="col-md-6"> @endif
		 <u><b>{!! $desc !!}</b></u>
		 <br> <br><a class="blue-link" onclick="selectAll(this,true,'.perms{!! $p_id !!}')">Select all</a>
		  @foreach( $perm_children[$p_id] AS $id => $child_desc)
		  <div class='checkbox'><label><input class="perms perms{!! $p_id !!}" type='checkbox' name="role_permissions[]" value="{!! $p_id.'_'.$id !!}"> {!! $child_desc !!}</label></div>
		  @endforeach
		  <br>
		@endforeach
	    </div>
		</div>
		<div style="text-align: center;">{!! MyHTML::submit('Save User Role') !!} </div>

		{!! Form::close() !!}
	</div>
</div>
<script type="text/javascript">
function selectAll(that,check,clss){
	var items=document.querySelectorAll(clss);
	for(var i in items){
		items[i].checked=check;
    }
    if(check==true){
    	that.innerHTML="Unselect All";
    	that.setAttribute('onClick',"selectAll(this,false,'"+clss+"')");
    }else{
    	that.innerHTML="Select All";
    	that.setAttribute('onClick',"selectAll(this,true,'"+clss+"')");
    }

}

function chkform(d){
	var num_chkt=$("input:checkbox:checked.perms").length;
	if(num_chkt==0){
		alert("Please select atleast one permission");
		return false;
	}

	return true;
}
</script>
	
<?php

		/**/

		//print_r($perm_parents)."<br>";
		//print_r($perm_children);
/*
		Array ( [1] => Array ( [11] => Capture Sample/Batch details from the form 
			                   [12] => View Sample/Batch details 
			                   [13] => Edit Sample/Batch details 
			                   [14] => Approve Sample/Batch details 
			                   [15] => Export Sample/Batch details as PDF/Excel and printing 
			                   [16] => Soft delete Sample/Batch ) 

		        [2] => Array ( [17] => Create Worksheet 
		        	           [18] => View Worksheet details 
		        	           [19] => Edit Worksheet details 
		        	           [20] => Soft delete Worksheet 
		        	           [21] => Upload results to a worksheet ) 

		        [3] => Array ( [22] => Edit Results 
		        	           [23] => Approve Sample results 
		        	           [24] => Export Sample results as PDF/Excel and Print ) 

		        [4] => Array ( [25] => Print Follow up forms for HIV+- infants 
		        	           [26] => Capture feedback from facilities 
		        	           [27] => View follow up information ) 

		        [5] => Array ( [28] => Stock in :: Make 
		        	           [29] => Stock in :: Edit 
		        	           [30] => Stock in :: View 
		        	           [31] => In house requisitions :: Make 
		        	           [32] => In house requisitions :: Edit 
		        	           [33] => In house requisitions :: View 
		        	           [34] => In house requisitions :: Approve 
		        	           [35] => Facility Orders :: Make 
		        	           [36] => Facility Orders :: Edit 
		        	           [37] => Facility Orders :: View 
		        	           [38] => Facility Orders :: Approve ) 

		        [6] => Array ( [39] => Manage categories of feedback 
		        	           [40] => Feedback:: Add 
		        	           [41] => Feedback:: Respond 
		        	           [42] => Feedback:: View ) 

		        [7] => Array ( [43] => System Administration :: Manage User roles 
		        	           [44] => System Administration :: Manage User accounts 
		        	           [45] => System Administration :: Manage Appendices 
		        	           [46] => System Administration :: Manage IPs 
		        	           [47] => System Administration :: Manage Facilities 
		        	           [48] => System Administration :: Manage Locations (Regions,Hubs,Districts) ) )

*/?>
@endsection