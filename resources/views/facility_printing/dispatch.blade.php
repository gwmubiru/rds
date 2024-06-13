<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EID Results</title>
      <style type="text/css">

    .print-container{
        width: 1000px;
        margin-left: 50px;
        min-height: 1400px;
    }

    .print-header{
        text-transform: uppercase;
        font-size: 16px;
        text-align: center;
    }

    .print-header-moh{
        font-weight: bolder;
    }

    .print-ttl{
        font-weight: bolder;
        text-transform: uppercase;
        /*background-color: #D8D8D8;*/
        padding: 5px;
        font-size: 14px;
        margin-top: 10px;
    }

    .print-sect, .print-sect2{
        font-size: 14px;
        padding: 5px;
        border: 1px solid #a9a6a6;
        min-height: 110px;
    }

    .print-sect2{
        height: 120px;
    }

    .print-sect table td{
        padding: 4px;

    }


    .print-val{
        text-decoration: underline;
    }

    .print-check,.print-uncheck{
        font-size: 20px;
    }


    page {
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
      padding: 5px;
    }

    page[size="A4"] {  
      width: 21cm;
      height: 29.7cm; 
    }
    page[size="A4"][layout="landscape"] {
      width: 29.7cm;
      height: 21cm;  
    }

    page[size="A5"] {  
      width: 14.85cm;
      height: 21cm; 
    }
    page[size="A5"][layout="landscape"] {
      width: 21cm;
      height: 14.85cm;  
    }

    .printmm-container{
        width: 20cm;
    }

    .stamp{
      position: relative;
    }

    .stamp-date{
     position: absolute;
     margin-top: 55px;
     margin-left: -145px;
     font-size: 14px;
     font-weight: bold;
     color: #F01319;
    }

    .date-released{
      font-size:11px;color:#000;
      border-top:dotted 1px;
      font-weight: lighter;
    }

    table{
      width: 100%;
    }

     body{
      font-family: times, sans-serif;
      font-size: 12px;
       margin: 0;
       padding: 0;
    }

    </style>
</head>
<body>
<?php
$header_url = "/images/header.$type.png";
$stamp_url = "/images/stamp.$type.png";
$page_setup = Request::get('page_setup');
$page_setup_str = $page_setup == '1'?"size=A4":"size=A5 layout=landscape";
?>

  @foreach($samples as $sample)
  <page {{ $page_setup_str }}>
      <img src="{{ \MyHTML::getImageData($header_url)}}">
      <table >
        <tr>
          <td>Infant Name:</td>
          <td><b>{{ $sample->infant_name }}</b></td>
          <td>Age: <b>{{ $sample->infant_age }}</b></td>
          <td>Sex: <b>{{ $sample->infant_gender }}</b></td>
          <td>District:</td>
          <td><b>{{ $sample->district }}</b></td>
        </tr>
        <tr>
          <td>Infant ID No:</td>
          <td><b>{{ $sample->infant_exp_id }}</b></td>
          <td>Health Center:</td>
          <td><b>{{ $sample->facility }}</b></td>
        </tr>
        <tr>
          <td>Accession No:</td>
          <td><b>{{ $sample->sid }}</b></td>
          <td>Batch Number:</td>
          <td><b>{{ $sample->batch_number }}</b></td>
        </tr>
        <tr>
          <td>Sample Collection Date: </td>
          <td><b>{{ \MyHTML::localiseDate($sample->date_dbs_taken, "d-M-Y") }}</b></td>
          <td>Receipt Date: </td>
          <td><b>{{ \MyHTML::localiseDate($sample->date_rcvd_by_cphl, "d-M-Y") }}</b></td>
          <td>Assay Date: </td>
          <td>
            <b>
              <?php
              if($type=='eid'){
                echo $sample->accepted_result!='SAMPLE_WAS_REJECTED'?\MyHTML::localiseDate($sample->date_results_entered, "d-M-Y"):"";
              }elseif($type=='scd'){
                echo $sample->SCD_test_result!='SAMPLE_WAS_REJECTED'?\MyHTML::localiseDate($sample->date_sc_tested, "d-M-Y"):"";
              }
              ?>
            </b>
          </td>
        </tr>
        <tr><td colspan="6"><hr style="border: 2px solid black"></td></tr>
        <tr>
          
          @if($type=='eid'||$type=='rejects')
          <td>Lab Test:<b style='font-size:10px;'>COBAS&nbsp;Ampliprep/<br>Taqman&nbsp;HIV-1&nbsp;Qualitative<br>&nbsp;Test&nbsp;Version&nbsp;2.0</b></td>
          <td>Result:<br>            
              @if($sample->accepted_result=='SAMPLE_WAS_REJECTED' || $sample->sample_rejected=='YES' || $sample->sample_rejected=='REJECTED_FOR_EID')
              <b>SAMPLE WAS REJECTED</b>, <br>Rejection Reason: {{ $sample->rejection_reason }}
              @else
              <b>{{ $sample->accepted_result }}</b>
              @endif            
          </td>
          @elseif ($type=='scd')
          <td>Lab Test: <br> <b>Sickle Cell Test</b> </td>
          <td>Result:<br>            
              @if($sample->SCD_test_result=='SAMPLE_WAS_REJECTED' || $sample->sample_rejected=='YES')
              <b>SAMPLE WAS REJECTED</b>, <br>Rejection Reason: {{ $sample->rejection_reason }}
              @else
              <b>{{ \MyHTML::SCDResult($sample->SCD_test_result) }}</b>
              @endif            
          </td>            
          @else 
           
          @endif

          <td rowspan="2"><img src="{{ \MyHTML::getImageData('/images/sewanyana.gif') }}" style="width:70px; height:auto"></td>
          <td rowspan="2">{!! QrCode::errorCorrection('H')->size("90")->generate("$type, $sample->sid "); !!}</td>
          <td rowspan="2" colspan="2">
            <img class="stamp" src="{{ \MyHTML::getImageData($stamp_url) }}">
            <span class="stamp-date"><?=strtoupper(\MyHTML::localiseDate($sample->qc_at,"d M Y"))?><br><span class='date-released'>DATE RELEASED</span></span>

          </td>
        </tr>
        @if($type=='eid'||$type=='scd')
        <tr>
          <td>Done by:</td>
          <td><b>{{ $sample->done_by }}</b></td>
        </tr>
        @endif
        <tr>
          <td colspan="6"> 
          @if($type=='eid')       
              <div style="float:left; width: 115px;"><b>HIV Medical Notes:</b></div>
              @if($sample->accepted_result === "NEGATIVE")

                  <div style="float:left; margin-left: 1em; margin-bottom: 0.3em;">
                    1) A negative result implies an HIV free status <u>at the time of testing.</u> <br>
                    2) Further exposure to HIV risks (for example through breastfeeding) may result in HIV infection.
                  </div>

                  <div style="clear:left;float:left;width: 115px;"><b>HIV Testing Protocol:</b></div>

                  <DIV style="float:left; margin-left: 1em;">
                    1) If this is the first test, this baby should be tested again 6 weeks after breastfeeding stops.<br>
                    2) All children should be re-tested with a rapid test at 18 months of age irrespective of earlier PCR results.
                  </DIV>
              @elseif($sample->accepted_result === "POSITIVE")

                  <div style="float:left; margin-left: 1em; margin-bottom: 0.3em;">
                        Action: Start treatment immediately
                  </div>

                  <div style="clear:left;float:left;width: 115px;"><b>HIV Testing Protocol:</b></div>

                  <DIV style="float:left; margin-left: 1em;">
                    1) Take off another sample on the day of initiation of treatment and send it.<br>
                    2) All children should be re-tested with a rapid test at 18 months of age irrespective of earlier PCR results.
                  </DIV>
              @elseif($sample->accepted_result === "INVALID")
                  <div style="float:left; margin-left: 1em; margin-bottom: 0.3em;">
                      Invalid result can be caused by loss of specimen integrity due to<br> 
                      contamination, Poor sample handling (poor drying, exposure to moisture and other adverse condition or
                      target below detection limits.
                  </div>

                  <div style="clear:left;float:left;width: 115px;"><b>HIV Testing Protocol:</b></div>

                  <div style="float:left; margin-left: 1em;">
                    1) Take off another sample on the day of initiation of treatment and send it.<br>
                    2) All children should be re-tested with a rapid test at 18 months of age irrespective of earlier PCR results.
                  </div>            
              @endif
          @elseif($type=='scd')
              <div style="clear:left; float: left; margin-top: 0.3em;">
               <b>Sickle Cell Medical Notes: </b> {{ \MyHTML::SCDExplanation($sample->SCD_test_result) }}</div>
          @else
          @endif

          </td>
        </tr>


      </table>
    
      <footer>
        <span style='float:right'>
          <span style='margin-right: 60px;'>print&nbsp;version&nbsp;{{ $print_v }}</span>
          1 of 1
        </span>
      </footer>
    
  </page>
  @endforeach
</body>
@if(!\Request::has('qc') and \Request::has("print"))
<script type="text/javascript">
  window.print(); 
  setTimeout(window.close, 0);
</script>
@endif
</html>