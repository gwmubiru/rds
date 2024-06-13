<page size="A4">
	<div style="font-size: 13px;">
		<div class="print-header">
			@if(!file_exists(base_path('public/images/logos/logo_'.$result_obj['ref_lab'].'.png')))
			<img src="{{ MyHTML::getImageData('images/logos/moh_logo.jpeg') }}" class="stamp" height="200" >
			@else
				@if($result_obj['ref_lab'] == 2898)
				<img src="{{ MyHTML::getImageData('images/logos/logo_'.$result_obj['ref_lab'].'.png') }}" class="stamp img-responsive" width="">
				@else
				<img src="{{ MyHTML::getImageData('images/logos/logo_'.$result_obj['ref_lab'].'.png') }}" class="stamp img-responsive" width="">
				@endif
			@endif
			<div class="print-header-moh">
				<!-- The republic of uganda<br> -->
				<!-- ministry of health uganda<br> -->
				@if($result_obj['ref_lab'] != 2898 && $result_obj['ref_lab'] != 2901 && $result_obj['ref_lab'] != 2904 && $result_obj['ref_lab'] != 2905 && $result_obj['ref_lab'] != 2906 && $result_obj['ref_lab'] != 2895 && $result_obj['ref_lab'] != 2919 && $result_obj['ref_lab'] != 2920 && $result_obj['ref_lab'] != 3014 && $result_obj['ref_lab'] != 2925)
				<br>
				{{ $result_obj['results'][0]['lab'] }}<br>
				@endif
				
			</div>
			<br>

			COVID-19 TEST RESULTS<br>
		</div>

		<div class="row" style="margin-top: 10px;">
			<div style="width:100%;">
				<div class="print-sect" style = "border-top: 1px solid #a9a6a6; padding-top:5px;">
					<span style="float: right;"><b>Result No.:</b> <span style="color: #F01319;padding-bottom: 10px;"><?=$result_obj['patient_id'] ?></span></span>
				</div>
				<div>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">

						<tr>
							<td align="right" style="padding-top: 10px;"><b>Test Method:</b></td>
							<td class="print-val" style="padding-top: 10px; padding-left: 5px;">
								<?=$result_obj['test_method']?></td>
								<td style="padding-bottom: 10px;"></td>
								<td style="padding-bottom: 10px;"></td>
							</tr>
							<tr>
								<td align="right" style="padding-top: 10px;"><b>District:</b></td>
								<td class="print-val" style="padding-top: 10px;" style="padding-top: 10px; padding-left: 5px;"> <?=$result_obj['district']?></td>
								<td align="right" style="padding-top: 10px;"><b>Type of Site of Collection</b></td>
								<td class="print-val" style="padding-top: 10px; padding-left: 5px;"><?=$result_obj['type_of_site']?></td>
							</tr>
							<tr>
								<td width="15%" align="right" style="padding-top: 14px;"><b>Site of Collection:</b></td>
								<td width="35%"class="print-val" style="padding-top: 14px; padding-left: 5px;"><?=$result_obj['sentinel_site']?></td>
								<td width="35%" align="right" style="padding-top: 14px;"><b>Date of Sample Collection:</b></td>
								<td  width="15%" class="print-val" style="padding-top: 14px; padding-left: 5px;"><?=$result_obj['results'][0]['collection_date']?></td>
							</tr>
							<tr>
								<td width="20%" align="right" style="padding-top: 14px;"><b>Purpose of Testing:</b></td>
								<td width="35%"class="print-val" style="padding-top: 14px; padding-left: 5px;"><b style="color:green; font-size:150%;"><?=$result_obj['who_is_being_tested']?><b></td>
								<td width="35%" align="right" style="padding-top: 14px;"><b>Receipt NUmber:</b></td>
								<td  width="15%" class="print-val" style="padding-top: 14px; padding-left: 5px;"><b style="font-family:Courier; color:Blue; font-size: 15px;"><?=$result_obj['receipt_number']?></b></td>
							</tr>
						</table>
					</div>
				</div>

			</div>
			<br>
			<div class="row" style="margin-top:10px;">
				<div style="width:100%;">
					<div class="print-sect" style = "border-top: 1px solid #a9a6a6;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td  width="15%" align="right" style="padding-top: 10px;"><b>Name:</b></td>
								<td  width="30%" class="print-val" style="padding-top: 10px;"><?=$result_obj['case_name']?></td>

								<td  width="15%" align="right" style="padding-top: 10px;"><b>Passport No:</b></td>
								<td  width="30%" class="print-val" style="padding-top: 10px;"><?=$result_obj['passport_number']?></td>

								<td  width="15%" align="right" style="padding-top: 10px;"><b>Age</b>:</td>
								<td  width="15%" class="print-val" style="padding-top: 10px;"><?=$result_obj['age_years']?> </td>

								<td  width="15%" align="right" style="padding-top: 10px;"><b>Sex:</b></td>
								<td  width="10%" class="print-val" style="padding-top: 10px;"><?=$result_obj['sex']?></td>
							</tr>
						</table>
					</div>
				</div>

			</div>
			<br><br>
			<div class="row">
				<div style="width:100%;">
					<div class="print-sect" style = "padding-top:5px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">

							<tr>
								<td width="20%"><b>Date of Sample Collection</b></td>
								<td width="15%"><b>Date of Result</b></td>
								<td width="10%"><b>CT Value</b></td>
								<td width="15%"><b>Testing Platform</b></td>
								<td width="15%"><b>Platform Range</b></td>
								<td width="45%"><b>Result</b></td>

							</tr>
							@for($i = 0; $i < 1; $i++)
							<tr>
								<td class="print-vald" width="15%"><?=$result_obj['results'][$i]['collection_date']?>
									@if($result_obj['results'][$i]['specimen_collection_time'] != '12:00:00 am')
								<br>
								<?=$result_obj['results'][0]['specimen_collection_time']?>
								@endif
								</td>
								<td class="print-vald" width="15%"><?=$result_obj['results'][$i]['test_date']?>
									@if($result_obj['results'][$i]['test_time'] != '12:00:00 am')
									<br><?=$result_obj['results'][$i]['test_time']?>
									@endif
								</td>
								<td class="print-vald" width="10%"><?=$result_obj['ct_value']?></td>
								<td class="print-vald" width="15%"><?=$result_obj['testing_platform']?></td>
								<td class="print-vald" width="15%"><?=$result_obj['platform_range']?></td>
								<td class="print-vald" width="45%"><span id="result" class="<?=strtolower($result_obj['results'][$i]['result'])?>"><?=strtoupper($result_obj['results'][$i]['result'])?></span></td>

							</tr>
							@endfor
						</table>
					</div>

				</div>

			</div>
			<br>
			<div class="row" style = "border-top: 8px solid #a9a6a6; margin-top:10px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">

					<tr>
						<td width="50%" align="left" style="padding-left:20px;">


							<div style="margin-top:4px;">
								<img src="{{ MyHTML::getImageData('images/lab_'.$result_obj['ref_lab'].'.png') }}" class="stamp" >
								<span class="stamp-date"><?=$result_obj['test_date']?> <br><span class='date-released' style="font-size:12px;color:#000;

									font-weight: lighter;">DATE RELEASED</span></span>

								</div>

							</td>
							<td width="30%"></td>
							<td width="20%">
								<span><br>
									<img src="{{ MyHTML::getImageData('images/signatures/lab_'.$result_obj['ref_lab'].'.png') }}" class="manager img-responsive" >
									<br>
									@if($result_obj['ref_lab'] != 36)
									<b>Lab Manager</b><br>
									{{ $result_obj['results'][0]['lab'] }}
									@endif
								</span>

							</td>

							<td width="5%">
								<span><br>
									<div style="margin:0.25em;">

									<?php
											$url = "https:"."/"."/rds.cphluganda.org/validator/";
										$result_id = Crypt::encrypt($result_obj['result_id']);
										?>
										<?php echo \DNS2D::getBarcodeHTML($url.$result_id, "QRCODE",2,2);?>
									</div>
								</span>
							</td>
						</tr>

					</table>
					<br>
					<small><i>Printed by:....{!!Auth::user()->username!!}...................Print Date:...<?php echo date("D / d / M / Y"); ?>................Printed <?php echo $result_obj['download_counter']; ?> time(s).....</i></samll>
					<p style="color:#337ab7; font-family:Courier New; font-size:15px">The Laboratory is Certified by Ministry of Health Uganda to test for COVID-19</p>
					</div>
				</div>
			</page>
