<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('meta-title', 'RDS')</title>


    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery.dataTables.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery-ui.css')}}" rel="stylesheet" >

    <link href="{{ asset('/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/twitter-bootstrap-3.3/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/eid.css') }}" rel="stylesheet">
    <link rel="Shortcut Icon" href="{{ asset('/images/icon.png') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('/css/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{ asset('/css/select2.min.css') }}">


    <script src="{{ asset('/js/general.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-2.1.3.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('/js/jquery-ui.js')}}" type="text/javascript"></script>

    <script src="{{ asset('/js/plugins/bootstrap-select.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/twitter-bootstrap-3.3/js/bootstrap.min.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/js/plugins/bootstrap-form-buttonset.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/js/select2.full.min.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>

    <style type="text/css">
        .select2-hidden-accessible {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            height: 1px !important;
            margin: -1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
        }

    </style>
</head>
<body>
    <div class="container" style="padding-top: 1em;">
        @include('flash::message')

        @yield('content')
    </div>
    @yield('content2')

    {!! session("auth_msge") !!}

    <script>

    $(".w_create").click(function () {
        $('#the_modal').modal('show');
    });

    $(".auto_accession").click(function () {
        $('#accession_modal').modal('show');

    });


            $(".standard-datepicker-nofuture").datepicker({
                    dateFormat: "yy-mm-dd",
                    maxDate: 0
            });

            $(document).ready(function(){
                $('#who_being_tested').on('change', function () {
                    if(this.value === "Health Worker"){
                        $("#hwt").show();
                        $("#traveler").hide();
                        $("#eac_driver_id").hide();
                    } else if(this.value === "Traveler") {
                        $("#traveler").show();
                        $("#hwt").hide();
                        $("#eac_driver_id").hide();
                    }
                    else{
                        $("#traveler").hide();
                        $("#hwt").hide();
                        $("#eac_driver_id").hide();
                    }
                });
            });

            function ShowHideDiv() {

                    var chkHF = document.getElementById("hf"); //radio
                    var dvHF = document.getElementById("dvHF"); //text
                    var chkPOE = document.getElementById("poe"); //radio
                    var dvPoe = document.getElementById("dvPoe"); //text
                    var chkInst = document.getElementById("inst"); //radio
                    var dvInst = document.getElementById("dvInst"); //text
                    var chkOther = document.getElementById("other"); //radio
                    var dvOther = document.getElementById("dvOther"); //text

                    var chkUg = document.getElementById("ug"); //radio
                    var dvUg = document.getElementById("dvUg"); //text
                    var chkNoug = document.getElementById("noug"); //radio
                    var dvNoug = document.getElementById("dvNoug"); //text

                    dvHF.style.display = chkHF.checked ? "block" : "none"; //display hfs
                    dvPoe.style.display = chkPOE.checked ? "block" : "none"; //display boarder
                    dvInst.style.display = chkInst.checked ? "block" : "none"; //display Institutions
                    dvOther.style.display = chkOther.checked ? "block" : "none"; //display Other
                    dvUg.style.display = chkUg.checked ? "block" : "none"; //display Other
                    dvNoug.style.display = chkNoug.checked ? "block" : "none"; //display Other
                }

                $(document).ready(function(){
                    $('#travel_out_of_ug_b4_onset').on('change', function () {
                    if(this.value === "1"){
                        $("#onset").show();
                        } else {
                            $("#onset").hide();
                        }
                    });
                });


                /**
                *Convert Age to date and visa viz
                */
                $("#dob").change(function(){
                    set_age();
                });

                $("#age").change(function(){
                set_dob();

            });

                $("#id_age_units").change(function(){
                set_dob();
            });

                function round1(val){
                    return Math.round(val*10)/10;
    }

                function set_dob(){
                    var date_now = new Date();
                    var now_s = date_now.getTime();
                    var age = $("#age").val();
                    var units = $("#id_age_units").val();
                    var age_s=0;
                    if(units=='M'){

                        age = age/12;
                        age_s = age*365*24*3600*1000;
                    }else if (units=='D') {

                        age_s = age*24*3600*1000;

                    }else{

                        age_s = age*365*24*3600*1000;

                }

                    var dob_s = now_s-age_s;
                    var dob = new Date(dob_s);

                    //dob.setMonth(0, 1);

                    $("#dob").combodate('setValue', dob);

                }

                function set_age(){
                    var date_now = new Date();
                    var now_s = date_now.getTime();
                    var dob = new Date($("#dob").val());
                    var dob_s = dob.getTime();

                    var yrs = (now_s-dob_s)/(365*24*3600*1000) || 0;
                    var fraction_of_a_month_in_a_year=(30/365)||2;


                if(yrs<1 && yrs >= fraction_of_a_month_in_a_year){//Age in Months
                        var mths = yrs*12;
                        $("#age").val(round1(mths));
                        $("#id_age_units").val("M");
                    }else if(yrs<fraction_of_a_month_in_a_year){//Age in Days

                        $("#id_age_units").val("D");
                    }else{//Age in Years
                        $("#age").val(round1(yrs));
                    $("#id_age_units").val("Y");
                }
        }
    $("#lab_number, #symp, #knownUnderlying, #specimen_type, #patient_district, #poeWhere_sample_collected_from,#facilityWhere_sample_collected_from, #poe_field, #swabing_district, #q_swabing_district, .select-field").select2();

      //Auto fills text boxes on Mini LIF
      $("#epidNo").on('blur',function(){
        $.ajax({
          url: "/barcodes/" + $(this).val(),
          type: 'GET',
          success: function(data) {

            $('#patient_name').val(data[0].patient_surname);
            $('#passportNo').val(data[0].passportNo);
            $('#sex').val(data[0].sex);
            $('#who_being_tested').val(data[0].who_being_tested);
            $('#serial_number').val(data[0].serial_number);
            $('#age').val(data[0].age);
            $('#dob').val(data[0].dob);
            $('#id').val(data[0].patient_id);
            $('#patient_NOK').val(data[0].patient_NOK);
            $('#patient_contact').val(data[0].patient_contact);
            $('#gender').val(data[0].sex).change();
            $('#locator_id').val(data[0].ulin).change();
            $('#who_being_tested').val(data[0].who_being_tested).change();
            $('#specimen_collection_date').val(data[0].specimen_collection_date).change();
            $('#data_entry_date').val(data[0].updated_at).change();


            if(data[0].where_sample_collected_from = 'POE'){
              document.getElementById("poe").checked = true;
              document.getElementById('dvPoe').style.display = 'block';
              $('#poeWhere_sample_collected_from').val((data[0].nameWhere_sample_collected_from).toUpperCase()  ).change();
            }
            else if(data[0].where_sample_collected_from = 'HEALTH FACILITY'){
              document.getElementById("hf").checked = true;
              document.getElementById('dvHF').style.display = 'block';
              $('#facilityWhere_sample_collected_from').val(data[0].nameWhere_sample_collected_from).change();
            }
            else if(data[0].where_sample_collected_from = 'QURANTINE'){
              document.getElementById("inst").checked = true;
              document.getElementById('dvInst').style.display = 'block';
              $('#nameWhere_sample_collected_from').val((data[0].nameWhere_sample_collected_from).toUpperCase() ).change();
            }
            else{
              document.getElementById("other").checked = true;
              document.getElementById('dvOther').style.display = 'block';
              $('#nameWhere_sample_collected_from').val((data[0].nameWhere_sample_collected_from).toUpperCase() ).change();
            }
            if(data[0].nationality != 'UG'){
              document.getElementById("noug").checked = true;
              document.getElementById('dvNoug').style.display = 'block';
            }
            if(data[0].eac_driver_id != ''){
              document.getElementById('eac_driver_id').style.display = 'block';
              $('#eac_driver_id').val(data[0].eac_driver_id);
            }
            if(data[0].ulin !='')
            {
            alert('WARNING ! \n'+'This Entry was assigned LOCATOR ID: '+data[0].specimen_ulin+ '\n\n ENTER NEXT FORM');
            }
            else{
              document.getElementById("ug").checked = true;
              document.getElementById('dvUg').style.display = 'block';
            }
            console.log(data);
          }
        });
      });

    </script>
</body>

</html>
