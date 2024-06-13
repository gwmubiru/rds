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

@section('worksheet_list')
<?php 
// session_start();
$userid=11; // $_SESSION['uid'] ; //id of user who is updatin th record
$accounttype = 5;// $_SESSION['accounttype'];
// require_once('../connection/config.php');
// require_once('classes/tc_calendar.php');
$success = empty($_GET['p']) ? "0" : $_GET['p'];
$wtype = empty($_GET['wtype']) ? "0" : $_GET['wtype']; // Use this line or below line if register_global is off


?>
<?php 
	// include('../includes/header.php');
?>
<style type="text/css">
select {
width: 250;}
</style>	<script language="javascript" src="calendar.js"></script>

<link type="text/css" href="calendar.css" rel="stylesheet" />	
		<SCRIPT language=JavaScript>
function reload(form)
{
var val=form.cat.options[form.cat.options.selectedIndex].value;
self.location='addsample.php?catt=' + val ;
}
</script>

		<div  class="section">
		<div class="section-title">WORKSHEET LIST </div>
		<div class="xtop">
<table>
<tr>

<!-- Adapted from includes/worksheetheader.php -->

<td><small>Key:  <strong> Pending Worksheets :-</strong> <font color="#FF0000"> Awaiting Results Update </font> | <strong> Flagged Worksheet </strong>  <font color="#FF0000"> :- Awaiting Review by Lab Manager due to discrepancies in Results </font> </td>
</tr>
</table>

<table>
	<tr> 			
	<td colspan="10"><strong><?php echo "Total Complete Worksheets: [ " . $Lab->Gettotalcompleteworksheets() . " ]";?></strong>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  View:- &nbsp;&nbsp;&nbsp;&nbsp;<a href="worksheetlist.php">All </a>  |  <a href="worksheetlist.php?wtype=<?php echo "0";?>">Pending Worksheets </a>  |  <a href="worksheetlist.php?wtype=<?php echo "1";?>"> Complete Worksheets </a>  | <a href="worksheetlist.php?wtype=<?php echo "2";?>"> Flagged Worksheets </a>  
	</td>
	</tr>
</table>

<!-- END: includes/worksheetheader.php -->


		<?php
		// include ('../includes/worksheetheader.php');
		
		 if ( !empty($success) )
				{
				?>
<table>
				  <tr>
					<td style="width:auto" >
					<div class="success">
					<?php echo  '<strong>'.' <font color="#666600">'.$success.'</strong>'.' </font>';?>
					</div>
					</td>
				  </tr>
		  </table>
				<?php } ?>

	 <?php 
	if (isset($wtype))
	{
   		if ($wtype==1) //complete worksheets
		{
		$rowsPerPage = 15; //number of rows to be displayed per page

		// by default we show first page
		$pageNum = 1;

		// if $_GET['page'] defined, use it as page number
		if(isset($_GET['page']))
		{
		$pageNum = $_GET['page'];
		}

// counting the offset
$offset = ($pageNum - 1) * $rowsPerPage;
//query database for all districts
   $qury = "SELECT ID,runbatchno,datecreated,HIQCAPNo,spekkitno,createdby,Flag,daterun,datereviewed
            FROM worksheets
			WHERE Flag=1
			ORDER BY ID DESC
			LIMIT $offset, $rowsPerPage";
			
			$result = mysqli_query($db_conn, $qury) or die(mysql_error()); //for main display
			$result2 = mysqli_query($db_conn, $qury) or die(mysql_error()); //for calculating samples with results and those without

$no=mysqli_num_rows($result);



if ($no !=0)
{
// print the districts info in table
echo '<table border="0"   class="data-table">
            
 <tr ><th>Worksheet No</th><th>Worklist Run Batch No	</th><th>Date Created</th><th>Created By</th><th>No. of Samples</th><th>HIQ CAP No</th><th>Spek Kit No</th><th>Date Run</th><th>Date Reviewed</th><th>Task</th></tr>';
	while(list($ID,$runbatchno,$datecreated,$HIQCAPNo,$spekkitno,$createdby,$Flag,$daterun,$datereviewed) = mysqli_fetch_array($result))
	{  
	
		if ($type==0)
		{
		//get number of sampels per  worksheet
		$numsamples=$Lab->GetSamplesPerworksheet($ID);
		}
		else
		{
		$numsamples=$Lab->GetRepeatSamplesPerworksheet($worksheet);
		}
		if ($daterun !="")
		{
		$daterun=date("d-M-Y",strtotime($daterun));
		}
		if ($datereviewed !="")
		{
		$datereviewed=date("d-M-Y",strtotime($datereviewed));
		}
		$datecreated=date("d-M-Y",strtotime($datecreated));
$creator=$Lab->GetUserFullnames($createdby);

$d="<a href=\"completeworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this batch'>View Details</a> | <a href=\"downloadcompleteworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a> ";



	echo "<tr class='even'>
			<td >$ID</td>
			<td>$runbatchno</td>
			<td >$datecreated</td>
			<td >$creator </td>
			<td > $numsamples</td>
		
			<td >$HIQCAPNo</td>
			<td >$spekkitno</td>
			<td >$daterun</td>
			<td >$datereviewed</td>
			<td >$d</td>
			
			
			
	</tr>";
	}
	echo '</table>';
	?>
		<?php
	echo '<br>';
	$numrows=$Lab->Gettotalcompleteworksheets(); //get total no of batches

	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);

// print the link to access each page
$self = $_SERVER['PHP_SELF'];
$nav  = '';
for($page = 1; $page <= $maxPage; $page++)
{
   if ($page == $pageNum)
   {
      $nav .= " $page "; // no need to create a link to current page
   }
   else
   {
      $nav .= " <a href=\"$self?page=$page\">$page</a> ";
   }
}

// creating previous and next link
// plus the link to go straight to
// the first and last page

if ($pageNum > 1)
{
   $page  = $pageNum - 1;
   $prev  = " <a href=\"$self?page=$page\">[Prev]</a> ";

   $first = " <a href=\"$self?page=1\">[First Page]</a> ";
}
else
{
   $prev  = '&nbsp;'; // we're on page one, don't print previous link
   $first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
   $page = $pageNum + 1;
   $next = " <a href=\"$self?page=$page\">[Next]</a> ";

   $last = " <a href=\"$self?page=$maxPage\">[Last Page]</a> ";
}
else
{
   $next = '&nbsp;'; // we're on the last page, don't print next link
   $last = '&nbsp;'; // nor the last page link
}

// print the navigation link
echo '<center>'. ' Page ' .$first . $prev . $nav . $next . $last .'</center>';


}

else
{

?>
<table   >
  <tr>
    <td style="width:auto" ><div class="notice"><?php 
		
echo  '<strong>'.' <font color="#666600">'.'No Completed Worksheets '.'</strong>'.' </font>';

?></div></th>
  </tr>
</table><?php

 }  
					}
					
				
			   		elseif ($wtype == 0) //pendin worksheets
					{
					  $rowsPerPage = 15; //number of rows to be displayed per page

// by default we show first page
$pageNum = 1;

// if $_GET['page'] defined, use it as page number
if(isset($_GET['page']))
{
$pageNum = $_GET['page'];
}

// counting the offset
$offset = ($pageNum - 1) * $rowsPerPage;
//query database for all districts
   $qury = "SELECT ID,runbatchno,datecreated,HIQCAPNo,spekkitno,createdby,Lotno,Rackno,Flag,daterun,datereviewed
            FROM worksheets
			WHERE Flag=0
			ORDER BY ID DESC
			LIMIT $offset, $rowsPerPage";
			
			$result = mysqli_query($db_conn, $qury) or die(mysql_error()); //for main display
			$result2 = mysqli_query($db_conn, $qury) or die(mysql_error()); //for calculating samples with results and those without

$no=mysqli_num_rows($result);



if ($no !=0)
{  
// print the districts info in table
echo '<table border="0"   class="data-table">
            
 <tr ><th><small>Worksheet No</small></th><th><small> Run Batch No	</small></th><th><small>Date Created</small></th><th><small>Created By</small></th><th><small>No. of Samples</small></th><th><small>HIQ CAP No</small></th><th><small>Spek Kit No</small></th><th><small>Date Run</small></th><th><small>Date Reviewed</small></th><th><small>Task</small></th></tr>';
	while(list($ID,$runbatchno,$datecreated,$HIQCAPNo,$spekkitno,$createdby,$Lotno,$Rackno,$Flag,$daterun,$datereviewed) = mysqli_fetch_array($result))
	{  
	
	
		//get number of sampels per  worksheet
		$numsamples=$Lab->GetSamplesPerworksheet($ID);
		
		if ($daterun !="")
		{
		$daterun=date("d-M-Y",strtotime($daterun));
		}
		if ($datereviewed !="")
		{
		$datereviewed=date("d-M-Y",strtotime($datereviewed));
		}
		$datecreated=date("d-M-Y",strtotime($datecreated));
$creator=$Lab->GetUserFullnames($createdby);

$wsheet="General worksheet";
$showwsheets="<a href=\"worksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this batch'>View Details</a> | <a href=\"downloadworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a> | <a href=\"deleteworksheet.php" ."?ID=$ID" . "\" title='Click to Delete Worksheet' OnClick=\"return confirm('Are you sure you want to delete Worksheet $ID');\" >Delete Worksheet  </a> | <a href=\"updateresults.php" ."?ID=$ID" . "\" title='Click to Update Results Worksheet' > Update Results </a>";







	echo "<tr class='even'>
			<td><a href=\"worksheetDetails.php" ."?ID=$ID" . "\" title='Click to view  Samples in this batch'>$ID</a></td>
				<td >$runbatchno</td><td >$datecreated</td>
			<td>$creator </td>
						<td > $numsamples</td>
		
			<td>$HIQCAPNo</td>
			<td>$spekkitno</td>
				<td >$daterun</td>
			<td>$datereviewed</td>
			<td> $showwsheets	</td>
			
			
			
	</tr>";
	}
	echo '</table>';
	?>
		<?php
	echo '<br>';
	$numrows=$Lab->GettotalPendingworksheets(); //get total no of batches

	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);

// print the link to access each page
$self = $_SERVER['PHP_SELF'];
$nav  = '';
for($page = 1; $page <= $maxPage; $page++)
{
   if ($page == $pageNum)
   {
      $nav .= " $page "; // no need to create a link to current page
   }
   else
   {
      $nav .= " <a href=\"$self?page=$page\">$page</a> ";
   }
}

// creating previous and next link
// plus the link to go straight to
// the first and last page

if ($pageNum > 1)
{
   $page  = $pageNum - 1;
   $prev  = " <a href=\"$self?page=$page\">[Prev]</a> ";

   $first = " <a href=\"$self?page=1\">[First Page]</a> ";
}
else
{
   $prev  = '&nbsp;'; // we're on page one, don't print previous link
   $first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
   $page = $pageNum + 1;
   $next = " <a href=\"$self?page=$page\">[Next]</a> ";

   $last = " <a href=\"$self?page=$maxPage\">[Last Page]</a> ";
}
else
{
   $next = '&nbsp;'; // we're on the last page, don't print next link
   $last = '&nbsp;'; // nor the last page link
}

// print the navigation link
echo '<center>'. ' Page ' .$first . $prev . $nav . $next . $last .'</center>';


}//end for

else //no pendng worksheets
{

?>
<table   >
  <tr>
    <td style="width:auto" ><div class="notice"><?php 
		
echo  '<strong>'.' <font color="#666600">'.'No Pending Worksheets'.'</strong>'.' </font>';

?></div></th>
  </tr>
</table><?php

 }  //end for
}//end if type=0
			   
			   			 
				elseif ($wtype == 2) //flagged worksheets
					{
					  	$rowsPerPage = 15; //number of rows to be displayed per page


						if(isset($_GET['page'])){ // use it as page number
							$pageNum = $_GET['page'];
						}else{
							$pageNum = 1;	
						}

// counting the offset
$offset = ($pageNum - 1) * $rowsPerPage;
//query database for all districts
   $qury = "SELECT ID,runbatchno,datecreated,HIQCAPNo,spekkitno,createdby,Flag,daterun,datereviewed
            FROM worksheets
			WHERE Flag=2
			ORDER BY ID DESC
			LIMIT $offset, $rowsPerPage";
			
			$result = mysqli_query($db_conn, $qury) or die(mysql_error()); //for main display
			$result2 = mysqli_query($db_conn, $qury) or die(mysql_error()); //for calculating samples with results and those without

$no=mysqli_num_rows($result);



if ($no !=0)
{ 

// print the districts info in table
echo '<table border="0"   class="data-table">
    	<tr>
    		<th><small>Worksheet No</small></th>
    		<th><small>Worklist Run Batch No</small></th>
    		<th><small>Date Created</small></th>
    		<th><small>Created By</small></th>
    		<th><small>No. of Samples</small></th>
    		<th><small>HIQ CAP No</small></th>
    		<th><small>Spek Kit No</small></th>
    		<th><small>Date Run</small></th>
    		<th><small>Date Reviewed</small></th>
    		<th><small>Status</small></th>
    		<th><small>Task</small></th>
    	</tr>';
	while($row = mysqli_fetch_array($result))
	{  
	
		list($ID,$runbatchno,$datecreated,$HIQCAPNo,$spekkitno,$createdby,$Flag,$daterun,$datereviewed) = $row;
	
		//get number of sampels per  worksheet
		$numsamples=$Lab->GetSamplesPerworksheet($ID);
		
		if ($daterun !="")
		{
		$daterun=date("d-M-Y",strtotime($daterun));
		}
		if  ($datereviewed !="")
		{
		$datereviewed=date("d-M-Y",strtotime($datereviewed));
		}
		else
		{
		$datereviewed="";
		}
		$datecreated=date("d-M-Y",strtotime($datecreated));
$creator=$Lab->GetUserFullnames($createdby);

$wsheet="General worksheet";
//$d2="<a href=\"flaggedworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this batch'>View Details</a>   </a>";



if ($_SESSION['accounttype'] != 6)
	{
	$d2=" <a href=\"flaggedworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this worksheet'>View Details</a> | <a href=\"downloadflaggedworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a>  ";

$d3="  <strong><font color='#FF0000'><small> Under Review</small> </font></strong>";
	}
	else
	{
	$d2=" <a href=\"flaggedworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this worksheet'>View Details</a> | <a href=\"downloadflaggedworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a> | <a href=\"reviewworksheet.php" ."?ID=$ID" . "\" title='Click to Review Worksheet'> Review Worksheet </a>  ";

$d3=" <strong><font color='#FF0000'><small> Awaiting Review </small></font></strong>  ";


	}



	echo "<tr class='even'>
			<td ><a href=\"flaggedworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view  Samples in this batch'>$ID</a></td>
				<td >$runbatchno</td><td >$datecreated</td>
			<td >$creator </td>
						<td > $numsamples</td>
		
			<td >$HIQCAPNo</td>
			<td >$spekkitno</td>
				<td >$daterun</td>
			<td >$datereviewed</td>
			<td >$d3</td>
			<td > $d2  	</td>
			
			
			
	</tr>";
	}
	echo '</table>';
	?>
		<?php
	echo '<br>';
	$numrows=$Lab->Gettotalworksheetsunderreview(); //get total no of batches

	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);

// print the link to access each page
$self = $_SERVER['PHP_SELF'];
$nav  = '';
for($page = 1; $page <= $maxPage; $page++)
{
   if ($page == $pageNum)
   {
      $nav .= " $page "; // no need to create a link to current page
   }
   else
   {
      $nav .= " <a href=\"$self?page=$page\">$page</a> ";
   }
}

// creating previous and next link
// plus the link to go straight to
// the first and last page

if ($pageNum > 1)
{
   $page  = $pageNum - 1;
   $prev  = " <a href=\"$self?page=$page\">[Prev]</a> ";

   $first = " <a href=\"$self?page=1\">[First Page]</a> ";
}
else
{
   $prev  = '&nbsp;'; // we're on page one, don't print previous link
   $first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
   $page = $pageNum + 1;
   $next = " <a href=\"$self?page=$page\">[Next]</a> ";

   $last = " <a href=\"$self?page=$maxPage\">[Last Page]</a> ";
}
else
{
   $next = '&nbsp;'; // we're on the last page, don't print next link
   $last = '&nbsp;'; // nor the last page link
}

// print the navigation link
echo '<center>'. ' Page ' .$first . $prev . $nav . $next . $last .'</center>';


}

else
{

?>
<table   >
  <tr>
    <td style="width:auto" ><div class="notice"><?php 
		
echo  '<strong>'.' <font color="#666600">'.'No Flagged Worksheets'.'</strong>'.' </font>';

?></div></th>
  </tr>
</table><?php

 }  
								   
			   
			   
				}
	}//end if wtype set
			else //wtype not set
			{
		 
       

	
   $rowsPerPage = 15; //number of rows to be displayed per page

// by default we show first page
$pageNum = 1;

// if $_GET['page'] defined, use it as page number
if(isset($_GET['page']))
{
$pageNum = $_GET['page'];
}

// counting the offset
$offset = ($pageNum - 1) * $rowsPerPage;
//query database for all districts
   $qury = "SELECT ID,runbatchno,datecreated,HIQCAPNo,spekkitno,createdby,Flag,daterun,datereviewed
            FROM worksheets
			ORDER BY ID DESC
			LIMIT $offset, $rowsPerPage";
			
			$result = mysqli_query($db_conn, $qury) or die(mysql_error()); //for main display
			$result2 = mysqli_query($db_conn, $qury) or die(mysql_error()); //for calculating samples with results and those without

$no=mysqli_num_rows($result);



if ($no !=0)
{
// print the districts info in table
echo '<table border="0"   class="data-table">
            
<tr ><th> <small>Worksheet No</small></th><th> <small>Run Batch No</small>	</th><th> <small>Date Created</small></th><th> <small>Created By</small></th><th> <small>Samples</small></th><th> <small>HIQ CAP No</small></th><th> <small>Spek Kit No</small></th><th> <small>Date Run</small></th><th> <small>Date Reviewed</small></th><th> <small>Status</small></th><th> <small>Task</small></th></tr>';
	while(list($ID,$runbatchno,$datecreated,$HIQCAPNo,$spekkitno,$createdby,$Flag,$daterun,$datereviewed) = mysqli_fetch_array($result))
	{  
	
		//get number of sampels per  worksheet
		$numsamples=$Lab->GetSamplesPerworksheet($ID);
		
		
		if ($daterun !="")
		{
		$daterun=date("d-M-Y",strtotime($daterun));
		}
		if ($datereviewed !="")
		{
		$datereviewed=date("d-M-Y",strtotime($datereviewed));
		}
		else
		{
		$datereviewed="";
		}
		$datecreated=date("d-M-Y",strtotime($datecreated));
$creator=$Lab->GetUserFullnames($createdby);


if ($Flag == 0)
{
$displayall="<a href=\"worksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this batch'>View Details</a> | <a href=\"downloadworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a> | <a href=\"deleteworksheet.php" ."?ID=$ID" . "\" title='Click to Delete Worksheet' OnClick=\"return confirm('Are you sure you want to delete Worksheet $ID');\" >Delete Worksheet  </a> | <a href=\"updateresults.php" ."?ID=$ID" . "\" title='Click to Update Results Worksheet' > Update Results </a>";
$status=" <strong><small><font color='#0000FF'> In Process  </small></font></strong>";
}
else if ($Flag ==1)
{
$displayall=" <a href=\"completeworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this batch'>View Details</a> | <a href=\"downloadcompleteworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a> ";
$status=" <strong><font color='#339900'> Complete </font></strong>";
}
else if ($Flag ==2)
{



if ($_SESSION['accounttype'] != 6)
	{
	
$displayall=" <a href=\"flaggedworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this worksheet'>View Details</a> | <a href=\"downloadflaggedworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a>  ";


$status=" <strong><font color='#FF0000'> Under Review </font></strong>";
	}
	else
	{
	$displayall=" <a href=\"flaggedworksheetDetails.php" ."?ID=$ID" . "\" title='Click to view Samples in this worksheet'>View Details</a> | <a href=\"downloadflaggedworksheet.php" ."?ID=$ID" . "\" title='Click to Download Worksheet' target='_blank'>Print Worksheet </a> | <a href=\"reviewworksheet.php" ."?ID=$ID" . "\" title='Click to Review Worksheet'> Review Worksheet </a>  ";


	//$rev="  ";
$status=" <strong><font color='#FF0000'> Awaiting Review </font></strong>";


	}

	

}


	echo "<tr class='even'>
			<td >$ID</td>
			<td >$runbatchno </td>	
			<td >$datecreated</td>
			<td ><small>$creator </small></td>
			
			<td > $numsamples</td>
		
			<td >$HIQCAPNo</td>
			<td >$spekkitno</td>
				<td >$daterun</td>
			<td >$datereviewed</td>
			<td >$status</td>
			<td > $displayall
			</td>
			
			
			
	</tr>";
	}
	echo '</table>';
	?>
		<?php
	echo '<br>';
	$numrows=$Lab->Gettotalworksheets(); //get total no of batches

	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);

// print the link to access each page
$self = $_SERVER['PHP_SELF'];
$nav  = '';
for($page = 1; $page <= $maxPage; $page++)
{
   if ($page == $pageNum)
   {
      $nav .= " $page "; // no need to create a link to current page
   }
   else
   {
      $nav .= " <a href=\"$self?page=$page\">$page</a> ";
   }
}

// creating previous and next link
// plus the link to go straight to
// the first and last page

if ($pageNum > 1)
{
   $page  = $pageNum - 1;
   $prev  = " <a href=\"$self?page=$page\">[Prev]</a> ";

   $first = " <a href=\"$self?page=1\">[First Page]</a> ";
}
else
{
   $prev  = '&nbsp;'; // we're on page one, don't print previous link
   $first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
   $page = $pageNum + 1;
   $next = " <a href=\"$self?page=$page\">[Next]</a> ";

   $last = " <a href=\"$self?page=$maxPage\">[Last Page]</a> ";
}
else
{
   $next = '&nbsp;'; // we're on the last page, don't print next link
   $last = '&nbsp;'; // nor the last page link
}

// print the navigation link
echo '<center>'. ' Page ' .$first . $prev . $nav . $next . $last .'</center>';


}

else
{

?>
<table   >
  <tr>
    <td style="width:auto" ><div class="notice"><?php 
		
echo  '<strong>'.' <font color="#666600">'.'No Worksheets Created'.'</strong>'.' </font>';

?></div></th>
  </tr>
</table><?php

 }  
 }
  
	
	?>	</div>
		</div>
		
@stop