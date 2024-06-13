@extends('Admin.app')
@section('admin-content')
<div id="d8" class="panel panel-default">
	<div class="panel-heading"><b>Hubs</b></div>
	<div class="panel-body ">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>$post_url,'id'=>'form_id')) !!}
		<a href="/excel">{!! MyHTML::tinyImg('excel.png',20,30) !!}Export to Excel</a>
		<table class='table table-striped table table-condensed' id='tab_id'>
		  <thead>
			<tr>
				
				<th>Hub</th>
				<th>Email</th>
				<th>IP</th>
				<th>Hub Coordinator</th>
				<th>Hub Coordinator Contact</th>
				<th></th>
				<!-- <th></th> -->
			</tr>
		  </thead>
			<?php 
			$x=1;
			$excel_data=array();;
			?>
			 <tbody>
			@foreach ($hubs AS $hub)
		 
			<tr>
				
				<?php 
				echo "<td id=hub_".$hub->id.">".$hub->hub." </td>";
				echo "<td id=email_".$hub->id.">".$hub->email."</td>";
				echo "<td id=ip_".$hub->id.">".$hub->ip.MyHTML::hidden('ip',$hub->ipID,'ipID_'.$hub->id)."</td>";
				echo "<td id=coord_".$hub->id.">".$hub->coordinator."</td>";
				echo "<td id=coord_c_".$hub->id.">".$hub->coordinator_contact."</td>";
				echo "<td class='edit_links' id=edit_".$hub->id.">".MyHTML::link_to('#','Edit','',"editRow($hub->id)")."</td>";
				$excel_data[]=array(
					"Hub"=>$hub->hub,
					"Email"=>$hub->email,
					"IP"=>$hub->ip,
					"Hub Coordinator"=>$hub->coordinator,
					"Hub Coordinator Contact"=>$hub->coordinator_contact
					);
				?>
				<!-- <td>{!! $hub->id !!}</td> -->
			</tr>		 
			@endforeach
			</tbody>
		</table>

		<?php if(!isset($edit_id)) echo "<label class='add_item' onClick='addItem()'>Add</label>" ?><br>
		<p><?php if(isset($ips)) foreach($ips as $ip_id => $ip_name) echo MyHTML::hidden("ip_arr[$ip_id]",$ip_id."_".$ip_name,null,'ip_arr') ?></p>
		<div id='save_btn_hide'>{!! MyHTML::submit('Save') !!}</div>
		<?php session(["excel_data"=>$excel_data,"excel_file_name"=>"hubs"]) ?>
	  {!! Form::close() !!}
	</div>
</div>	

<script type="text/javascript">
function editRow(id){
	var hub=getById('hub_'+id);
	var email=getById('email_'+id);
	var ip=getById('ip_'+id);
	var coord=getById('coord_'+id);
	var coord_c=getById('coord_c_'+id);
	hub.innerHTML="{!! MyHTML::text2('hub','"+hub.innerHTML+"') !!}";
	email.innerHTML="{!! MyHTML::text2('email','"+email.innerHTML+"') !!}";
	var ipID=getById('ipID_'+id).value;
	var ipArr=FormIPArr(getByClass(".ip_arr"));	
	ip.innerHTML=select('ipID',ipArr,ipID);
	coord.innerHTML="{!! MyHTML::text2('coordinator','"+coord.innerHTML+"') !!}";
	coord_c.innerHTML="{!! MyHTML::text2('coordinator_contact','"+coord_c.innerHTML+"') !!}";
	clearByClass(".edit_links");
	getById('edit_'+id).innerHTML='{!! MyHTML::submit("Save","btn btn-primary btn-sm")." ".MyHTML::link_to("locations/hubs","Cancel","btn btn-primary btn-sm") !!}';
	getById('form_id').action='hubs/update/'+id;
}

function FormIPArr(ip_arr){
	var arr=[" "];
	for(var i in ip_arr){
		var val=ip_arr[i].value;
		if(val!=undefined){
			var val2=val.split('_');
			arr[val2[0]]=val2[1];
		}		
	}
	return arr;
}


function addItem(){
	document.getElementById('save_btn_hide').style.display='inline-block';
	var tab=document.getElementById('tab_id');
	var rowCount = tab.rows.length;
	var row = tab.insertRow(rowCount);
	
	var cell0 = row.insertCell(0);
	var cell1 = row.insertCell(1);
	var cell2 = row.insertCell(2);
	var cell3 = row.insertCell(3);
	var cell4 = row.insertCell(4);
	var cell5 = row.insertCell(5);

	var cnt=rowCount;
	cell0.innerHTML="<input type='hidden' name='nrs["+cnt+"]' value="+cnt+">"+"<input class='input_sm' type='text' name='hubs[]' value=''>";
	cell1.innerHTML="<input class='input_sm' type='text' name='emails[]' value=''>";
	cell2.innerHTML='<?php if(isset($ips)) echo Form::select('ips[]',$ips) ?>';
	cell3.innerHTML="<input class='input_sm' type='text' name='coordinator[]' value=''>";
	cell4.innerHTML="<input class='input_sm' type='text' name='coordinator_contact[]' value=''>";
	cell5.setAttribute("class","rm_item");
    cell5.setAttribute("onClick","removeItem(this)");
    cell5.innerHTML="Remove";
    //count.value=countv;
}

 $(document).ready(function() {
  	$('#tab_id').DataTable();
  });
</script>

@endsection