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


@section('worksheet_details')
<?php
	$labss=1;// $_SESSION['lab'];

	$wno= empty($_GET['ID']) ? "" : $_GET['ID'];
	$worksheet = $Lab->getWorksheetDetails($wno);

	extract($worksheet);			

	$datecreated=date("d-M-Y",strtotime($datecreated));
	if ( !empty($kitexpirydate) ){
		$kitexpirydate=date("d-M-Y",strtotime($kitexpirydate));
	}
	if ( !empty($datecut) ){
		$datecut=date("d-M-Y",strtotime($datecut));
	}
	else{
		
		$datecut="";
	}

	if ( !empty($daterun) ){
		$daterun=date("d-M-Y",strtotime($daterun));
	}
	else{
		
		$daterun="";
	}

	$samplesPerRow = 3;
	$creator=$Lab->GetUserFullnames($createdby);
?>

<style type="text/css">
	select { width: 250;}
</style>	

<script language="javascript" src="calendar.js"></script>
<link type="text/css" href="calendar.css" rel="stylesheet" />	
<script language=JavaScript>
	function reload(form){
	
		var val=form.cat.options[form.cat.options.selectedIndex].value;
		self.location='addsample.php?catt=' + val ;
	}

	function submitPressed() {
		document.worksheetform.SaveWorksheet.disabled = true;
		//stuff goes here
		document.worksheetform.submit();
	}
</script> 
		<div  class="section">
		<div class="section-title">WORKSHEET NO <?php echo $wno; ?> DETAILS </div>
		<div class="xtop">
		<table><tr>
  <td>
  <A HREF="javascript:history.back(-1)"><img src="../img/back.gif" alt="Go Back"/></A>
  </td>
  </tr></table>
		<form  method="post" action="downloadworksheet.php?ID=<?php echo $wno; ?>" name="worksheetform" target="_blank" >
		<table border="0" class="data-table" cellspacing="4">
		<tr class="even" style='background: #dddddd;'>
		<td  class="comment style1 style4">
		Worksheet / Template No		</td>
		<td >
		  <span class="style5"><?php echo $ID; ?></span></td>
</tr>
			<tr class="even" style='background:#dddddd;'>
		<td class="comment style1 style4">
		Worklist Run Batch No		</td>
		<td class="comment">
		  <span class="style5"><?php echo $runbatchno; ?></span></td>
		<td class="comment style1 style4">
		Date Created		</td>
		<td class="comment" ><?php  echo  $datecreated ; //get current date ?></td>
		<td >HIQCAP Kit Lot No</th>
		  <td><?PHP ECHO $HIQCAPNo; ?></td>	
		</tr>
		
<tr class="even" style='background:#dddddd;'>
		<td class="comment style1 style4">
		Created By	    </td>
		<td>
	    <?php  echo $creator; ?>		</td><td  class="comment style1 style4">
	  	  Spex Kit No		</td>
		<td  colspan="">
		<?PHP ECHO $Spekkitno; ?></td>
		<td class="comment style1 style4">KIT EXP</td>
		<td><?PHP ECHO $kitexpirydate; ?></td>
  </tr>
			
<tr style='background:#dddddd;'>
<?php
	$qury = "SELECT ID,accessionno,patient,batchno,parentid,datereceived,envelopeno 
    	     		FROM samples
				WHERE worksheet='$wno' ORDER BY parentid DESC,ID ASC";			
	$result = mysqli_query($db_conn, $qury) or die(mysql_error());
?>
	<tr class="even"><td colspan="6">&nbsp;</td></tr>
<tr style='background:#dddddd;'>
<?php
	$count = 1;
	$colcount=1;
	for($i = 1; $i <= 2; $i++) {
		if ($count==1){

			$pc="<div align='right'>
		 	<table><tr><td class='comment style1 style4'><small>1</small></td></tr></table></div><div align='center'>Negative Control<br><strong>NC</strong></div>";
		}
		elseif ($count==2){
			$pc="<div align='right'><table></div><tr><td class='comment style1 style4'><small>2</small></td></tr></table><div align='center'>Positive Control<br><strong>PC</strong></div>";
		}
		
		$RE= $colcount%6;
?>
    <td height="50" bgcolor="#dddddd" class="comment style1 style4"> <?php echo $pc; ?> </td>
<?php	 
	
       	$count++;
		$colcount++;
}

	$scount = 2;
	while(list($ID,$accessionno,$patient,$batchno,$parentid,$datereceived,$envelopeno) = mysqli_fetch_array($result)){  
		$scount = $scount + 1;
		$paroid=$Lab->getParentID($accessionno,$labss);//get parent id
		
	if ($paroid =='0'){
		$paroid="";
	}
	else{
		$paroid=" - ". $paroid;
	}
			
	$RE= $colcount%6;
		
?>
		 
	

  
     
     <td width='178px' bgcolor="#dddddd">
	 <div align="right">
	 <table><tr><td class="comment style1 style4"><small>
	 <?php echo $scount;?></small>
	 </td></tr></table></div>
	 
	 <div align="center">
		<font size="-4"> Envelope no:   <?php echo $envelopeno; ?></font><br>  
		<font size="-4"> Patient ID:   <?php echo $patient; ?></font><br>  
		<font size="-4"> Batch no:   <?php echo $batchno; ?></font> <br> 
		<font size="-4"> Lab no: <?php echo $accessionno; ?><?php echo '<strong>'.$paroid .'</strong>' ;?></font>  <br>
<?php 
		// echo" <img src='../html/image.php?code=code128&o=2&dpi=50&t=50&r=1&rot=0&text=$accessionno&f1=Arial.ttf&f2=0&a1=&a2=B&a3='/>";
?>

<?php 
	echo "<img style='display:none;' src='/bc?code=code128&o=2&dpi=50&t=50&r=1&rot=0&text=$accessionno&f1=Arial.ttf&f2=0&a1=&a2=B&a3='/>";
	echo "<img src='$accessionno.jpg'>";
?>
	</div></td>
<?php  
		
		$colcount ++;
		 
	
        if ($RE==0){ 
     		
     		echo "</tr>";
		 }//end if modulus is 0
	 
	 }//end while
?>
	 
	 <tr bgcolor="#999999">
            <td  colspan="7" bgcolor="#00526C" ><center>
			
			    <input type="submit" name="SaveWorksheet" value="Print Worksheet" class="button"  />
				
            </center></td>
          </tr>

</table>
	</form>

@stop