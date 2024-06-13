<style type="text/css" media="print">
	.dontprint{ display: none; }
</style>
<div class="well dontprint" style="margin-top:1em; height:77px">

	<div class="col-md-4">
		
		
		<button type="submit" style="float:left; margin-right:5em;" class="btn btn-primary" id="make_new_worksheet">
				<span class="glyphicon glyphicon-th" aria-hidden="true"></span>
			Make New Worksheet
		</button>

	</div>

	<div class="col-md-4">
		<span id="s" style="display:none">Showing</span>
 		<select id='wtype' style="float:left" class="selectpicker" id="cx" title="Show Only..."> 		
    		<option data-hidden="true"></option>
			<option value='PENDING'>Worksheets Without Results </option>
			<option value='4REVIEW'>Worksheets Awaiting Review</option>
			<option value='COMPLETED'>Completed Worksheets</option>
			<!-- <option value='FLAGGED'>Flagged Worksheets</option> -->
			<option value='ALL' >Show All Worksheets</option>
		</select>

	</div>

	<div class="col-md-4">
		

		<div class="btn-group">
			<button 	type="button" 	style="float:right; margin-left: 0px;" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				Action <span class="caret"></span>
			</button>

			<ul class="dropdown-menu pull-right worksheet_actions" 	role="menu">

				<li><a 	action="view"	href="#"><span class="glyphicon glyphicon-th pull-left">&nbsp;</span>View</a></li>
				<li><a 	action="toPDF" 	href="#"><span class="glyphicon glyphicon-save-file pull-left">&nbsp;</span>Download PDF</a></li>
					
					<li class="divider"></li>
				<li><a 	action="edit" 	href="#"><span class="glyphicon glyphicon-pencil pull-left">&nbsp;</span>Edit</a></li>
				<li><a 	action="uploadFile" href="#"><span class="glyphicon glyphicon-open-file pull-left">&nbsp;</span>Upload Results</a></li>
					
					<li class="divider"></li>
				<li><a 	action="delete"	href="#"><span style="color:red;" class="glyphicon glyphicon-remove pull-left">&nbsp;</span>Delete</a></li>
			</ul>

			<select  style="float:right; width: 5em;" data-live-search="true" class="selectpicker" id="cx2" name="selValue">
					
					<option>Select a Worksheet</option>

				@foreach($selected_worksheets as $w)
					<option value="{{ $w->id }}">{{ $w->id }}</option>
				@endforeach

			</select>
		</div>

	</div>
</div>

  	<script type="text/javascript">

  		$(function (){

			$('.buttonset-rd').bsFormButtonset('attach');

			$('.selectpicker').selectpicker({style: 'btn-default'});


			$("#cx2").change(function(){

				var worksheet_number = parseInt(this.value);
				var newText = (isNaN(worksheet_number)) ? this.value : "Worksheet #: " + worksheet_number;

				$('button[data-id=cx2]').find('span.filter-option.pull-left').text( newText );
			});

			$("#wtype").change(function(){
				return window.location.assign("/wlist?wtype="+this.value);
			})



// from /wlist
			var worksheet_from_menu = document.getElementById('cx2');

			$('.worksheet_actions li > a').click(function () {

				var __this = $(this);
				var action = __this.attr("action");
				var target = __this.attr("worksheet");


				if(	target === undefined ){// via menu
					
					target = parseInt(worksheet_from_menu.value);
					
					if(isNaN(target)){
						alert("Please select a worksheet");
						return false;
					}
				} 

				doWorksheetAction(action, target);
			});

			function doWorksheetAction(action, target){

				switch(action){
					case "create" : location = "/w";  return;
					case "edit"	: 	location = "/ws?i=edit&ws="+target;  return;
					case "view"	: 	location = "/ws?i=view&ws="+target;  return;
					case "toPDF": 	location = "/ws?i=toPDF&ws="+target; return;
					case "uploadFile" :	location = "/ws?i=uploadFile&ws="+target; return;
					case "saveFile":location = "/ws?i=saveFile&ws="+target; return;	
					case "delete" : location = "/ws?i=del&ws="+target; return;
				}
			}

			$("#make_new_worksheet").click(function (evt) {
				doWorksheetAction("create");
			});
  		});

  	</script>
