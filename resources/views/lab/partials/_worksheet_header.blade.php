<!-- start Worksheet header  -->

    <?php

        // get worksheet creator's name & initials              
        $worksheet_number = "XX";
        $name = "--Unknown--";          
        $initials = "XX";
        $dc_human = "";
        $RunBatchNumber = "__zz__";
        $new_work_sheet = false;

        if(Input::has('ws')){

                $worksheet_number = Input::get('ws');
                $SQL = "SELECT  date_format(DateCreated, '%y%m%d') as date_created, 
                                                date_format(DateCreated, '%D of %M %Y') as  dc_human , 
                                                family_name, other_name 

                                FROM users, lab_worksheets 
                                        
                                        WHERE   users.id = lab_worksheets.CreatedBy
                                        AND     lab_worksheets.id = '$worksheet_number' ";      
                
                $rows = DB::select( $SQL );

                foreach ($rows as $user) {// I expect only 1 row
                        $name = $user->family_name . " " . $user->other_name;
                        $initials = substr($user->family_name, 0, 1) . substr($user->other_name, 0, 1);
                        $RunBatchNumber = $user->date_created . $initials . $worksheet_number;
                        $dc_human = $user->dc_human;
                }
        }else{
                $new_work_sheet = true;
        }
    ?>

    @if($new_work_sheet)
    <tr style='background:#dddddd;'>
            <tr class="even"><td colspan="6" align="center">
                    <H2>Make New Worksheet</H2>
            </td>
    </tr>

    @else
    <tr style='background:#dddddd;'>
            <tr class="even"><td colspan="6" align="center">
                    <H4>Worksheet No: {{ $worksheet_number }}</H4>
            </td>
    </tr>

    <tr class="even" style='background: #dddddd;'>
            <td class="comment style1 style4" colspan="6">
                    <center>
                            Created By:&nbsp;{{ $name }}&nbsp; on the {{ $dc_human }} &nbsp;&nbsp;&nbsp;[Run Batch No: {{ $RunBatchNumber }}] 
                    </center>
            </td>

    </tr>
    @endif
    
	<?php $html_attr["required"] = "yes"; ?>
	
	<tr class="even" style='background:#dddddd;'>
		<td align="right">HI2QCAP Kit Lot No:&nbsp;&nbsp;</td>
	  	<td> {!! Form::text('Kit_LotNumber', null,  $html_attr) !!} </td>	

		<td class="comment style1 style4" align="right">Spex Kit No:&nbsp;&nbsp;</td>
		<td> {!! Form::text('Kit_Number', null, $html_attr) !!} </td>	


		<?php $html_attr["class"] = "datepicker"; ?>
		<?php $html_attr["style"] = "width:100%"; ?>

		<td class="comment style1 style4" align="right">KIT EXP:&nbsp;&nbsp;</td>
		<td> {!! Form::text('Kit_ExpiryDate', null, $html_attr ) !!} </td>
	</tr>

	<tr style='background:#dddddd;'>
		<tr class="even"><td colspan="6">&nbsp;</td>
	</tr>

<!-- end header -->
