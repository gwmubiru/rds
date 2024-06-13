
@extends('Admin.app')
@section('admin-content')
<div id="d7" class="panel panel-default">
<div class="panel panel-default">
	<div class="panel-heading"><b>Districts</b></div>
	<div class="panel-body ">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>$post_url,'id'=>'form_id')) !!}
		<a href="/excel">{!! MyHTML::tinyImg('excel.png',20,30) !!}Export to Excel</a>
		<table class='table table-striped table table-condensed' id='tab_id'>
		  <thead>
			<tr>
				<th >#</th>
				<th width='20%'>District No.</th>
				<th width='20%'>District</th>
				<th width='23%'>Region</th>
				<th width='20%'>SCD High Burden?</th>
				<th width='15%'></th>
			</tr>
		  </thead>
			<?php 
			$x=1;
			$excel_data=array();
			?>
			 <tbody>
			@foreach ($districts AS $district)
		 
			<tr>
				<td>{{$x++}}</td>
				<?php 
				echo "<td id=district_nr_".$district->id.">".$district->district_nr."</td>";
				echo "<td id=district_".$district->id.">".$district->district."</td>";
				echo "<td id=region_".$district->id.">".$district->region.MyHTML::hidden('region',$district->regionID,'regionID_'.$district->id)."</td>";
				echo "<td id=scd_high_burden_".$district->id.">".$district->scd_high_burden."</td>";
				echo "<td class='edit_links' id=edit_".$district->id.">".MyHTML::link_to('#','Edit','',"editRow($district->id)")."</td>";
				$excel_data[]=array(
					"District No"=>$district->district_nr,
					"District"=>$district->district,
					"Region"=>$district->region
					);
				?>
			</tr>		 
			@endforeach
			</tbody>
		</table>

		<?php if(!isset($edit_id)) echo "<label class='add_item' onClick='addItem()'>Add</label>" ?><br>
		<p><?php if(isset($regions)) foreach($regions as $region_id => $region_name) echo MyHTML::hidden("region_arr[$region_id]",$region_id."_".$region_name,null,'region_arr') ?></p>
		<div id='save_btn_hide'>{!! MyHTML::submit('Save') !!}</div>
		<?php session(["excel_data"=>$excel_data,"excel_file_name"=>"districts"]) ?>
	  {!! Form::close() !!}
	</div>
</div>	

<script type="text/javascript">
function editRow(id){
	var district_nr=getById('district_nr_'+id);
	var district=getById('district_'+id);
	var region=getById('region_'+id);
	var scd=getById('scd_high_burden_'+id);
	district_nr.innerHTML="{!! MyHTML::text2('district_nr','"+district_nr.innerHTML+"') !!}";
	district.innerHTML="{!! MyHTML::text2('district','"+district.innerHTML+"') !!}";
	scd.innerHTML=select('scd_high_burden',{'NO':'NO','YES':'YES'},scd.innerHTML);
	
	var regionID=getById('regionID_'+id).value;
	var regionArr=FormRegionArr(getByClass(".region_arr"));	
	region.innerHTML=select('regionID',regionArr,regionID);
	clearByClass(".edit_links");
	getById('edit_'+id).innerHTML='{!! MyHTML::submit("Save","btn btn-primary btn-sm")." ".MyHTML::link_to("locations/districts","Cancel","btn btn-primary btn-sm") !!}';
	getById('form_id').action='districts/update/'+id;
}

function FormRegionArr(region_arr){
	var arr=[];
	for(var i in region_arr){
		var val=region_arr[i].value;
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
	cell0.innerHTML=cnt+"<input type='hidden' name='nrs["+cnt+"]' value="+cnt+">";
	cell1.innerHTML="<input class='input_md' type='text' name='district_nr[]' value=''>";
	cell2.innerHTML="<input class='input_md' type='text' name='districts[]' value=''>";
	cell3.innerHTML='<?php if(isset($regions)) echo Form::select('regions[]',$regions) ?>';
	cell4.innerHTML='<?php if(isset($regions)) echo Form::select('scd_high_burden[]',["NO"=>"NO","YES"=>"YES"]) ?>';
	
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