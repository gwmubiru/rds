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


@section('worksheet')

<?php 
// echo "start:";
	define('IN_CB',true);
	define('VERSION', '2.1.0');

	if(!function_exists('imagecreate'))
		exit('Sorry, make sure you have the GD extension installed before running this script.');
// echo "a";

// include_once('/php/lib/tc_calendar.php');


	$userid=11;// $_SESSION['uid'] ;	
	$initials='Y.T';// strtoupper($_SESSION['initials']);	
	$leo=date('Ymd');	
	$worksheetrunno=$leo.$initials;

// echo "b";
	
	$creator = $Lab->GetUserFullnames($userid);

// echo $creator;

	$errmsg_arr = array();// Array to store validation errors
	$errflag = false;// Validation error flag

	$currentday = date('Y-m-d') +1 ;	

// echo "1";

	if (Request::has('SaveWorksheet'))
	{	
		$worksheetno = Request::input('worksheetno');
		$worksheetrunno = Request::input('worksheetrunno');
		$lotno = Request::input('lotno');
		$hiqcap = Request::input('hiqcap');
		$rackno = Request::input('rackno');
		$spekkitno = Request::input('spekkitno');
		$labcode = Request::input('labcode');
		$sample = Request::input('sample');
		$datecreated = date('d-m-Y');
		$kitexp = Request::input('kitexp');
		$kitexp = date("Y-m-d",strtotime($kitexp)); //convert to yy-mm-dd
		$datecut = Request::input('datecut');
		$datecut = date("Y-m-d",strtotime($datecut)); //convert to yy-mm-dd


		//save worksheet details
		$worksheetdetailsrec ="INSERT INTO worksheets
									(ID, runbatchno, datecreated, HIQCAPNO, spekkitno, createdby,
										Lotno, Rackno, kitexpirydate, datecut, lab)
								VALUES
									('$worksheetno','$worksheetrunno','$datecreated','$hiqcap',
										'$spekkitno','$userid','$lotno','$rackno','$kitexp','$datecut','$labss')";
					
		$worksheetdetail = @mysqli_query($worksheetdetailsrec) or die(mysql_error());

		foreach($labcode as $t => $b)
		{
		
		
		// update sample record
		$samplerec = mysqli_query(	"UPDATE samples
					  					SET Inworksheet = 1,  worksheet='$worksheetno'
											WHERE (accessionno = '$labcode[$t]')")
		 				or die(mysql_error());
		
		// update pending tasks
		$repeatresults = mysqli_query($db_conn, "UPDATE pendingtasks SET status = 1 
										WHERE (sample='$labcode[$t]' AND task=3)")
						or die(mysql_error());
		}

		$activity = "INSERT INTO pendingtasks(task,worksheet,status,lab)
						VALUES (9,'$worksheetno',0,'$labss')";
		
		$pendingactivity = @mysqli_query($db_conn, $activity) or die(mysql_error());

// echo "2";
		$st = "";
					
		if ($worksheetdetail && $samplerec && $pendingactivity) //check if all records entered
		{
			$tasktime= date("h:i:s a");
			$todaysdate=date("Y-m-d");
			
			//save activity of user
			$task = 5; //create worksheet
			$activity = $Lab->SaveUserActivity($userid,$task,$tasktime,$worksheetno,$todaysdate);

			$disable="Sample: ";
			echo '<script type="text/javascript">' ;
			echo "window.open('downloadworksheet.php?ID=$worksheetno','_blank')";
			echo '</script>';
		}
		else
		{
				$st="Worksheet Save Failed, try again ";
		}
	}
// echo "3";
?>
<style type="text/css">
select {
width: 250;}
</style>	
<script type="text/javascript" src="/js/validation2.js"></script>
<link rel="stylesheet" href="/css/validation.css" type="text/css" media="screen" />

<script language="javascript" src="/js/calendar.js"></script>
<link type="text/css" href="/css/calendar.css" rel="stylesheet" />	
		<link href="/css/jquery-ui.css" rel="stylesheet" type="text/css"/>
 
  <script src="/js/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="/css/demos.css">
  <script>
  $(document).ready(function() {
   // $("#dob").datepicker();
	$( "#kitexp" ).datepicker({ minDate: "-5D", maxDate: "+5Y" });
	});


//  });
  </script>
  <script>
  $(document).ready(function() {
   // $("#datecollected").datepicker();
$( "#datecut" ).datepicker({ minDate: "-7D", maxDate: "+0D" });

  });
  </script>

<script language="JavaScript">
function submitPressed() {
document.worksheetform.SaveWorksheet.disabled = true;
//stuff goes here
document.worksheetform.submit();
}
</script> 
		<style type="text/css">
<!--
.style1 {font-weight: bold}
-->
        </style>
		<div  class="section">
		<div class="section-title">CREATE WORKLIST / WORKSHEET </div>
		<div class="xtop">
			<table><tr><td>
		 <p><font color="#FF0000">Please enter the batch serial number after the initials in the Worklist Run Batch No Section</font>
		</td></tr>
		</table>
<?php
	$st = empty($st) ? "" : $st; // added by cX to suppress Err msg
		if ($st !="")
		{
?> 
		<table   >
  <tr>
    <td style="width:auto" >
    	<div class="error">
    		<?php echo  '<strong>'.' <font color="#666600">'.$st.'</strong>'.' </font>'; ?>
    	</div>
    </td>
  </tr>
</table>
<?php } ?>


<?php //select 22 samples for testing

// echo "22";

$qury = "select ID, accessionno, pcr, patient, batchno, parentid, datereceived, 
			IF(parentid > '0' OR parentid IS NULL, 0, 1) AS isnull  
		from samples  
		WHERE Inworksheet=0 AND ((receivedstatus !=2) and (receivedstatus !=4))   AND ((result IS NULL ) OR (result =0 )) AND status =1 AND Flag=1
			ORDER BY isnull ASC, datereceived ASC, parentid ASC, ID ASC
			LIMIT 0, 4";
			$result = mysqli_query($db_conn, $qury) or die(mysql_error());
			$no=mysqli_num_rows($result); //no of samples
// echo "22: got ... $no samples";		
		if ($no <= 22)
		{ $worksheetno = $Lab->GetNewWorksheetNo(); 
// echo "1 after 22";			
$waksheetdate=date('Y-m-d');
$worksheerrunbatchno=$waksheetdate;
		?>
		<form  method="post" action="" id="customForm">
		<table  border="0" class="data-table" align="center">
		<tr class="even">
		<th  >
		Worksheet / Template No		</th>
		<td >
		  <span class="style5"><?php echo $worksheetno; ?>
		  	<input name="worksheetno" type="hidden" id="worksheetno" value="<?php echo $worksheetno; ?>"   readonly="" style = 'background:#F6F6F6;'  />
		  </span>
		</td>
</tr>
			<tr class="even">
		<th >
		Worklist Run Batch No		</th>
		<td class="comment">
		  <span class="style5"><input name="worksheetrunno" type="text" id="worksheetrunno" value="<?php echo $worksheetrunno; ?>"    style="width:124px" class="text"/></span></td>
		<th class="comment style1 style4">
		Date Created		</th>
		<td class="comment" ><?php $currentdate=date('d-M-Y'); echo  $currentdate ; //get current date ?></td>
		<th >HIQCAP Kit Lot No</th>
		  <td><div>
		    <input name="hiqcap" type="text" id="hiqcap" value=""  style="width:129px" class="text" />
		    <br />
		  <span id="hiqcapInfo"></span></div></td>	
		</tr>
		
<tr class="even">
		<th>
		Created By	    </th>
		<td>
	    <?php  echo $creator ?>		</td><th>
	  	  Spex Kit No		</th>
		<td  colspan="">
		 <div> <input name="spekkitno" type="text" id="spekkitno" value=""  style="width:124px"  class="text"  /> <span id="spekkitnoInfo"></span></div></td>
		<th>KIT EXP</th>
		<td><div>
			<p> <input id="kitexp" type="text" name="kitexp" class="text"  style="width:129px" ><span id="kitexpInfo"></span></div></p>


<div type="text" id="kitexp">
</div></td>
  </tr><tr >
		
		</tr>
		
			<tr class="even">
		<td colspan="7" >&nbsp;		</td>
		</tr>

<tr> 
<?php
// echo "2...";

	 $count = 1;
$colcount=1;
for($i = 1; $i <= 2; $i++) 
{
		if ($count==1)
		{
		$pc="<div align='right'>
	 	<table><tr><td><small>1</small></td></tr></table></div><div align='center'>Negative Control<br><strong>NC</strong></div>";
		}
		elseif ($count==2)
		{
		$pc="<div align='right'><table></div><tr><td><small>2</small></td></tr></table><div align='center'>Positive Control<br><strong>PC</strong></div>";
		}
		
				$RE= $colcount%6;
?>
             <td height="50" > <?php echo $pc; ?> </td><?php	 
	

       $count ++;         
	$colcount ++;
			
}
$scount = 2;
 while(list($ID,$accessionno,$pcr,$patient,$batchno,$parentid,$datereceived) = mysqli_fetch_array($result))
	{  
	$scount = $scount + 1;
	$paroid = $Lab->GetParentID($accessionno,$labss);//get parent id
	
if ($paroid =="0")
{
$paroid="";
$previouswsheet="";
$labnodesc="Lab no:";
$rerundetails="";
}
else
{
$previouswsheet = $Lab->GetWorksheetnoforParentID($paroid,$labss);
$paroid=" <b>".$paroid."</b> " ;
$labnodesc="New Lab no:";
$rerundetails= '<br> Parent Lab No:  '. $paroid .'<br> Previous Run Worksheet #: '.   " <b>". $previouswsheet ."</b> ";
}
		
		$RE= $colcount%6;
		
		 ?>
		
	

  
     
     <td width='178px'>
	 <div align="right">
	 <table><tr><td><small>
	 <?php echo $scount;?></small>
	 </td></tr></table></div>
	 Patient ID:  <input name='patient[]' type='text' id='patient' value='<?php echo $patient; ?>' size='14' readonly='' style = 'background:#F6F6F6;'> <br>  Batch no:  <input name='batchno[]' type='text' id='batchno' value='<?php echo $batchno; ?>' size='6' readonly='' style = 'background:#F6F6F6;'> <br>PCR: <?php echo $pcr; ?><br> <?php echo $labnodesc; ?>  <input name='labcode[]' type='text' id='labcode' value='<?php echo $accessionno; ?>' size='10' readonly='' style = 'background:#F6F6F6;'> <?php echo $rerundetails; ?>
	<?php 
		echo "<img style='display:none;' src='/bc?code=code128&o=2&dpi=50&t=50&r=1&rot=0&text=$accessionno&f1=Arial.ttf&f2=0&a1=&a2=B&a3='/>";
		echo "<img src='$accessionno.jpg'>";
	?>
	</td>
<?php  $colcount ++;
		 
		
             if ($RE==0)
			 { 
			?>
	
       
   
     </tr>
<?php
		 }//end if modulus is 0
	 }//end while?>



<tr >
            <th  colspan="7" ><center>
			
			    <input type="submit" name="SaveWorksheet" value="Save & Print Worksheet" class="button"  />
				
            </center></th	>
          </tr>
</table>
	
   
	</tr> 

		    
		</form>
		<?php }
		else
		{?>
		<table   >
  <tr>
    <td style="width:auto" ><div class="notice"><?php 
		
echo  '<strong>'.' <font color="#666600">'. 'No Enough Samples to run a test['. $no. ']'.'</strong>'.' </font>';

?></div></th>
  </tr>
</table>
		
	<?php	}
		?>
		
		</div>
		</div>

@stop