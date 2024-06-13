<style media="screen">
.qrcode-container {
        text-align: center;
        width: 128px;
        height: 128px;
        margin:  0 auto;
}
.stamp-container {
        text-align: right;
        width: 100%;
        height: 100%;
        margin-right: 150px;
        margin-top: -690px;
}
html,page{
        height:297mm;
        width:210mm;
}
</style>

<page size="A4">
        <div style="font-size: 21px; border: 1px solid #a9a6a6; padding-top:5px; padding-right:5px; padding-left:5px;">
                <div class="center">
                        <div class="print-header-moh" style="text-align:center">
                                <img src="{{ MyHTML::getImageData('images/ug_coat_of_arms.png') }}" alt="centered" width="20%">
                                <p>THE REPUBLIC OF UGANDA <br> MINISTRY OF HEALTH</p>
                                <td align="center" style="padding-right: 100px;"><b>EVD Test Certificate</b></td>
                        </div>

                        <tr>
                                <td align="left" style="padding-top: 10px;">Result Number:</td>
                                <td class="print-val" style="padding-top: 10px; padding-left: 5px;">
                                        <!-- <small style="color:red;">MoH-{{$data['lab_number']}}</small> -->
                                        <small style="color:red;">{{$data['lab_number']}}</small>
                                </tr>
                        </div>

                        <div style="width:100%;">
                                <div class="print-sect" style = "border-top: 1px solid #a9a6a6; padding-top:5px;">
                                </div>
                                <p style="text-align: center">This is to certify that <b>{{$data['patient_surname']}} {{$data['patient_firstname']}}</b>,{{$data['age']}} years, {{$data['sex']}}, tested <b>{{$data['result']}}</b> for Ebola on
                                        <b>{{date ("M-d-Y",strtotime($data['test_date'])) }}</b> following a laboratory test performed at<b> {{App\Closet\MyHTML::getRefLabName($data['ref_lab'])}},Uganda. </b><br>

                                        <i style="color:gray;"><b>This certificate is not a guarantee against future infections. Observe hygiene and always follow SOPS.</b></i><br><br>
                                        @if($days < 3 )<i style="color:green"><b>Certificate was issued after {{$days * 24}} hours</b></i> @elseif($days < 14 && $data['result'] = 'negative') <b style="color:green">Certificate still valid for {{$days}} days</b></i>  @elseif($days < 14 && $data['result'] = 'positive') <b style="color:red">POSITIVE RESULT but Certificate still valid for {{$days}} days</b></i> @else <b style="color:red">Certificate expired {{$days}} days ago</b></i> @endif

                                        <div class="qrcode-container">
                                                <div class="qrcode" id="qrcode">
                                                        <?php
                                                        $url = 'rds.cphluganda.org/evd/validator';
                                                        $result_id = Crypt::encrypt($data['id']);
							$result = $data['result'];

                                                        ?>
                                                        <?php echo \DNS2D::getBarcodeHTML($url.$result_id, "QRCODE",2,2);?>
                                                </div>
                                        </div>

                                        <div class="stamp-container">
                                                <div class="qrcode" id="qrcode">

                                                        @if($days < 14 && $result == 'Negative')
                                                        <img src="{{ MyHTML::getImageData('images/valid.png') }}" width="45%">
                                                        @elseif($days >= 14)
                                                        <img src="{{ MyHTML::getImageData('images/expired-stamp.png') }}" width="45%">
							@elseif($result == 'positive' && $days < 14)
							<img src="{{ MyHTML::getImageData('images/positive.png') }}" width="40%">

                                                        @endif
                                                </div>
                                        </div>
                                </div>
                                <i style="color:gray">Test certificate issued by Ministry Of Health for the Republic of Uganda</i>
                        </div>
                </page>

