<!DOCTYPE html>
<html>
<head>
<style>
table {
  border-collapse: collapse;
  width: 25%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
@if($result_obj['ref_lab'] == 2906)
<body>
  <p style="font-size:15px; color:gray">Dear {{$result_obj['case_name']}},
  <br><br>
  Please find attached Covid 19 test results.
  <br><br>
  For any further questions please call us on +256 (0) 393 241 400  / +256 ( 0) 200 909 227 / +256 706 881 782 or email us on info@ancabiotech.com and we will be glad to be of assistance.
  <br><br>
  Thank you for choosing ANCA Biotech.
  <br><br>
  Kind Regards,
  <br><br>
  The Results Team<br>
  <hr></hr>
  <!-- insert ANCA image here -->
  <img src="{{ MyHTML::getImageData('images/anca.png') }}" class="stamp img-responsive" width="95" height="25"><br>
<b>ANCA Biotech Limited</b><br>
Plot 118 I Luthuli Avenue I Bugolobi<br>
P. O. Box 1718 I Kampala I Uganda<br>
Tel: +256.393.241400 | +256.200.909227 | +256.706.8811782<br>
abl@ancabiotech.com | www.ancabiotech.com | facebook.com/ancabiotech<br>
<img src="{{ MyHTML::getImageData('images/wear_mask.gif') }}" class="stamp img-responsive">


  </p>
</body>
@else
<body>
<p style="font-size:15px; color:gray">Dear {{$result_obj['case_name']}} <br> please find a copy of your Covid-19 result from {{$result_obj['results']['lab']}} herein attached.</p>
<p style="font-family:'Courier New';color:red;font-size:15px"><i>Please, note that replies to this message are routed to an unmonitored mailbox. <br>
If you have any questions or inquiries, please contact CPHL/MOH toll free 0800-221-100
</i></p>
<br>
</body>
@endif
</html>

