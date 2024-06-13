"use strict";
/*jshint quotmark: false */
/* global $, DBS, alert, prompt, confirm, console, moment, APPROVAL_MODULE */

$(function  () {

    var samples = APPROVAL_MODULE.dbs; 
    var first_sample = APPROVAL_MODULE.selectFirstSample();
    var sample = samples[ first_sample ];
    var sample_number = first_sample;
    
    var age_popup;
    var date_popup;    
    const AGE_IS_MISSING = 78; // 78 == 'Age is Missing' (in appendices table)

    showSample( first_sample );

    $("#vnav").change(function(){
        showPrevSample( $("#vnav").val() );
    });

    $("#prev").click(function(){        
        showNextSample();
    });

    $("#next").click(function(){

        var lastSample = samples.length - 1;
        
        if(sample_number < lastSample){

            sample_number++;
            showSample( sample_number );
            return;
        }

        alert("No more samples in this batch"); // [" + sample_number + " : " + lastSample +"]");            
    });



    $("#sample_rejected").change(function(){

        setMemoryVal("#sample_rejected", this.value, sample.sample_id);
        
        if(this.value === "YES"||this.value === "REJECTED_FOR_EID"){

            $("#rejection_label").show();
            $("#rejection_reason").show();

        }else{

            $("#rejection_reason").hide();
            $("#rejection_label").hide();
            $("#reason_other").hide();            
        }
        
        console.log("all_samples_rejected = " + all_samples_rejected());

    });



    $("#infant_name").on('change', function () {
        alert('infant name');
    });


    $( window ).load(function() {
        monitor_batch_data();
        fetch_original_data();
    });


    $(".sRadio").click(function (){
        
        log_batch_header();
        
        var id = $(this).attr("id");

        $(".sRadio").fadeTo(30, 1);

        $(this).fadeTo(30, 1);
        $("label[for=" + id + "]").fadeTo(30, 1);
    });

    $("#unlock").click(function(){
        confirm("WARNING!!!\n\nYou are about to make changes to the data.");
    });
    $("#edit_batch").click(function(){
        confirm("Go to the batch page?\n\nYour current work will be lost.");
    });

    $("#region").click(function(){
        var edit_region = confirm("You are about to modify facility & region data\n\n" +
                                    "THIS AFFECTS ALL SAMPLES IN THE BATCH!!!" +
                                        "\n\nIs that what you want to do?");
        if(edit_region === true){
            // step 1: if No of spots and verification status has been set, save this data
            // step 2: redirect to the batch page.
        }
    });


    $("#save_dbs").click(function (){
        console.log("ajax go");
        ajax_go();
    });


    $('#requested_tests').on('change', function() {
        console.log("#requested_tests changed");
        update_eid_icon();
        update_scd_icon();
    });



    $('#rejection_reason').on('change', function() {

        var other = 33;
        var rejection_reason = this.value;
        var default_reason = $("#reason_other").val();
        
        if( rejection_reason == other ){

            var rr = prompt("Why did you reject this sample?", default_reason);

            $("#reason_other").val(rr || default_reason);
            $("#reason_other").show();

        }else{

            $("#reason_other").hide();
        }
    }); 




    $("#batch_selector").on("change", function () {
        location.href = "/approve/" + this.value;
    });

    $("#dbs_selector").on("change", function () {
        location.href = "/approve/" + this.value;
    });


    $("#batch_selector").select2();
    $("#dbs_selector").select2();



    
    $('.date_link').on('click', function(e) {

        e.preventDefault();
        date_popup = $('#date_pop_up').bPopup();
    });


    $('#save_dates').on('click', function () {
        
        setVal("#date_dbs_taken", $("#new_date_dbs_taken").val(), sample.sample_id);
        setVal("#date_dispatched_from_facility", $("#new_date_dispatched_from_facility").val());
        setVal("#date_rcvd_by_cphl", $("#new_date_rcvd_by_cphl").val());

        date_popup.close();

        var date_data = {};
            date_data.batch_id = sample.batch_id;
            date_data.date_dispatched_from_facility = getVal("#date_dispatched_from_facility");
            date_data.date_rcvd_by_cphl = getVal("#date_rcvd_by_cphl");

        console.log(JSON.stringify(date_data, null, 4));

        console.log("here is the same date_data (above) as a string:");
        console.log( JSON.stringify(date_data,null, 4) );

        $.get(  "/bDate",
                date_data, 
                function(d){ 
                    console.log(d);
                },
                "json" // dataType of AJAX reply
        );

        return false;
    });

    $("#rscs").on("click", function (){
        $(this).notify({
          title: '<center>Code:<br><b style="font-size:larger">'+ $("#rc").text() +'</b></CENTER>',
          button: 'CONFIRM'
        }, { 
          style: 'foo',
          autoHide: false,
          clickToHide: false,
          position: 'bottom center',
          className: 'info'

        });
    });

    //add a new style 'foo'
    $.notify.addStyle('foo', {
      html: 
        "<div>" +
          "<div class='clearfix'>" +
            "<div class='title' data-notify-html='title'/>" +
            "<div class='buttons'>" +
              "<button class='no'>CANCEL</button>" +
              "<button class='yes' data-notify-text='button'></button>" +
            "</div>" +
          "</div>" +
        "</div>"
    });

    //listen for click events from this style
    $(document).on('click', '.notifyjs-foo-base .no', function() {
      $(this).trigger('notify-hide');
    });
    $(document).on('click', '.notifyjs-foo-base .yes', function() {
        var note = $(this);
        var release_code = $("#rc").text();


        $.getJSON("/rscs/"+release_code)

            .success(function (server_reply) {
                note.trigger('notify-hide');
                $("#xxy").notify(  "Success\n" + 
                                    server_reply.nSamplesReleased + " samples sent to lab", 
                                    {position: "bottom right", className: "success"});

                $("#undo_link").attr("rc", release_code);// save old code
                $("#rc").text(server_reply.newReleaseCode);// set new code
                $("#undo_link").show().fadeTo(7500, 1).fadeOut(1250);

            })
            
            .error(function  () {
                note.trigger('notify-hide');
                $("#xxy").notify(  "Release Error\n" + 
                                    "Failed to send samples to the lab", 
                                    {position: "bottom right", className: "error"});
            });
    });
    $("#undo_link").on('click', function (){

        var prev_release_code = $(this).attr("rc");

        $.getJSON("/rscs_undo/" + prev_release_code)

            .success(function (server_reply) {
                // note.trigger('notify-hide');
                $("#xxy").notify(  "Success\n" + 
                                    server_reply.nSamplesReturned + " samples returned", 
                                    {position: "bottom right", className: "success"});

                $("#undo_link").attr("rc", "");// i.e. nothing to undo
                $("#rc").text(server_reply.newReleaseCode);// set new code
                $("#undo_link").hide();

            })
        
            .error(function  () {
                
                var errMsg;
                // note.trigger('notify-hide');

                if( prev_release_code === "") // this should never happen
                    errMsg = "Nothing To Undo\n" + "No samples had been released";
                else
                    errMsg = "Release Error\n" + "Failed to send samples to the lab";

                $("#xxy").notify(  errMsg, {position: "bottom right", className: "error"});
            });
    });
    

    $('body').on('focus', '[contenteditable]', function() {
        var $this = $(this);
        $this.data('before', $this.html());
        return $this;
    }).on('blur paste', '[contenteditable]', function() {// keyup  input
        var $this = $(this);
        if ($this.data('before') !== $this.html()) {
            var newName = $this.text();
            var newText = (sample_number+1) + " : " + newName + 
                            " (Exp ID: " + sample.infant_exp_id + ")";

            $("#vnav option:selected").text( newText );
        }
        return $this;
    });

    $("#no_age").on('click', function (){
        age_popup.close();
        reject_sample_with_missing_age();
        return false;
    });

    $("#save_age").on('click', function () {
        
        // body...
        // save infant_age and dob

        console.log("infant_age = " + $("#infant_age_").val().trim() );
        console.log("infant_dob = " + $("#infant_dob_").val().trim() );

        age_popup.close();            
        return false;
    });





/* --------event-handling ends here -------------------- */


    function monitor_batch_data () {

        var e, event_data;
        event_data = {
            operation : DBS.BATCH_STARTED,
        };

        e = new CustomEvent('dbs_data_changed', {'detail' : event_data });
        document.dispatchEvent(e);
    }

    function log_batch_header(){
        
        var e, event_data, dbs_form;
        log_batch_header.done = log_batch_header.done || false;

        if( log_batch_header.done ){
            return;
        }

        log_batch_header.done = true;
        dbs_form = APPROVAL_MODULE.dbs[0];

        event_data = {
            batch_id        : String(dbs_form.batch_id),
            batch_number    : String(dbs_form.batch_number),
            date_dispatched_from_facility   : dbs_form.date_dispatched_from_facility,
            date_rcvd_by_cphl   : dbs_form.date_rcvd_by_cphl,
            operation : DBS.BATCH_HEADER_SAVED                            
        };

        e = new CustomEvent('dbs_data_changed', {'detail' : event_data });
        document.dispatchEvent(e);

    }

    function log_sample_saved (sample) {

        var e, event_data;
                
        event_data = {
            operation : DBS.SAMPLE_FINISHED,
            sample_id : String(sample.sample_id),
            infant_name : sample.infant_name,
            infant_exp_id : sample.infant_exp_id,
            date_dbs_taken : sample.date_dbs_taken,
            PCR_test_requested : sample.PCR_test_requested,
            SCD_test_requested : sample.SCD_test_requested
        };

        e = new CustomEvent('dbs_data_changed', {'detail' : event_data });
        document.dispatchEvent(e);
    }

    function save_delta () {

        var e, event_data;
                
        event_data = {
            operation : DBS.VERIFICATION_COMPLETED,
            batch_number    : getVal("#batch_number").trim(),
            date_dispatched_from_facility   : getVal("#date_dispatched_from_facility"),
            date_rcvd_by_cphl               : getVal("#date_rcvd_by_cphl")
        };

        e = new CustomEvent('dbs_data_changed', {'detail' : event_data });
        document.dispatchEvent(e);        
    }

    function format_date( id ){

        var the_date = $(id);
        var formatted_date;

        if( the_date.length === 0) return;
        if( the_date.text().trim() === "" ) return;


        formatted_date = moment(the_date.text(), "YYYY-MM-DD").format("Do MMM YYYY");
        the_date.text( formatted_date );
        return;
    }

    function format_all_dates(){

        format_date("#date_dbs_taken");
        format_date("#date_dispatched_from_facility");
        format_date("#date_rcvd_by_cphl");
    }


    function showNextSample(){
        if(sample_number > 0){

            sample_number--;
            showSample( sample_number );
            return;
        }

        alert("No more samples in this batch");// [" + sample_number + " : " + lastSample +"]");            
    }


    function showPrevSample ( sn ) {
        sample_number = parseInt(sn);
        showSample( sample_number );
    }


    function getTests(sample){

        var tests = [];

        if(sample.PCR_test_requested === "YES") { tests.push("PCR"); }
        if(sample.SCD_test_requested === "YES") { tests.push("SCD"); }

        return tests.join(" + ");
    }


    function showSample( i ){

        APPROVAL_MODULE.activeSample = i;
        sample = samples[i];

            setVal("#infant_name", sample.infant_name);
            setVal("#province_code", sample.province_code);
            setVal("#province_name", sample.province_name);
            setVal("#district", sample.district);

            if(sample.scd_high_burden=='YES'){
                $("#scd_high_burden").show();
            }else{
                $("#scd_high_burden").hide();
            }             
            setVal("#facility_name", sample.facility_name);
            setVal("#infant_exp_id", sample.infant_exp_id);
            setVal("#sample_id", sample.sample_id);
            setVal("#date_dbs_taken", sample.date_dbs_taken);
            setVal("#date_dispatched_from_facility", sample.date_dispatched_from_facility);
            setVal("#date_rcvd_by_cphl", sample.date_rcvd_by_cphl);

            setVal("#ready_for_SCD_test", sample.ready_for_SCD_test);
            setVal("#requested_tests", getTests(sample), null, "use_val_function" );
            $('#age').html(sample.infant_age)
            update_eid_icon();
            update_scd_icon();


        $("#new_date_dbs_taken").val(sample.date_dbs_taken);
        $("#new_date_dispatched_from_facility").val(sample.date_dispatched_from_facility);
        $("#new_date_rcvd_by_cphl").val(sample.date_rcvd_by_cphl);

        $("#sample_rejected").val(sample.sample_rejected);
        $("#rejection_reason").val(sample.rejection_reason_id);
        $("#reason_other").val(sample.rejection_comments);

        if(sample.sample_rejected === "YES"){

            $("#rejection_label").show();
            $("#rejection_reason").show();
            

            if(sample.rejection_reason_id == 33){// 33 == 'Other' (according to the appendices table)
                
                $("#reason_other").show();
                
            }else{
                $("#reason_other").hide();
            }
        
        }else{

            $("#rejection_label").hide();
            $("#rejection_reason").hide();
            $("#reason_other").hide();
        }

        format_all_dates();

        // update the nSpots radio button
        var spot_radio = $("input:radio[name=nSpots]");
        var len = spot_radio.length;

        for(var x = 0; x < len; x++){
            if(spot_radio[x].value == sample.nSpots){ 
                spot_radio[x].checked = true;
            }
        }

        // update the navigation select:
        $("#vnav").val( i );


        console.log("sample.sample_rejected=");
        console.log(sample.sample_rejected);

// cxxxx
        if( sample.infant_age === null && sample.PCR_test_requested == 'YES'){// request age...
            var sample_already_rejected = (sample.sample_rejected === 'YES');
            
            if( !sample_already_rejected ){// ...but only if necessary

                setVal("#kid_name", sample.infant_name);

                age_popup = $('#age_pop_up').bPopup({
                    modalClose: false,
                    opacity: 0.6,
                    positionStyle: 'fixed' // use 'fixed' or 'absolute'
                });
            }
        }
    }

    function setVal(field_id, newValue, current_sample_id, use_val){
        
        // update display
        if(use_val)
            $(field_id).val(newValue);
        else
            $(field_id).text(newValue);

        
        if(current_sample_id){// update RAM
            setMemoryVal(field_id, newValue, current_sample_id);
        }
    }

    function  setMemoryVal(field_id, newValue, current_sample_id) {

        console.log("setMemoryVal(" + field_id + " , " + newValue + ", " + current_sample_id + ")");
        
        var nSamples = samples.length;
        var this_sample = {};
        var field_name = "";

//cxxx
        for(var i = 0; i < nSamples; i++){
            this_sample = samples[i];
            if(this_sample.sample_id == current_sample_id){// we got the sample we want
                field_name = field_id.substring(1);
                this_sample[field_name] = newValue;
                // console.log("we got the sample we want: changed " + field_name + " to " + newValue);
                // console.log('checking global samples obj: samples['+ i +'][' + field_name+ '] = ' + samples[i][field_name]);
                break;// no need to check other samples
            }
        }
    }



    function getVal(id, use_val){
        if(use_val)
            return $(id).val();
        else
            return $(id).text();
    }

    function ajax_go(){

        var LEFT_BLANK = -1;
        var j = sample_number;
        var last_sample = samples.length - 1;

        var approval_data = {};

            approval_data.batch_number = getVal("#batch_number").trim();
            approval_data.infant_name = getVal("#infant_name").trim();
            approval_data.infant_exp_id = getVal("#infant_exp_id");
            approval_data.sample_rejected = $("#sample_rejected").val();
            approval_data.rejection_reason_id = $("#rejection_reason").val();
            approval_data.ready_for_SCD_test =  get_SC_readiness();
            approval_data.nSpots = $("input:radio[name=nSpots]:checked").val() || LEFT_BLANK;
            approval_data.sample_id = sample.sample_id;
            approval_data.reason_other = (approval_data.rejection_reason_id == "33") ? $("#reason_other").val() : "";
            approval_data.sample_verified_by = APPROVAL_MODULE.current_user;
            approval_data.date_dbs_taken = APPROVAL_MODULE.dbs_collection_date(sample);
            approval_data.all_rejected = all_samples_rejected() ? "YES" : "NO";
            approval_data.tests_requested = tests_requested_for_this_batch();


            approval_data.PCR_test_requested = doPCRtest() ? "YES" : "NO";
            approval_data.SCD_test_requested = doSCDtest() ? "YES" : "NO";

           // cxxx 
        if( $("#infant_age_").val().trim().length > 0 && 
            $("#infant_dob_").val().trim().length > 0 ){

                approval_data.infant_age = $("#infant_age_").val().trim();
                approval_data.infant_dob = $("#infant_dob_").val().trim();
        }

        if(approval_data.nSpots === LEFT_BLANK){
            alert("How many Spots?");
            return false;
        }

        if(approval_data.sample_rejected === "NOT_YET_CHECKED"){
            alert("Please confirm Verification Status:\n\nWas the sample accepted or not?");
            return false;
        }

        if(approval_data.sample_rejected === "YES" || approval_data.sample_rejected === 'REJECTED_FOR_EID'){

            if(approval_data.rejection_reason_id === ""){

                alert("What is the REASON for rejecting sample?");
                return false;
            }
        }

        if(approval_data.infant_name === ""){

            alert("Infant Name cant be empty");
            return false; 
        }

        log_sample_saved(approval_data);
        if(j == last_sample){
            save_delta();
        }

        console.log("sending this approval_data:");
        console.log(approval_data);

        console.log("here is the same approval_data as a string: ");
        console.log( JSON.stringify(approval_data,null, 4) );
        
        var url = "/dbsVerify/" +   approval_data.sample_id;
        
        $.get(  url,
                approval_data, 
                function(d){ 
                    console.log("AJAX reply:");
                    console.log(d);

                    samples[j].infant_name = approval_data.infant_name;
                    samples[j].infant_exp_id = approval_data.infant_exp_id;
                    samples[j].sample_rejected = approval_data.sample_rejected;
                    samples[j].rejection_reason_id = approval_data.rejection_reason_id;
                    samples[j].sample_id = approval_data.sample_id;
                    samples[j].nSpots = approval_data.nSpots;


                    // alert("Successfully saved the data");

                    var nextSample = 0;
                    var lastSample = samples.length - 1;

                    if(j === lastSample) {
                        location.href = "/batchQ";
                        return;
                    }
                    
                    if(j < lastSample){
            
                    // reset the display:
                        $(".sRadio").fadeTo(30, 1);
                        $("#sample_rejected").val("NOT_YET_CHECKED");
                        $("#rejection_reason").val("");
                        $("#infant_age_").val(""); 
                        $("#infant_dob_").val("");

                        $("#rejection_label").hide();
                        $("#rejection_reason").hide();            

                        var spot_radio = $("input:radio[name=nSpots]");
                        var len = spot_radio.length;

                        for(var x = 0; x < len; x++)
                            spot_radio[x].checked = false;

                    // show the next sample:
                        nextSample = j+1;

                        sample_number = nextSample;
                        showSample( nextSample );
                    }
                },
                "json" // dataType of AJAX reply
        );
    }

   
    function get_SC_readiness () {
        const READY_FOR_SICKLE_CELL_TEST = "YES";
        const NOT_READY = "NO";

        if( sample_accepted_for_sc_test() ){
            
            if(doPCRtest()) 
                return NOT_READY; // do PCR first
            else
                return READY_FOR_SICKLE_CELL_TEST;
        }

        return NOT_READY;
    }


    function sample_accepted_for_sc_test () {/* should this sample be allowed to do a sickle cell test? */

        if(! doSCDtest () ){
            return false; /* Test not requested */
        }

        if( $("#sample_rejected").val() == "YES"){

            if((getVal("#ready_for_SCD_test").trim() == "YES")){
                return true; /* Yes. EID was rejected but SC was accepted */
            }
            
            return false; /* No. Both SC and EID were rejected  */
        }

        return true; /* Yes. SC test was requested and sample was not rejected */
    }


    function doPCRtest () {                    
        var requested_tests = $("#requested_tests").val();

        if (requested_tests == "PCR" || requested_tests == "PCR + SCD") return true;
        else return false;
    }

    function doSCDtest () {
        var requested_tests = $("#requested_tests").val();

        if (requested_tests == "SCD" || requested_tests == "PCR + SCD") return true;
        else return false;
    }

    function update_eid_icon () {
        if(doPCRtest()) $("#eid_icon").show(); else $("#eid_icon").hide();    
    }
    function update_scd_icon () {
        if(doSCDtest()) $("#scd_icon").show(); else $("#scd_icon").hide();
    }

    function reject_sample_with_missing_age(sample_position){//cxxx

        const x = sample_position || sample_number;
        const this_sample = samples[x];
        const sample_id = this_sample.sample_id;
        const THIS_IS_A_FORM_FIELD = true;

        const do_sc_test = (this_sample.SCD_test_requested === "YES");
        const do_eid_test = (this_sample.PCR_test_requested === "YES");

        const test_sickle_cell_only = (do_sc_test && !do_eid_test);
        const test_eid_only = (do_eid_test && !do_sc_test);
        const test_none = (!do_eid_test && !do_sc_test);
        const test_both = (do_eid_test && do_sc_test);

        if(test_none) {
            return; /* should never occur: at least one test will be requested */
        }

        if(test_both){
            setVal("#ready_for_SCD_test", 'YES', sample_id, !THIS_IS_A_FORM_FIELD);
            setVal("#rejection_reason", AGE_IS_MISSING, sample_id, THIS_IS_A_FORM_FIELD);
            setVal("#sample_rejected", 'YES', sample_id, THIS_IS_A_FORM_FIELD);

            $("#rejection_label").show();
            $("#rejection_reason").show();

            return;
        }

        if(test_eid_only){
            setVal("#sample_rejected", 'YES', sample_id, THIS_IS_A_FORM_FIELD);
            setVal("#rejection_reason", AGE_IS_MISSING, sample_id, THIS_IS_A_FORM_FIELD);
            setVal("#ready_for_SCD_test", 'NO', sample_id, !THIS_IS_A_FORM_FIELD);

            $("#rejection_label").show();
            $("#rejection_reason").show();

            return;
        }

        if(test_sickle_cell_only){
            setVal("#sample_rejected", 'NO', sample_id, THIS_IS_A_FORM_FIELD);
            setVal("#rejection_reason", AGE_IS_MISSING, sample_id, THIS_IS_A_FORM_FIELD);
            setVal("#ready_for_SCD_test", 'YES', sample_id, !THIS_IS_A_FORM_FIELD);

            $("#rejection_label").show();
            $("#rejection_reason").show();

            return;            
        }
    }

    function tests_requested_for_this_batch() {

        var nTests = {};
            nTests.eid = 0;
            nTests.scd = 0;

        samples.forEach(function (current_sample) {
            if(current_sample.PCR_test_requested == "YES") nTests.eid++;
            if(current_sample.SCD_test_requested == "YES") nTests.scd++;
        });


        if(nTests.eid > 0 && nTests.scd > 0){
            return "BOTH_PCR_AND_SCD";
        }

        if(nTests.eid > 0){
            return "PCR";
        }

        if(nTests.scd > 0){
            return "SCD";
        }

        return "UNKNOWN";
    }

    function all_samples_rejected() {

        var all_rejected = true;

         samples.forEach(function (current_sample) {
            if(current_sample.sample_rejected == "NO" || 
                current_sample.sample_rejected == "REJECTED_FOR_EID"|| 
                current_sample.sample_rejected == "NOT_YET_CHECKED"){
                all_rejected = false;
            }
        });
 
        return all_rejected;
    }


    function fetch_original_data () {
        var batch_id;

        if(DBS.IGNORE_EVENTS) return;

        batch_id = APPROVAL_MODULE.dbs[0].batch_id;
        
        $.get("/o_dbs_data/" + batch_id, function (data) {
            console.log("fetch_original_data() ... data = ");
            console.log(data);
            DBS.IGNORE_EVENTS = data.dbs_error ? true : false;
            APPROVAL_MODULE.original_dbs_data = data;
            console.log("APPROVAL_MODULE.original_dbs_data = ");
            console.log(APPROVAL_MODULE.original_dbs_data);
            
        });
    }
});