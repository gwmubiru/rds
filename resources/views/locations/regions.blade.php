
@extends('Admin.app')
@section('admin-content')
<div id="d6" class="panel panel-default">
	<div class="panel-heading"><b>Regions</b></div>
	<div class="panel-body">
		{!! Session::get('msge') !!}
		{!! Form::open(array('url'=>$post_url)) !!}
		<table class='table table-striped table table-condensed' id='tab_id'>
			<tr>
				<th width='2%'>#</th>
				<th>Region</th>
				<th></th>
			</tr>
			<?php $x=1 ?>
			
			@foreach ($regions AS $region)
			<tr>
				<td>{{$x++}}</td>
				<?php 
				$item_edit=false;
				if(isset($edit_id)) $item_edit=$edit_id==$region->id?true:false; 
				if($item_edit==true){
					echo "<td>".Form::text('region',$region->region)."</td>";
					echo "<td>".Form::submit('Save',array('class'=>'btn btn-primary'))."</td>";
				}else{
					echo "<td>".$region->region."</td>";
					echo "<td>".link_to('locations/regions/edit/'.$region->id,'Edit')."</td>";

				}

				?>
			</tr>
			@endforeach
		</table>

		<?php if(!isset($edit_id)) echo "<label class='add_item' onClick='addItem()'>Add</label>" ?><br>
		<div id='save_btn_hide'>{!! Form::submit('Save',array('class'=>'btn btn-primary')) !!}</div>
	  {!! Form::close() !!}
	</div>
</div>	

<script type="text/javascript">
function addItem(){
	document.getElementById('save_btn_hide').style.display='inline-block';
	var tab=document.getElementById('tab_id');
	var rowCount = tab.rows.length;
	var row = tab.insertRow(rowCount);
	
	var cell0 = row.insertCell(0);
	var cell1 = row.insertCell(1);
	var cell2 = row.insertCell(2);

	var cnt=rowCount;
	cell0.innerHTML=cnt+"<input type='hidden' name='nrs["+cnt+"]' value="+cnt+">";
	cell1.innerHTML="<input class='input_md' type='text' name='regions[]' value=''>";
	
	cell2.setAttribute("class","rm_item");
    cell2.setAttribute("onClick","removeItem(this)");
    cell2.innerHTML="Remove";
    //count.value=countv;
}
</script>

@endsection