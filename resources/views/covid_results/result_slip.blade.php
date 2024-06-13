<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>@yield('meta-title', 'Lab Result')</title> 

    <style type="text/css">
      body{
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
        color: #333;
        background-color: #fff;

      }
    .print-container{
        width: 1000px;
        margin-left: 50px;
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
        padding-bottom: 10px;
    }

  
    .print-sect table td{
        padding: 4px;

    }

    .print-val{
        border-bottom: 1px dotted #a9a6a6;
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
    .stamp-date.st_28{
      margin-top: 30px;
      padding-left: 6px;
    }
    .date-released{
      font-size:11px;
      color:red;
      border-top:dotted 1px;
      font-weight: lighter;
    }

    #result{
      padding: 2px;
      padding-left: 20px;
      padding-right: 20px;
      font-size: 28px;
      font-weight: bold;
      transform: rotate(-20deg);
    -ms-transform: rotate(-20deg);  /*  for IE  */

    /*  for browsers supporting webkit (such as chrome, firefox, safari etc.). */
    -webkit-transform: rotate(-20deg);
    display: inline-block;
    }
    
    .positive{
      border: 7px solid #d43f3a;
      border-radius: 4px;
      color: #8B0000;
    }
    .negative{
      border: 7px solid #228B22;
      border-radius: 4px;
      color: #008000;
    }
    </style>

</head>

<body>
<?php 
$local_today = date('d M Y');
$local_today = strtoupper($local_today);
?> 
@foreach ($vldbresult AS $result_obj)
  @include('covid_results._result_slip')
@endforeach
</body>
<script type="text/javascript">
  window.print(); 
  //setTimeout(window.close, 0);
</script>

</html>