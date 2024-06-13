<page size="A4">
	<div style="font-style: Times New Roman Georgia; font-size: 13px;">
		<div class="print-header">
			<img src="{{ MyHTML::getImageData('images/uganda.emblem.gif') }}">
			<div class="print-header-moh">
				The republic of uganda<br>
				ministry of health <br>
				<!-- Central Public Health Laboratories -->

				COVID-19 TEST RESULTS<br>
			</div>

		</div>
		<div class="print-sect" style = "border-top: 1px solid #a9a6a6; padding-top:5px;">
	<span style="float: left;"><b>Laboratory No:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?=$result_obj['specimen_ulin'] ?></span></span>
	<span style="float: right;"><b>Client number:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?=$result_obj['patient_id'] == '' ? $result_obj['case_id'] : $result_obj['patient_id']?></span></span>
</div><br>
<div class="print-sect" style = "padding-top:5px;">
	<span style="float: left;"><b>Name of client:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?= $result_obj['case_name']?></span></span>
	<span style="float: right;"><b>Sex of client:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?= ''.$result_obj['sex']?> </span></span>
	<span style="float: right;"><b>Age of client:</b> <span style="color: #184e7b;padding-right: 20px;"><?=$result_obj['age_years'].'Yrs'?> </span></span>
</div><br>

<div class="print-sect" style = "padding-top:5px;">
	<span style="float: left;"><b>District:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?=$result_obj['patient_district']?> </span></span>
	<span style="float: right;"><b>Sample Type:</b> <span style="color: #184e7b;padding-bottom: 10px;">NP SWAB</span></span>
</div><br>

<div class="print-sect" style = "padding-top:5px;">
	<span style="float: left;"><b>Place of sample collection:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?=$result_obj['sentinel_other'] == '' ? $result_obj['sentinel_site'] : $result_obj['sentinel_other'] ?></span></span>
	<span style="float: right;"><b>Date of sample collection:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?=$result_obj['results'][0]['collection_date']?> </span></span>
</div><br>
<div class="print-sect" style = "padding-top:5px;">
	<span style="float: left;"><b>Requested by:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?=$result_obj['interviewer_name'] ?></span></span>
	<span style="float: right;"><b>Requestor&#39;s Contact:</b> <span style="color: #184e7b;padding-left: 10px;"><?=$result_obj['interviewer_phone'] == '' ? 'N/A' : $result_obj['interviewer_phone']  ?> </span></span>
</div><br>
<div class="print-sect" style = "padding-top:5px;">
	<span style="float: left;"><b>Date of sample receipt:</b> <span style="color: #184e7b;padding-bottom: 10px;"><?=$result_obj['specimen_collection_date'] ?></span></span>
	<span style="float: right;"><b>Time of Receipt:</b> <span style="color: #184e7b;padding-left: 10px;"><?=$result_obj['specimen_collection_time']?> </span></span>
</div><br>
<div>
	<div class="print-sect" style = "border-top: 1px solid #a9a6a6; padding-top:5px;">

		<div class="row" style="margin-top: 10px;">

			<table width="100%" border="0" cellspacing="0" cellpadding="0">

				<tr>
					<td align="right" style="padding-top: 10px;"><b>Test Method:</b></td>
					<td class="print-val" style="padding-top: 10px; padding-left: 5px; width:100%">SARS-CoV-2 RT-PCR </td>
					<td style="padding-bottom: 10px;"></td>
					<td style="padding-bottom: 10px;"></td>
				</tr>

				<tr>
					<td width="15%" align="right" style="padding-top: 14px;"></td>
					<td width="35%" align="right" style="padding-top: 14px;"></td>
				</tr>

			</table>
		</div>
	</div>
</div>

<div class="row">
	<div style="width:100%;">
		<div class="print-sect" style = "padding-top:5px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">

				<tr>
					<td width="20%"></td>
					<td width="25%"><b>Date of Result</b></td>
					<td width="25%"><b>Result</b></td>

				</tr>
				@for($i = 0; $i < 3; $i++)
				<tr>
					<td class="print-val" width="20%"><b>Sample {{$i+1}}</b></td>
					<td class="print-val" width="25%"><?=$result_obj['results'][$i]['test_date']?></td>
					<td class="print-val" width="25%"><?=$result_obj['results'][$i]['result']?></td>
				</tr>
				@endfor

			</table>
		</div>
	</div>
</div>
<br>
<div class="row" style = "border-top: 8px solid #a9a6a6; margin-top:10px; margin-bottom:350px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">

		<tr>
			<td width="50%" align="left" style="padding-left:20px;">


				<div style="margin-top:10px;">
					<img src="{{MyHTML::getImageData('images/lab_'.$result_obj['ref_lab'].'.png') }}" class="stamp" >
					<span class="stamp-date"><?=$result_obj['test_date']?> <br><span class='date-released' style="font-size:11px;color:#000;

						font-weight: lighter;">DATE RELEASED</span></span>

				</div>

			</td>
			<td width="30%"></td>
			<td width="20%">
				<span><br>
					<img src="{{ MyHTML::getImageData('images/signatures/lab_'.$result_obj['ref_lab'].'.png') }}" class="manager" >
					<br>
					<b>Laboratory Team Leader</b><br>
					Central Public Health Laboratories
				</span>

			</td>

				<td width="10%">
                                                <span><br>
                                                        <div style="margin:0.25em;">
								<?php
								$url = 'rds.cphluganda.org/validator/';
								$result_id = Crypt::encrypt($result_obj['result_id']);
								?>
								<?php echo \DNS2D::getBarcodeHTML($url.$result_id, "QRCODE",2,2);?>
                                                        </div>
                                                </span>
                                        </td>

		</tr>

	</table>

</div>

	</div><!-- </div> -->
</page>
<small><i>Printed by:....{!!Auth::user()->username!!}...................Print Date:...<?php echo date("D / d / M / Y"); ?>................Printed <?php echo $result_obj['download_counter']; ?> time(s).....</i></samll>

