<?php
$sentinel_site = $result_obj->sentinel_site;
if(strtolower($sentinel_site) == 'Other'){
	$sentinel_site = $result_obj->sentinel_other;
}


$signature_img = MyHTML::getImageData('images/signatures/signature.14.gif');?>
<page size="A4">
	<div style="font-size: 13px;">
		<div class="print-header">
			<img src="{{ MyHTML::getImageData('images/uganda.emblem.gif') }}">
			<div class="print-header-moh">
				The republic of uganda<br>
				ministry of health uganda<br>
				Uganda Virus Research Insititute laboratories
				<br>
			</div>

		<br>
		
		COVID-19 TEST RESULTS<br>
		</div>
		
		<div class="row" style="margin-top: 10px;">
			<div style="width:100%;">
				<div class="print-sect" style = "border-top: 1px solid #a9a6a6; padding-top:5px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="25%" style="padding-bottom: 10px;"></td>
							<td width="25%" style="padding-bottom: 10px;"></td>
							<td align="right" width="25%" style="padding-bottom: 10px;"></td>
							<td width="25%"><span style="float: right;"><b>Result No.:</b> <span style="color: #F01319;padding-bottom: 10px;"><?=substr($result_obj->patient_id, 3) ?></span></span></td>
						</tr>
						<tr>
							<td align="right" style="padding-top: 10px;"><b>Test Method:</b></td>
							<td class="print-val" style="padding-top: 10px;">PCR</td>
							<td style="padding-bottom: 10px;"></td>
							<td style="padding-bottom: 10px;"></td>
						</tr>
						<tr>
							<td align="right" style="padding-top: 10px;"><b>District:</b></td>
							<td class="print-val" style="padding-top: 10px;"><?=$result_obj->district?></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td align="right" style="padding-top: 14px;"><b>Site of Collection:</b></td>
							<td class="print-val" style="padding-top: 14px;"><?=$sentinel_site?></td>
							<td align="right" style="padding-top: 14px;"><b>Collection Date:</b></td>
							<td  class="print-val" style="padding-top: 14px;"><?=MyHTML::localiseDate($result_obj->date_of_collection,'d M Y')?></td>
						</tr>

					</table>
				</div>
				
			</div>	

		</div>

		<div class="row" style="margin-top:10px;">
			<div style="width:100%;">
				<div class="print-sect" style = "border-top: 1px solid #a9a6a6;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td  width="25%" align="right" style="padding-top: 10px;"><b>CLient Name:</b></td>
							<td  width="25%" class="print-val" style="padding-top: 10px;"><?=$result_obj->case_name?></td>
							<td  width="25%" align="right" style="padding-top: 10px;"><b>Age of Client</b></td>
							<td  width="25%" class="print-val" style="padding-top: 10px;"><?=$result_obj->age_years?$result_obj->age_years.'Years':''?> </td>
						</tr>
						<tr>
							<td  width="25%" align="right" style="padding-top: 10px;"></td>
							<td  width="25%" style="padding-top: 10px;"></td>
							<td  width="25%" align="right" style="padding-top: 10px;"><b>Sex of Client:</b></td>
							<td  width="25%" class="print-val" style="padding-top: 10px;"><?=$result_obj->sex?></td>
						</tr>
					</table>
				</div>
				
			</div>	

		</div>

		<div class="row">
			<div style="width:100%;">
				<div class="print-sect" style = "border-top: 1px solid #a9a6a6; padding-top:5px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						
						<tr>
							<td width="25%" align="right"><b>Date of Result:</b></td>
							<td width="25%" class="print-val"><?=MyHTML::localiseDate($result_obj->test_date, 'd M Y')?></td>
							<td width="25%"></td>
							<td width="25%"></td>
						</tr>
						<tr>
							<td width="25%" align="right"><b>Result:</b></td>
							<td width="25%"class="print-val"><?=$result_obj->result?></td>
							<td width="25%"></td>
							<td width="25%"></td>
						</tr>					
						
					</table>
				</div>
				
			</div>	

		</div>
		
		<div class="row" style = "border-top: 1px solid #a9a6a6; margin-top:10px;">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
						
				<tr>
					<td width="50%" align="left" style="padding-left:20px;">
						<div style="margin-top:10px;">
				<img src="{{ MyHTML::getImageData('images/stamp.png') }}" class="stamp" >

				<span class="stamp-date"><?=strtoupper(MyHTML::localiseDate($result_obj->test_date, 'd M Y')) ?> <br><span class='date-released'></span></span>

			</div>

					</td>
					<td width="30%"></td>
					<td width="20%"><img src="{{ MyHTML::getImageData('images/signatures/signature.png') }}" height="50" width="100">
						<span><br>
							
							<b>Laboratory Technologist</b><br> 
							Uganda Research Institute
						</span>

					</td>
				</tr>			
				
			</table>
			
		</div>
    	
	</div>
	
</page>
<!-- </div> -->