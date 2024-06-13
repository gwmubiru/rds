@extends('lab.createworksheet')

@section('header')

<!DOCTYPE html>
<html lang="en">

<?php

// echo getcwd();

	$userid=11; // $_SESSION['uid'] ;
	$accttype=5; // $_SESSION['accounttype'];
	$userlab=1;// $_SESSION['lab'];
	$labss=1; // $_SESSION['lab'];

	$key = 0; // $_GET['key'];

	$Lab = new LabFx;// side effect: loads DB connection used by mysqli_query()
	$db_conn = $Lab->GetDBconnection();



	//get the search variable
	// $searchparameter = $_GET['search']; cx--was not commented
	$top="Top";
	$side="Side";
	//get total batches for dispatch based on account type
	$totalbatchesfordipatch=$Lab->gettotalbatchesfordispatch($accttype,$userlab);


	//get total pending tasks based on account type
	$totaltasks=$Lab->gettotalpendingtasks($accttype);
	//get total batches waiting dispatch
	$totalbatcheswaitingapprovalbyclerk2 = $Lab->gettotalpendingbatches(0);


	$labname=$Lab->GetLabNames($userlab);//get lab name	


	if ($totaltasks != 0) {
		$d='<strong> [<font color="#FF0000"> '.$totaltasks .' </font>]</strong>';
	} else {
		$d='<strong> [ '.'0' .' ]</strong>';
	}

	if ($totalbatcheswaitingapprovalbyclerk2 != 0) {
		$numofbatches='<strong> ['.$totalbatcheswaitingapprovalbyclerk2 .']</strong>';
	} else {
		$numofbatches='<strong> ['.'0' .']</strong>';
	}

	if ($totalbatchesfordipatch != 0) {
		$totalbatchesfordipatch='<strong> ['.$totalbatchesfordipatch .']</strong>';
	} else {
		$totalbatchesfordipatch='<strong> ['.'0' .']</strong>';
	}

	// $sql=mysqli_query($db_conn, "update samples set rejectedreason=13 where rejectedreason=0 and receivedstatus=2") or die(mysql_error());// cx. unblock this	


	// ---------------------- end of part 1 -----------------------------------
?>

<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="description" content=""/>
<meta name="keywords" content="" />
<meta name="author" content="" />	
<link rel="stylesheet" type="text/css" href="/css/lab.css" media="screen" />
<title>EID UGANDA</title>

<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type='text/javascript' src='/js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<script type="text/javascript">
$().ready(function() {
	
	$("#sample").autocomplete("getsamples.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#sample").result(function(event, data, formatted) {
		$("#sampleid").val(data[1]);
	});
});
</script>


<script type="text/javascript">
$().ready(function() {
	
	$("#batch").autocomplete("getbatches.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#batch").result(function(event, data, formatted) {
		$("#batchid").val(data[1]);
	});
});
</script>

<script type="text/javascript">
$().ready(function() {
	
	$("#fname").autocomplete("get_facility.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#fname").result(function(event, data, formatted) {
		$("#fcode").val(data[1]);
	});
});
</script>

<script type="text/javascript">
$().ready(function() {
	
	$("#infantname").autocomplete("get_infantnames.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#infantname").result(function(event, data, formatted) {
		$("#infantcode").val(data[1]);
	});
});
</script>
<link rel="shortcut icon" href="../favicon.ico" >
<link rel="icon" type="image/gif" href="../animated_favicon1.gif" >
<script type="text/javascript" src="/js/reflection.js"></script> 
</head>

<body>
<div id="site-wrapper">
	<div id="header">
		<!--top-->
		<div id="top">
			<div class="left" id="logo"><img src="/images/moh_logo.jpg"/></div>
			<div align="right"><?php echo "Welcome". " <b> &laquo;Test-User&raquo;</b>". ' - '. $labname .'<br>'."<b>". date("l, d F Y")."</b>"; ?></div>
			<div class="clearer">&nbsp;</div>
		</div>
		<!--end top-->
		
		<!--menu-->
		<div class="navigation" id="sub-nav" style="display:none;">
			<ul class="tabbed">
				<?php

				if ($accttype != "") {
					//query for top bar
					$menuresult = mysqli_query($db_conn, "SELECT groupmenus.menu as 'topmenu' from groupmenus,menus where groupmenus.usergroup='$accttype' AND menus.ID=groupmenus.menu AND  menus.location='Top' ORDER BY  menus.order ASC, groupmenus.menu ASC" ) or die(mysql_error());

					//for the stakeholders
					if($accttype==9) {
						echo "<li><a href=\"labdashboard.stakeholder.php\">Interactive Map Statistics &nbsp;</a> |&nbsp</li>";
						echo "<li><a href=\"labdashboard.stakeholder.table.php\">Tabular Statistics &nbsp;</a> |&nbsp</li>";
					}

					while(list($topmenu) = mysqli_fetch_array($menuresult)) { 
						if($topmenu ==  33) {
							//national dashboard
							$title = $Lab->GetMenuName($topmenu);
							$link = $Lab->GetMenuUrl($topmenu);
							echo "<li>";
							echo "<a href=$link target='_blank'>$title &nbsp;</a> |&nbsp";
							echo"</li>";
						} else if ($topmenu ==  47) {
							//recycle bin
							//select only the samples count that have been deleted
							$permonthdelete = GetTotalDeletedSamplesPerMonth();
							$title = $Lab->GetMenuName($topmenu);
							$link = $Lab->GetMenuUrl($topmenu);
							echo "<li>";
							if ($permonthdelete == 0) {
								echo "<a href=$link>$title &nbsp;</a> |&nbsp";
							} else if ($permonthdelete > 0) {
								echo "<a href=$link>$title <small>[ <font color='#FF0000'><strong>$permonthdelete</strong></font> ]</small>&nbsp;</a> |&nbsp";
							}
							echo"</li>";
						} else { 
							$title = $Lab->GetMenuName($topmenu);
							$link = $Lab->GetMenuUrl($topmenu);
							echo "<li>";
							echo "<a href=$link>$title &nbsp;</a> |&nbsp";
							echo"</li>";
						}
					} 
				}
				?>
			</ul>
			<div class="clearer">&nbsp;</div>
		</div>
		<!--end menu-->
	</div>
	
	<?php
	//check if the key has any value
	//if key == 1 that shows that it is the lab manager logged in therefore do not show the sidebar
	if ($key == 1) {
		?>
		<div class="left sidebar" id="sidebar">
		<?php
	} else if ($key != 1) {
	?>
		<div class="left sidebar" id="sidebar" style="display:none">
		<div class="section">
				<div class="section-title" >Quick Menu</div>
				<!--side bar menu-->
				<div class="section-content">
					<ul class="nice-list">
					<?php  
					if ($accttype !="") {
						//query for side bar
						$result2 = mysqli_query($db_conn, "SELECT groupmenus.menu as 'sidemenu' 
														from groupmenus,menus 
															where groupmenus.usergroup='$accttype' AND 
																menus.ID=groupmenus.menu AND 
																	menus.location='Side' 
																		ORDER BY menus.order ASC, groupmenus.menu ASC") or die(mysql_error());
						$DD=mysqli_num_rows($result2);
						while(list($sidemenu) = mysqli_fetch_array($result2)) { 
							if($sidemenu == 36) {
								//pending tasks
								$menuname = $Lab->GetMenuName($sidemenu);
								$title= $menuname . $d;
								$link = $Lab->GetMenuUrl($sidemenu);
								echo "<li> <div class='left'>";
								echo "<a href=$link>$title &nbsp;</a> </div>";
								echo"<div class='clearer'>&nbsp;</div></li>";
								//echo "jina ".$title;
							} elseif ($sidemenu ==  35) {
								//dispatch results
								$menuname = $Lab->GetMenuName($sidemenu);
								$title= $menuname . $totalbatchesfordipatch;
								$link = $Lab->GetMenuUrl($sidemenu);
								echo "<li> <div class='left'>";
								echo "<a href=$link>$title &nbsp;</a> </div>";
								echo"<div class='clearer'>&nbsp;</div></li>";
								//echo "jina ".$title;
							} elseif ($sidemenu ==  8) {
								//verify batches
								$menuname = $Lab->GetMenuName($sidemenu);
								$title= $menuname . $numofbatches;
								$link = $Lab->GetMenuUrl($sidemenu);
								echo "<li> <div class='left'>";
								echo "<a href=$link>$title &nbsp;</a> </div>";
								echo"<div class='clearer'>&nbsp;</div></li>";
							} else if ($sidemenu ==  49) {
								//flagged worksheets
								$menuname = $Lab->GetMenuName($sidemenu);
								$title= $menuname . $d;
								$link = $Lab->GetMenuUrl($sidemenu);
								echo "<li> <div class='left'>";
								echo "<a href=$link>$title &nbsp;</a> </div>";
								echo"<div class='clearer'>&nbsp;</div></li>";
								//echo "jina ".$title;
							} else if ($sidemenu ==  9) {
								//create worksheet
								$workqury = "select ID,accessionno,patient,batchno,parentid,datereceived, IF(parentid > '0' OR parentid IS NULL, 0, 1) AS isnull  from samples  WHERE Inworksheet=0 AND ((receivedstatus !=2) and (receivedstatus !=4))   AND ((result IS NULL ) OR (result =0 )) AND status =1 AND Flag=1 ORDER BY isnull ASC,datereceived ASC,parentid ASC,ID ASC";			
								$workresult = mysqli_query($db_conn, $workqury) or die(mysql_error());
								$noofavailsamples=mysqli_num_rows($workresult); //no of samples
								if($noofavailsamples >  21 ) {
									$fcolor = "#FF0000";
								} else { 
									$fcolor = "#000000";
								}
								$menuname = $Lab->GetMenuName($sidemenu);
								$title= $menuname . ' <strong>[ <font color='.$fcolor.'>'.$noofavailsamples. ' </font>]</strong>';
								$link = $Lab->GetMenuUrl($sidemenu);
								echo "<li> <div class='left'>";
								echo "<a href=$link>$title &nbsp;</a> </div>";
								echo"<div class='clearer'>&nbsp;</div></li>";
							} else {
								$title = $Lab->GetMenuName($sidemenu);
								$link = $Lab->GetMenuUrl($sidemenu);
								echo "<li> <div class='left'>";
								echo "<a href=$link>$title &nbsp;</a> </div>";
								echo"<div class='clearer'>&nbsp;</div></li>";
							}
						} 
					}
					?>
					</ul>
				</div>
				<!--end side bar menu-->
			</div>
			<!--search form-->
			<div class="section">
			<?php  if (($accttype==1) || ($accttype==4) || ($accttype==5) || ($accttype==6) || ($accttype==9)) { ?>
			<div class="section-title"><small>Search Sample</small></div>
				<div class="section-content">
					<form autocomplete="off" method="post" action="search.php">
						<input name="sample" id="sample" type="text" class="text" size="12" />
						<input type="hidden" name="sampleid" id="sampleid" />&nbsp; 
						<input name="submit" type="submit" value="Go" class="button" style="width:30px; font-size:9px"/>
					</form>
				</div>
				<div class="section-title"><small>Search Batch</small></div>
				<div class="section-content">
                    <form autocomplete="off" method="post" action="searchbatch.php">
                        <input name="batch" id="batch" type="text" class="text" size="12" value="" />
                        <input type="hidden" name="batchid" id="batchid" />&nbsp; 
                        <input name="submit" type="submit" value="Go" class="button" style="width:30px; font-size:9px"/>
                    </form>
				</div>
				<div class="section-title"><small>Search Facility Batches</small></div>
				<div class="section-content">
                    <form autocomplete="off" method="post" action="searchbyfacility.php">
                        <input name="fname" id="fname" type="text" class="text" size="12" value="" />
                        <input type="hidden" name="fcode" id="fcode" />&nbsp; 
                        <input name="submit" type="submit" value="Go" class="button" style="width:30px; font-size:9px"/>
                    </form>
				</div>
				<div class="section-title"><small>Search By Infant Name</small></div>
				<div class="section-content">
                    <form autocomplete="off" method="post" action="searchbyinfantname.php">
                        <input name="infantname" id="infantname" type="text" class="text" size="12" value="" />
                        <input type="hidden" name="infantcode" id="infantcode" />&nbsp; 
                        <input name="submit" type="submit" value="Go" class="button" style="width:30px; font-size:9px"/>
                    </form>
				</div>
			</div>
			<?php } else if($accttype==2) { ?>	
			<div class="section-title">Search (all)</div>
			<div class="section-content">
                <form method="post" action="adminsearch.php">
                    <input name="search" type="text" class="text" size="15" />
                    <input name="submit" value="Go"  type="submit" class="button"/>
                </form>
            </div>
			</div>
			<?php } ?>
			<!--end search form-->
		<?php } ?>		
		<!--<div  class="center" id="main-content">-->
</div>

@stop


@section('update_worksheet')

<?php

	$worksheetno= empty($_REQUEST['ID']) ? "" : $_REQUEST['ID'];
	$worksheet = $Lab->getWorksheetDetails($worksheetno);

	extract($worksheet);			

	$datecreated=date("d-M-Y",strtotime($datecreated));
	$creator = $Lab->GetUserFullnames($createdby);
	$userid=11; // $_SESSION['uid'] ; //id of user who is updatin th record
?>

<style type="text/css">
select {
width: 250;}
</style>	<script language="javascript" src="/js/calendar.js"></script>

<link type="text/css" href="/css/calendar.css" rel="stylesheet" />	
		<SCRIPT language=JavaScript>
function reload(form)
{
	var val=form.cat.options[form.cat.options.selectedIndex].value;
	self.location='addsample.php?catt=' + val ;
}
</script>

<div  class="section">
	<div class="section-title">UPDATE TEST RESULTS  FOR WORKSHEET NO <?php echo $worksheetno; ?></div>
	<div class="xtop">
		

			<?php
			
if(isset($_POST['submit']))
{    $file1  = $_FILES['filename']['name'];
	 if  ($file1 =="" )
	{
		$error='<center>'."Please Select a Results CSV".'</center>';
	
		?>
		<table class='data-table'  >
  <tr class='even'>
    <td style="width:auto" ><div class="error"><?php 
		
echo  '<strong>'.' <font color="#666600">'.$error.'</strong>'.' </font>';

?></div></th>
  </tr>
</table>
<?php 

	print "<form action='' method='post' enctype='multipart/form-data'>";

	echo "<table border='0' class='data-table'>	
<tr class='even'>
		<th colspan='1'>
		<strong>Worksheet No</strong>		</th>
		<td colspan='2'><input name='work' type='text' id='work' value='$worksheetno'   readonly='' style = 'background:#FCFCFC;'  />	
		<input type='hidden' name='results' value='Save Results' />	</td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		Worklist Run Batch No		</td>
		<td colspan='2'><input name='runbatchno' type='text' id='runbatchno' value='$runbatchno'  style = 'background:#FCFCFC;' readonly=''  />	
		</td>
		</tr>	
<tr class='even'>
		<td colspan='1'>
		HIQCAP Kit No		</td>
		<td colspan='2'><input name='HIQCAP_KitNo' type='text' id='HIQCAP_KitNo'value='$HIQCAPNo'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		  	Spek Kit No		</td>
		<td colspan='2'><input name='Spek_Kit_No' type='text' id='Spek_Kit_No' value= '$Spekkitno'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		Date Created		</td>
		<td colspan='2'><input name='wdcreated' type='text' id='wdcreated' value='$datecreated'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>	
			<tr class='even'>
		<td colspan='1'>
		Created by		</td>
		<td colspan='2'><input name='createdby' type='text' id='wdcreated' value='$creator'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		Locate file name to import:		</td>
     		<td colspan='2'><input type=file name=filename></td>
		</tr>	
<tr class='odd'>
		<td colspan='3'>
		<input type='submit' name='submit' value='submit' class='button'></td>
     		
		</tr>	
		</table>";

     

      print "</form>";

} 
	
else if ($file1 !="" )
{
  			$work=$_POST['work'];
			$imagename = $_FILES['filename']['name'];
			$source = $_FILES['filename']['tmp_name'];
            $target = "ResultsCSVs/".$imagename;
            
            move_uploaded_file($source, $target);
			  
			$imagepath = $imagename;
			$new_string = ereg_replace("[^0-9]", "", $imagepath); //extract the uploaded file name


		if ($new_string != $work) //if selected file matches actual worksheet to be updated
		{

		$error='<center>'."Please Select The correct CSV file titled " .$work.".CSV" .'</center>';
		?>
		
		
			<table class='data-table'  >
  <tr class='even'>
    <td style="width:auto" ><div class="error"><?php 
		
echo  '<strong>'.' <font color="#666600">'.$error.'</strong>'.' </font>';

?></div></th>
  </tr>
</table>
<?php 

      print "<form action='' method='post' enctype='multipart/form-data'>";

echo "<table border='0' class='data-table'>	
<tr class='even'>
		<td colspan='1'>
		Worksheet No		</td>
		<td colspan='2'><input name='work' type='text' id='work' value='$worksheetno'  style = 'background:#FCFCFC;' readonly=''  />	
		<input type='hidden' name='results' value='Save Results' />	</td>
		</tr>
	<tr class='even'>
		<td colspan='1'>
		Worklist Run Batch No		</td>
		<td colspan='2'><input name='runbatchno' type='text' id='runbatchno' value='$runbatchno'  style = 'background:#FCFCFC;' readonly=''  />	
		</td>
		</tr>	
<tr class='even'>
		<td colspan='1'>
		HIQCAP Kit No		</td>
		<td colspan='2'><input name='HIQCAP_KitNo' type='text' id='HIQCAP_KitNo'value='$HIQCAPNo'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		  	Spek Kit No		</td>
		<td colspan='2'><input name='Spek_Kit_No' type='text' id='Spek_Kit_No' value= '$Spekkitno'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		Date Created		</td>
		<td colspan='2'><input name='wdcreated' type='text' id='wdcreated' value='$datecreated'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>	
			<tr class='even'>
		<td colspan='1'>
		Created	by	</td>
		<td colspan='2'><input name='createdby' type='text' id='wdcreated' value='$creator'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		Locate file name to import:		</td>
     		<td colspan='2'><input type=file name=filename></td>
		</tr>	
<tr class='odd'>
		<td colspan='3'>
		<input type='submit' name='submit' value='submit' class='button'></td>
     		
		</tr>	
		</table>";

     

      print "</form>";
		}
		else //work sheet match
		{

            $file = "ResultsCSVs/".$imagepath; //This is the original file 
			//echo  $file;
 			 $handle = fopen("$file", "r");

   			 while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){

   				$currentdate=date('Y-m-d'); //get current date

				$datereviewed=date("Y-m-d");
       
				if (($data[8] == "Target Not Detected") || ($data[8] == "Not Detected DBS"))
				{ //negative
	 
						$d=1;
				}
				else if  (($data[8] == "Detected DBS") || ($data[8] == 1) ||  ($data[8] == ">1") || ($data[8] == "1.00E+00") || ($data[8] == ">1.00E+00")      ) 
				{//positive
						$d=2;
				} 
				else
				{//failed/ indeterinate
						$d=3;
				}
				$dateoftest=date("Y-m-d",strtotime($data[3]));
				
	 		 $import = mysql_query("UPDATE samples
              SET result = '$d' ,datemodified = '$currentdate', datetested='$dateoftest'
			  			  WHERE (accessionno = '$data[4]')"); //   WHERE (ID = '$data[0]')"); 


    	 } //end while

    		 fclose($handle);
 			//update status of worksheet
 			$updateworksheetrec = mysql_query("UPDATE worksheets
             SET Updatedby='$userid' ,daterun='$dateoftest' , Flag=1 , reviewedby='$userid',datereviewed='$datereviewed'
			   			   WHERE (ID = '$work' )")or die(mysql_error());
						   
			$repeatresults = mysql_query("UPDATE pendingtasks
             			 SET  status  	 =  1 
			 			WHERE (worksheet='$work' AND task=9)")or die(mysql_error());
				
				
				unlink($file);				   
						   
    			// print "Import done";
								
				if($handle && $updateworksheetrec )
				{
					$tasktime= date("h:i:s a");
					$todaysdate=date("Y-m-d");
					
					//save activity of user
					$task = 7; //update results
					$activity = SaveUserActivity($userid,$task,$tasktime,$work,$todaysdate);

					$st="Import done, Results Updated successfully, Please Confirm and approve the updated results below";
					echo '<script type="text/javascript">' ;
					echo "window.location.href='confirmresults.php?p=$st&q=$work'";
					echo '</script>';
				}							//window.location.href='worksheet_list.php?p=$st';
			} //end if file name matches worksheet
 } // end if filename not null
 
 }// end if submitted
   else //not submiited
   {





      print "<form action='' method='post' enctype='multipart/form-data'>";

echo "<table border='0' class='data-table'>	
<tr class='even'>
		<td colspan='1'>
		Worksheet No		</td>
		<td colspan='2'><input name='work' type='text' id='work' value='$worksheetno'  style = 'background:#FCFCFC;' readonly=''  />	
		<input type='hidden' name='results' value='Save Results' />	</td>
		</tr>
		
		<tr class='even'>
		<td colspan='1'>
		Worklist Run Batch No		</td>
		<td colspan='2'><input name='runbatchno' type='text' id='runbatchno' value='$runbatchno'  style = 'background:#FCFCFC;' readonly=''  />	
		</td>
		</tr>	
<tr class='even'>
		<td colspan='1'>
		HIQCAP Kit No		</td>
		<td colspan='2'><input name='HIQCAP_KitNo' type='text' id='HIQCAP_KitNo'value='$HIQCAPNo'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		  	Spek Kit No		</td>
		<td colspan='2'><input name='Spek_Kit_No' type='text' id='Spek_Kit_No' value= '$Spekkitno'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		Date Created		</td>
		<td colspan='2'><input name='wdcreated' type='text' id='wdcreated' value='$datecreated'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>	
			<tr class='even'>
		<td colspan='1'>
		 Created by		</td>
		<td colspan='2'><input name='createdby' type='text' id='wdcreated' value='$creator'  style = 'background:#FCFCFC;' readonly=''  /></td>
		</tr>
		<tr class='even'>
		<td colspan='1'>
		Locate file name to import:		</td>
     		<td colspan='2'><input type=file name=filename></td>
		</tr>	
<tr class='odd'>
		<td colspan='3'>
		<input type='submit' name='submit' value='submit' class='button'></td>
     		
		</tr>	
		</table>";

     

      print "</form>";

   }
 
   ?>	
       
	
	
		</div>
		</div>
		

@stop