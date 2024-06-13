<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{ font-family:times, sans-serif;
        font-size:12px;
        padding:3px 10px;
        border-style:solid;
        border-width:1px;
        overflow:hidden;
        word-break:normal; 
        border:none
      }
.tdr{ text-align: right;}

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

page[size="A4"][layout="portrait"] {
  width: 29.7cm;
  height: 21cm;  
}

footer{
    float: right;
    font-size: 10px;
  }

.stamp{
  position: relative;
  left: 300px;
  padding-top: 30px;
}

.stamp-date{
  position: relative;
  top: -60px;
  left: 148px;
  font-size: 16px;
  font-weight: bold;
  color: #F01319;
}

</style>

<style type="text/css" media="print">
    @page 
    {
        size: auto;   /* auto is the current printer page size */
        /*margin: 0mm;   this affects the margin in the printer settings */
        margin: 1em;  /* the margin on the content before printing */

    }
    
    @media print {
      footer {page-break-after: always;}
    }

    body 
    {
        background-color:#FFFFFF; 
        /* border: solid 1px black ; */
        margin: 1em;  /* the margin on the content before printing */
   }
</style>

<?php
 $local_today = date('d M Y');
 $local_today = strtoupper($local_today);

$now=date("Y-m-d H:i:s");
$now_str=strtotime($now);
    function display_comment($data)
    {
        $comment = "";
        if($data->sample_rejected == "YES" || $data->sample_rejected == "REJECTED_FOR_EID"){
          $comment = $data->rejection_reason_str;
        }
        return $comment;
    }
  function getSCDResults($sample_id){
    $sql="SELECT tie_break_result,family_name,other_name,signature,ws.CreatedBy
          FROM sc_worksheet_index AS i
          LEFT JOIN sc_worksheets AS ws ON i.worksheet_number=ws.id
          LEFT JOIN users AS u ON ws.CreatedBy=u.id
          WHERE sample_id='$sample_id' AND tie_break_result!='SICKLER.TEST_AGAIN' AND tie_break_result!='INVALID'
           LIMIT 1";
    return \DB::select($sql);
  }
  function get_labTest_data($all_data)
  {
    $data = (object)["test_type"=>"","test_result"=>"","labtech_id"=>"","labtech_family_name"=>"","labtech_other_name"=>""];
    //$data=new stdClass;
    $test_requested = $all_data->test;
    if($test_requested == "PCR"){
        $data->test_type = "PCR";
        $data->test_result = $all_data->PCR_result;
        $data->labtech_id = $all_data->PCR_results_ReleasedBy;
        $data->labtech_family_name = $all_data->family_name;
        $data->labtech_other_name = $all_data->other_name;
    }
    elseif($test_requested == "SCD"){
        $data->test_type = "SCD";
        if(!empty($all_data->SCD_result)){
           $data->test_result = $all_data->SCD_result;
           $data->labtech_id = $all_data->SCD_results_ReleasedBy;
           $data->labtech_family_name = $all_data->family_name;
           $data->labtech_other_name = $all_data->other_name;
        }else{
          $scd_rslt=getSCDResults($all_data->axnNo);
          if(array_key_exists(0, $scd_rslt)){
            $posibo_rslt=$scd_rslt[0];
            $data->test_result = $posibo_rslt->tie_break_result;
            $data->labtech_id = $posibo_rslt->CreatedBy;
            $data->labtech_family_name = $posibo_rslt->family_name;
            $data->labtech_other_name = $posibo_rslt->other_name;
          }else{
            $data->test_result = "";
            $data->labtech_id = "";
            $data->labtech_family_name = "";
            $data->labtech_other_name = "";
          }
        } 
    }
    return $data;
  }
  $dbs = Request::get('dbs') ?: 0;
  $batch_id = Request::get('b') ?: 0;
  $print_one_sample = Request::get('xn') ?: 0;
  if($batch_id)
      $filter = "dbs_samples.batch_id = '$batch_id'";
  else
      $filter = "dbs_samples.id = $dbs";
  if( $print_one_sample){
      $sample_to_print = Request::get('xn');
      $filter = $filter . " AND dbs_samples.id = $sample_to_print";
  }
  $type = \Request::get('type');
  $filter .= $type=='EID'?" AND qc_done='YES'":" AND scd_qc_done='YES'"; 

  $REJECTION_REASONS = "6";
  $sql =   "SELECT  DISTINCT
  
                  dbs_samples.id as axnNo, 
  
                    infant_name, infant_gender,
    CASE
        WHEN infant_gender = 'MALE' THEN 'M' 
        WHEN infant_gender = 'FEMALE' THEN 'F'
        WHEN infant_gender = 'NOT_RECORDED' THEN 'Blank'
        ELSE ''
      END AS gender, 
                    infant_exp_id as expID,
                    date_dbs_taken as collected,
                    infant_age as age,
                    facility_id as HC,
                    batch_number,
                    users.id as user_id,
                    if(users.id = PCR_results_ReleasedBy, 'PCR', 'SCD') as test,
  
                    date_rcvd_by_cphl,
                    date_results_entered,
                    date_dbs_tested as assay_date,
                    accepted_result as PCR_result, 
                    SCD_test_result as SCD_result, 
                    facilities.facility as facility_name, 
                    districts.name as district_name ,
                    PCR_results_ReleasedBy,
                    SCD_results_ReleasedBy,
                    family_name,
                    other_name,
                    signature,
  
  
                    sample_rejected,
                    rejection_reason_id, 
                    appendix as rejection_reason_str,
                    PCR_test_requested,
                    SCD_test_requested
  
       
            FROM 
                  dbs_samples
                  LEFT JOIN  batches     ON  dbs_samples.batch_id = batches.id
                  LEFT JOIN  facilities  ON  batches.facility_id = facilities.id
                  LEFT JOIN  districts   ON  districts.id = facilities.districtID 
                  LEFT JOIN  users ON (users.id = PCR_results_ReleasedBy OR users.id = SCD_results_ReleasedBy) 
                  LEFT JOIN   appendices ON   (categoryID = 6 AND (rejection_reason_id = appendices.id))
              
  
            WHERE $filter 
    
            ORDER BY dbs_samples.id ASC";
      //
      //
      // add a where (via UI) to filter by districts, facilities, batches or individuals
      // UI could be a drop-down + textbox. 
      // drop-down has these options: districts, facilities, batches and individuals
      // If textbox is filled, use that as an ID of whatever dropbox has. 
      // If textbox's not filled, filter by the category
      //
      //
// dd($sql);
    $result = DB::select($sql);
// dd($result);
    $nResults = count($result);
?>

<!DOCTYPE html>
<html>
<head>
  <title>EID Results</title>
</head>
<body>

<?php
  $kids = [];
  foreach ($result as $rs) {
    $axnNo = $rs->axnNo;
    $test_type = $rs->test;
    //echo $rs->test."<br>";
    /* warning: 1 and 2, below, are magic numbers. Remove them. */
// dd( $result );
    if(!array_key_exists($axnNo, $kids)){
      $kids[$axnNo] = $rs;
      $kids[$axnNo]->tests_done = [];
      $testNo=1;
    }else{
      $testNo=2;
    }
    if($rs->PCR_test_requested=='YES'){
      $test_type=$rs->test='PCR';
      $kids[$axnNo]->tests_done[$test_type] = get_labTest_data($rs); 
      $kids[$axnNo]->tests_done[$test_type]->testNo = $testNo;
    }
    if($rs->SCD_test_requested=='YES'){
      $test_type=$rs->test='SCD';
      $kids[$axnNo]->tests_done[$test_type] = get_labTest_data($rs); 
      $kids[$axnNo]->tests_done[$test_type]->testNo = $testNo;
    }
    
  }
  
  // dd($kids);
  $i = -1; 
  $show_print_links = \Request::has('pp') ? false : true;
  $current_URL = \Request::fullUrl();
  $printed_PCR_results = [];
  $printed_SCD_results = [];
  function show_SCD_reviewer($results_data)
  {
      $sc_reviewer = "";
      $SCD = $results_data->tests_done["SCD"];      
      $sc_reviewer = strtoupper( $SCD->labtech_family_name ) . ", " . $SCD->labtech_other_name;
      if($SCD->labtech_id == env('SYS_REJECTOR', 1)){
        //
        // presence of SYS_REJECTOR means we have PCR results (by definition)
        // So hide the SYS_REJECTOR and print name of actual rejector
        //
        $PCR = $results_data->tests_done["PCR"];
        $sc_reviewer = strtoupper( $PCR->labtech_family_name ) . ", " . $PCR->labtech_other_name;
      }
      
      return $sc_reviewer;
  }
?>

  @if($show_print_links)
    <center >
      <a href="{{ $current_URL }}&pp=1" style="font-size: xx-large">PRINT ALL</a>
    </center>
  @endif

@foreach($kids as $r)
<?php 
        $i++;
        $PCR = null;
        $SCD = null;
        $show_PCR_results = false;
        $show_SCD_results = false;
        $PCR_results_requested = $type=='EID';
        $SCD_results_requested = $type=='SCD';
        if($PCR_results_requested && !empty($r->tests_done["PCR"])){
            $show_PCR_results = true;
            $PCR = $r->tests_done["PCR"];
            $printed_PCR_results[] = $r->axnNo;
        }
        if($SCD_results_requested && !empty($r->tests_done["SCD"])){
            $show_SCD_results = true;
            $SCD = $r->tests_done["SCD"];          
            $printed_SCD_results[] = $r->axnNo;
        }
  ?>


  @if( $show_PCR_results || $show_SCD_results )
<page size="A4">
   @if($show_print_links)
   <table>
    <thead>
      <td colspan="6">
          <a name="p{{ $i }}">
          <a href="{{ $current_URL }}&xn={{$r->axnNo}}&pp=1" >PRINT</a>
      </td>
    </thead>
  </table>
  @endif

   @if($show_PCR_results)  <img  src="{{ asset('/images/header.eid.png') }}" align="center"> @endif
   @if($show_SCD_results)  <img  src="{{ asset('/images/header.scd.png') }}" align="center"> @endif

 
<table class="tg" align="center" border="0">
  
  <tr>
    <td class="tg-031e" >Infant Name: </td>
    <td class="tg-031e" ><b>{{ ucwords(strtolower($r->infant_name)) }}</b></td>
    <td class="tg-031e" >Age: {{ $r->age }}</td>
    <td class="tg-031e" >Sex: <b>{{ $r->gender }}</b></td>
    <td class="tg-031e" >District:</td>
    <td class="tg-031e" ><b>{{ $r->district_name }}</b></td>
  </tr>


  <tr>
    <td class="tg-031e" >Infant ID No: </td>
    <td class="tg-031e" ><b>{{ $r->expID }}</b></td>
    <td class="tg-031e" >Health Center:</td>
    <td class="tg-031e" colspan="3"><b>{{ $r->facility_name }}</b></td>

  </tr>

  <tr>

    <td class="tg-031e" >EID Accession No: </td>
    <td class="tg-031e" ><b>{{ $r->axnNo }}</b></td>
    <td class="tg-031e" >Batch Number:</td>
    <td class="tg-031e" colspan="3"><b>{{ $r->batch_number }}</b></td>

  </tr>

  <tr>

    <td class="tg-031e" >Sample Collection Date: </td>
    <td class="tg-031e" ><b>{{ date_format(date_create($r->collected), "d-M-Y") }}</b></td>

    <td class="tg-031e" >Receipt Date:</td>
    <td class="tg-031e" ><b>{{ date_format(date_create($r->date_rcvd_by_cphl), "d-M-Y") }}</b></td>

    @if( $r->sample_rejected != 'YES')
    <td class="tg-031e" >Assay Date:</td>
    <td class="tg-031e" ><b>{{ date_format(date_create($r->assay_date ?: $r->date_results_entered ), "d-M-Y")  }}</b></td>
    @endif

  </tr>
  <tr>
    <td colspan="6"><hr style="border: 2px solid black"></td>
  </tr>

  @endif

  @if( $show_PCR_results )

    @if( $r->sample_rejected == 'YES' || $r->sample_rejected == 'REJECTED_FOR_EID')
      <tr>
        <td class="tg-031e">Lab Test # {{ $PCR->testNo }}: <br><b>HIV1-DNA-PCR</b></td>
        <td class="tg-031e">&nbsp;<br>Result:<br>
          <b>SAMPLE WAS REJECTED</b>
          <br>Rejection Reason: {{ display_comment($r) }}
        </td>
        <td class="tg-031e" >
            {!! QrCode::errorCorrection('H')->size("90")->generate("EID,".$r->axnNo.",SAMPLE WAS REJECTED,$now_str"); !!}
        </td>

      </tr>

    @else

      <tr>
        <td class="tg-031e">Lab Test # {{ $PCR->testNo }}: <br><b>HIV1-DNA-PCR</b></td>
        <td class="tg-031e">Result:<br><b>{{ $PCR->test_result }}</b></td>
        <td class="tg-031e"   rowspan="2">
            <img src="images/sewanyana.gif" style="width:70px; height:auto">
        </td>

         <td class="tg-031e"  colspan="2" rowspan="2">
            {!! QrCode::errorCorrection('H')->size("90")->generate("EID,".$r->axnNo.",$PCR->test_result,$now_str"); !!}
        </td>
      </tr>

        <tr>
          <td class="tg-031e"  >Done By:</td>
          <td class="tg-031e">

              {{ strtoupper($PCR->labtech_family_name) }}, 
              {{ $PCR->labtech_other_name }}
          </td>
        </tr>

    @endif      
  @endif

  @if($show_SCD_results)

  @if( $r->sample_rejected == 'YES')

    <tr>
      <td class="tg-031e">Lab Test # {{ $SCD->testNo }}:<br><b>Sickle Cell Test</b></td>
      <td class="tg-031e">
        Result:<br>
        <b>SAMPLE WAS REJECTED</b>
        <br>Rejection Reason: {{ display_comment($r) }}
      </td>
       <td class="tg-031e" >
            {!! QrCode::errorCorrection('H')->size("90")->generate("SCD,".$r->axnNo.",SAMPLE WAS REJECTED,$now_str"); !!}
        </td>
    </tr>
    
    @else

    <tr>
      <td class="tg-031e">Lab Test # {{ $SCD->testNo }}:<br><b>Sickle Cell Test</b></td>
      <td class="tg-031e">Result:<br><b>{{ $scd_res=$SCD->test_result == 'FAILED' ? 'INVALID' :  $SCD->test_result }}</b></td>
      <td class="tg-031e"   rowspan="2">
          <img src="images/sewanyana.gif" style="width:70px; height:auto">
      </td>
      <td class="tg-031e"  colspan="2" rowspan="2">
         {!! QrCode::errorCorrection('H')->size("90")->generate("SCD,".$r->axnNo.",$scd_res,$now_str"); !!}
       </td>
    </tr>

        <tr>
          <td class="tg-031e">Reviewed By:</td>
          <td class="tg-031e">{{ show_SCD_reviewer($r) }}</td>
        </tr>

    @endif
  @endif


  <tr>
    <td class="tg-031e"></td>
    <td class="tg-031e"></td>
    <td class="tg-031e"></td>
    <td class="tg-031e"></td>
    <td class="tg-031e"></td>
  </tr>
  <tr>
    <td colspan="6">

      @if($show_PCR_results)
          
          <div style="float:left; width: 115px;"><b>HIV Medical Notes:</b></div>

          @if($PCR->test_result === "NEGATIVE")

            <div style="float:left; margin-left: 1em; margin-bottom: 0.3em;">
              1) A negative result implies an HIV free status <u>at the time of testing.</u> <br>
              2) Further exposure to HIV risks (for example through breastfeeding) may result in HIV infection.
            </div>

            <div style="clear:left;float:left;width: 115px;"><b>HIV Testing Protocol:</b></div>

            <DIV style="float:left; margin-left: 1em;">
              1) If this is the first test, this baby should be tested again 6 weeks after breastfeeding stops.<br>
              2) All children should be re-tested with a rapid test at 18 months of age irrespective of earlier PCR results.
            </DIV>
          @endif

          @if($PCR->test_result === "POSITIVE")

            <div style="float:left; margin-left: 1em; margin-bottom: 0.3em;">
                  Action: Start treatment immediately
            </div>

            <div style="clear:left;float:left;width: 115px;"><b>HIV Testing Protocol:</b></div>

            <DIV style="float:left; margin-left: 1em;">
              1) Take off another sample on the day of initiation of treatment and send it.<br>
              2) All children should be re-tested with a rapid test at 18 months of age irrespective of earlier PCR results.
            </DIV>
            
          @endif


          @if($PCR->test_result === "INVALID")


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

      @endif

      @if($show_SCD_results)
          

          @if($SCD->test_result === "NORMAL")
            <div style="clear:left; float: left; margin-top: 0.3em;">
                <b>Sickle Cell Medical Notes: </b>
                This child has normal haemoglobin and DOES NOT have sickle cell trait or sickle cell disease
            </div>
          @elseif($SCD->test_result === "VARIANT")
            <div style="clear:left; float: left; margin-top: 0.3em;">
              <b>Sickle Cell Medical Notes: </b>          
              Variant: This child is normal and does not have sickle cell trait or sickle cell disease.
            </div>
          @elseif($SCD->test_result === "CARRIER")
            <div style="clear:left; float: left; margin-top: 0.3em;">
                <b>Sickle Cell Medical Notes: </b>
                The child is a sickle cell carrier, but does not have sickle cell disease and so 
                will not suffer any complications of sickle cell disease
            </div>
          @elseif($SCD->test_result === "SICKLER")
            <div style="clear:left; float: left; margin-top: 0.3em;">
                <b>Sickle Cell Medical Notes: </b>
                This child has sickle cell disease. Sickle Cell Disease causes abnormally shaped red blood cells and 
                numerous health complications including infection and anaemia. The child should receive the full series of pneumalcoccal
                vaccinations promptly and also penicillin prophylaxis until the age of 5 years
            </div>
          @elseif($SCD->test_result === "FAILED" || $SCD->test_result === "INVALID")
            <div style="clear:left; float: left; margin-top: 0.3em;">
                <b>Sickle Cell Medical Notes: </b>
                Invalid test due to poor sample integrity. A new sample should be sent
            </div>
          @endif

      @endif


    </td>
  </tr>
</table>
@if( $show_PCR_results )<div> <img class="stamp" src="{{ asset('/images/stamp.eid.png') }}"> <span class="stamp-date">{{ $local_today }}</span> </div> @endif

@if( $show_SCD_results )<div> <img class="stamp" src="{{ asset('/images/stamp.scd.png') }}"> <span class="stamp-date">{{ $local_today }}</span> </div>@endif

@if( $show_PCR_results || $show_SCD_results )
<br><br>
<footer>1 of 1</footer>
@endif
</page>

@endforeach


</body>
</html>



<?php 
  $print_me = "";
  $i_am_a_batch = false;
  if(Request::has('pp')) {
    $print_me = " window.print(); setTimeout(window.close, 0);";
  }
  if(Request::has('b') && !$print_one_sample &&  $nResults > 0){
    $i_am_a_batch = true;
  }
  if( $print_me && $i_am_a_batch){// update DB: mark batch as printed
      $scd_sql = "";
      $pcr_sql = "";
      $scd_printed = count($printed_SCD_results) > 0 ? true : false;
      $pcr_printed = count($printed_PCR_results) > 0 ? true : false;
      if( $scd_printed ){
          $scd_results  = new stdClass;
          $scd_results->n = count($printed_SCD_results);
          $scd_results->batch_IDs = $printed_SCD_results;
          $scd_json = json_encode($scd_results);
         
          $sql = "UPDATE batches SET f_date_SCD_printed='$now',date_SCD_printed='$now' WHERE id = '$batch_id'";
        \DB::unprepared($sql); 
      }
      if( $pcr_printed ){
          $pcr_results = new stdClass;
          $pcr_results->n = count($printed_PCR_results);
          $pcr_results->batch_IDs = $printed_PCR_results;
          $pcr_json = json_encode($pcr_results);
          
            $sql2= "UPDATE batches SET f_date_PCR_printed='$now',date_PCR_printed='$now' WHERE id = '$batch_id'";
        \DB::unprepared($sql2); 
      }
      
  }
 //echo $sql;
?>  

<script type="text/javascript">
{{ $print_me }}
/*
Eron's issue #1 can be solved starting from here...
mysql> select id, infant_name , batch_id, SCD_results_ReleasedBy , SCD_test_result  from dbs_samples where SCD_results_ReleasedBy is null and SCD_test_result is not null and PCR_test_requested = 'YES';
Eron's issue #2 can be solved by the new data type above { returned by get_labTest_data() }
Eron's issue #4 can also be solved by new data type
*/
</script>