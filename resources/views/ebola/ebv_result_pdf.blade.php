<!DOCTYPE html>
<html>
<style>
#dr_resultsTable {
  font-family: Helvetica;
  border-collapse: collapse;
  width: 100%;
  page-break-after:auto;
}

#dr_resultsTable td, #dr_resultsTable th {
  border: 1px solid #7393B3;
  padding: 8px;
  font-size: 12px;
}

#dr_resultsTable tr:nth-child(even){
  background-color: #f2f2f2;
}

#dr_resultsTable tr:hover {
  background-color: #ddd;
}

#dr_resultsTable th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}
/* h1{
	padding: 2px;
	padding-left: 20px;
	padding-right: 20px;
	transform: rotate(-20deg);
	-ms-transform: rotate(-20deg);
	-webkit-transform: rotate(-20deg);
	display: inline-block;
} */
footer {
		position: fixed;
		bottom: -60px;
		left: 0px;
		right: 0px;
		height: 50px;
		font-size: 10px !important;
		color: 	#7393B3;
		text-align: left;
}
#dr_resultsTable thead { display: table-row-group; }
</style>
<main>
<body>

  <table id="dr_resultsTable">
    <thead>
      <td colspan="1"><img src="{{ App\Closet\MyHTML::getImageData('images/coat_of_arms.png') }}" width="110%" align="center"></td>

      
      <td colspan="8" align="center" width="100%">
        @if($data_arr[0]['ref_lab'] == 2888)
        <h3>UGANDA VIRUS RESEARCH INSTITUTE <br>Viral Hemorrhagic Fever Laboratory
			<br>Plot 51-59 Nakiwogo Rd. P.O.Box 49 Entebbe, Uganda<br>Tel: 0800284384 (VHFUG) Toll Free.</h3>
      @elseif($data_arr[0]['ref_lab'] ==3035)
      <h3>CENTRAL PUBLIC HEALTH LABORATORIES <br>MUBENDE MOBILE LABORATORY
			<br> Plot 1062-106 Butabika road Luzira, Kampala, Uganda<br>Tel: 0800100066 (CPHL) Toll Free.</h3>
      @elseif($data_arr[0]['ref_lab'] == 3036)
      <h3>CENTRAL EBOLA TESTING LABORATORY <br>
        Plot 2 Lourdel Road, Opposite Public Service
      <br> P.O.Box 16041, Wandegeya Kampala, Uganda<br>Tel: 0800100066 (CPHL Toll-Free Line).</h3>
@endif</td>
			<td colspan="1" align="center">
				<?php
				$url = 'rds.cphluganda.org/evd/validator/';
				$result_id = Crypt::encrypt($data_arr[0]['id']);
				?>
				<?php echo \DNS2D::getBarcodeHTML($url.$result_id, "QRCODE",2,2);?>
			</td>
    </thead>

    <thead>
      <td bgcolor="#FFFFFF" style="line-height:8px; text-align:center; " colspan=10><b>RESULTS MOLECULAR REPORT FORM</b></td>
    </thead>

    <thead>
			<td colspan="3"><b>Date of Case Report:</b><br><br>{{$data_arr[0]['date_of_case_report']}}</td>
			<td colspan="3"><b>MoH/UVRI LAB No.:</b><br><br> {{$data_arr[0]['lab_number']}}</td>
      <td colspan="2"><b>Form No.:</b><br><br>{{$data_arr[0]['form_serial_number']}}</td>
      <td colspan="2"><b>CASE ID:</b><br><br>{{$data_arr[0]['case_id']}}</td>
    </thead>

		<thead>
			<td bgcolor="#7393B3" style="line-height:8px; text-align:center; " colspan=10><b style="color:white">Patient Information</b></td>
		</thead>

    <thead>
      <td colspan="3" ><b> Firstname:</b> <br><br> {{$data_arr[0]['patient_firstname']}}</td>
      <td colspan="3" ><b> Surname:</b> <br><br> {{$data_arr[0]['patient_surname']}}</td>
      <td colspan="2" ><b> Age:</b><br><br>{{$data_arr[0]['age']}} {{$data_arr[0]['age_units']}} </td>
      <td colspan="2" ><b> Sex:</b><br><br>{{$data_arr[0]['sex']}} </td>
    </thead>

    <thead>
			<td colspan="1"> <b>Occupation:</b> <br></br>{{$data_arr[0]['patient_occupation']}}</td>
      <td colspan="5"> <b>Symptoms:</b><br><br> {{$data_arr[0]['symptoms']}}</td>
      <td colspan="2"> <b>Onset Date:</b> <br><br>{{$data_arr[0]['symptom_onset_date']}}</td>
			<td colspan="2"> <b>Patient Status:</b><br><br>{{$data_arr[0]['patient_status']}}</td>
    </thead>

    <thead>
      <td bgcolor="#7393B3" style="line-height:8px; text-align:center; " colspan=10><b style="color:white">Sample Information</b></td>
    </thead>

    <thead>
      <td colspan="1"><b>Sample Type:</b><br><br>{{$data_arr[0]['sample_type']}}</td>
      <td colspan="2"><b>Collection Date: </b><br><br>{{$data_arr[0]['sample_collection_date']}}</td>
			<td colspan="1"> <b>Date Received:</b><br><br>{{$data_arr[0]['sample_reception_date']}}</td>
      <td colspan="5"> <b>Sender / Health Facility:</b><br><br>{{$data_arr[0]['interviewer_facility']}}</td>
      <td colspan="1"> <b>District:</b><br><br>{{$data_arr[0]['interviewer_district']}}</td>
    </thead>

    <thead>
      <td bgcolor="#7393B3" style="line-height:8px; text-align:center; " colspan=10><b style="color:white">Test Result Details</b></td>
    </thead>

    <thead>
      <td colspan="2" align="center"><b>Result</b></td>
      <td colspan="2" align="center"><b>Organism</b></td>
      <td colspan="2" align="center"><b>Test Type</b></td>
      <td colspan="1" align="center"><b>CT Value</b></td>
      <td colspan="2" align="center"><b>Test Date</b></td>
      <td colspan="1" align="center"><b>Testing Lab</b></td>
    </thead>

		<tbody>
			<tr>
				<td colspan="2" align="center" >
				<?php echo strtolower($data_arr[0]['result']) == "positive" ? "<h2 style='color:red;'>" .$data_arr[0]['result']. "</h2>" : "<h2 style='color:black;'>" .$data_arr[0]['result']. "</h2>"; ?></td>
				<td colspan="2" align="center">{{$data_arr[0]['organism']}}</td>
				<td colspan="2" align="center">{{$data_arr[0]['test_type']}}</td>
				<td colspan="1" align="center">{{$data_arr[0]['ct_value']}}</td>
				<td colspan="2" align="center">{{$data_arr[0]['test_date']}}</td>
				<td colspan="1" align="center">{{$data_arr[0]['testing_lab']}}</td>
			</tr>
		</tbody>

    <td bgcolor="#FFFFFF" style="line-height:15px; text-align:center; color:red " colspan=10>Official Use</td>
    <tbody>

      <tr>
        <td colspan="1"><b>Tested By:</b> <br><br> {{$data_arr[0]['tested_by']}}</a></td>
        <td colspan="2"><b>Reviewed By:</b> <br><br>{{$data_arr[0]['reviewed_by']}}</a></td>
        <td colspan="2"><b>Authorized By:</b> <br><br> {{$data_arr[0]['results_approver']}}</a></td>
        <td colspan="2"><b>Uploaded By:</b> <br><br> {{$data_arr[0]['family_name']}} {{$data_arr[0]['other_name']}}</a></td>
				<td colspan="3"><b>Lab Team Lead Sign:</b> </td>
        <!-- <td colspan="1"> <b>Print Date:</b> <br><br><?php echo date('F j, Y, g:i a'); ?> </td> -->
      </tr>
      <tr>
				<tr></tr>
				<td colspan="1"><b>Test Date:</b> <br>{{$data_arr[0]['test_date']}}</td>
				<td colspan="2"><b>Review Date:</b> <br>{{$data_arr[0]['review_date']}}</td>
				<td colspan="2"><b>Authorization Date:</b> <br>{{$data_arr[0]['approval_date']}}</td>
				<td colspan="2"><b>Upload Date:</b> <br>{{$data_arr[0]['created_at']}}</td>
        @if($data_arr[0]['ref_lab'] == 2888)
        <td colspan="3"><br><img src="{{ App\Closet\MyHTML::getImageData('images/signatures/sign_958.png') }}" class="manager img-responsive" width="100"></td>
          @else
        <td colspan="3"><br><img src="{{ App\Closet\MyHTML::getImageData('images/signatures/lab_'.$data_arr[0]['ref_lab'].'.png') }}" class="manager img-responsive" width="100"></td>
        @endif
    </tbody>
  </table>
	<br><br><br><br><br><br>

	<tr></tr>
	<div style="position: absolute; bottom: 5px; right: 8px;">
		<img src="{{App\Closet\MyHTML::getImageData('images/lab_'.$data_arr[0]['ref_lab'].'.png') }}" class="stamp" style="transform: rotate(30deg);">
		<span class="stamp-date"><span class='date-released' style="font-size:11px;color:red; font-weight: lighter; position: absolute; top: 35%; left: 30%; transform: translate(-50%, -50%);" align="center"><?=$data_arr[0]['test_date']?><br><br>DATE RELEASED</span></span>
	</div>

	<div id="footer">

 </div>

</body>
<footer>
<small><i>Printed by:....{!!Auth::user()->username!!}...................Print Date:...<?php echo date("D / d / M / Y"); ?>................Printed <?php echo $download_counter; ?> time(s).....</i></samll>
</footer>
  </main>

</html>
