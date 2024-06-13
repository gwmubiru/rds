"use strict";
/* global $, DBS, console, moment, CryptoJS */
/* jshint quotmark: false */
/* jshint sub:true*/

$(function(){

    var ajax_sample_data;

    $("#xf").validate({
        submitHandler: ajax_saveBatch,
        invalidHandler: function() {
            window.alert('Please correct the errors shown in red');
        }
    });

    $("#batch_number").on("change", function (){
        verifyBatchNumber();
    });
    
    $("#envelope_number").on("change", function (){
        verifyBatchNumber();
    });


    // sample-related stuff
    var NONE = -1;
    var EMPTY_STRING = "";
    var unsaved_row = NONE;// row number of sample that was not saved because user tried to save the sample before creating batch


    function getOptionalFields (rowNumber) {
        
        var YES = 1;
        var ignore = {};
        var tests_requested = getRequestedTests(rowNumber) || {};
        var is_SickleCell_Form = ("PCR" in tests_requested) ? false : true;
        var cphl_wants_everything_optional = true;

        if(cphl_wants_everything_optional || is_SickleCell_Form){
            ignore["pcr_" + rowNumber] = YES;
            ignore["infant_is_breast_feeding_" + rowNumber] = YES;
            ignore["mother_antenatal_prophylaxis_" + rowNumber] = YES;
            ignore["mother_delivery_prophylaxis_" + rowNumber] = YES;
            ignore["mother_postnatal_prophylaxis_" + rowNumber] = YES;
            ignore["infant_prophylaxis_" + rowNumber] = YES;
            ignore["date_dbs_taken_" + rowNumber] = YES;
}
        

        ignore["infant_age_" + rowNumber] = YES;
        ignore["infant_exp_id_" + rowNumber] = YES;
        ignore["infant_gender_" + rowNumber] = YES;
        ignore["infant_dob_" + rowNumber] = YES;
        ignore["infant_contact_phone_" + rowNumber] = YES;        
        ignore["infant_entryPoint_" + rowNumber] = YES;
        ignore["PCR_test_requested_" + rowNumber] = YES;
        ignore["SCD_test_requested_" + rowNumber] = YES;
        ignore["non_routine_" + rowNumber] = YES;        

        console.log('fields To Ignore = everything :(  ... "sorry, orders from above" ...');

        return ignore;
    
    }

    function tryToSaveRow(rowNumber, ignore_errors) {        

        var data = dataEntryCompleted(rowNumber);


        if(data){

            saveDBS(data, DBScallback);
            return;
        }
        
        // no data
        if(ignore_errors){
            return;
        }
        else{
            // display errors:
            $("#row_"+rowNumber).addClass("highlight_row"); 
            $("#row_"+rowNumber).notify("Please first complete data-entry for this row", { position:"top center" });

        }
    }


    $("tr.parent_row").on("focusout", function(event) {

        var old_row;
        var new_row = movedToNewRow();

        if( new_row ){

            old_row = new_row.from;
            tryToSaveRow(old_row);
        }

        return;



        function movedToNewRow() {

            var from_row = event.target ? event.target.getAttribute("r") : "-1";
            var dest_row = event.relatedTarget ? event.relatedTarget.getAttribute("r") : "-2";

            if(from_row == dest_row) { return null; }/* stayed on same row */
            
            if(event.target.getAttribute("name") === "date_dbs_taken"){ 
            // prevent false alert when user clicks date picker:

                event.target.focus();
                return null; 
            }

            if(event.target.hasAttribute("type_of_test")){ 
            // prevent false alert which is triggered because our checkbox is hidden out of the row
                return null; 
            }


            var delta = {};
                delta.from = from_row;
                delta.to = dest_row;

            return delta;
        }
    });


    function save_sample( rowToSave ){


        if(rowToSave === NONE) return;

        tryToSaveRow( rowToSave );

    }


    function verifyBatchNumber(){
        verifyBatchNumber.timer_started = verifyBatchNumber.timer_started || false;


        var batch_number = $("#batch_number").val().trim();
        var envelope_number = $("#envelope_number").val().trim();
        var pre_envelope_number = $("#pre_envelope_number").val().trim();
        var timer_started = verifyBatchNumber.timer_started;
        var e, event_data;

        if( !timer_started ){

            verifyBatchNumber.timer_started = true;
            event_data = {
                operation : DBS.BATCH_STARTED,
            };

            e = new CustomEvent('dbs_data_changed', {'detail' : event_data });
            document.dispatchEvent(e);
        }


        if(!envelope_number) {
            $("#envelope_number").notify( "Please enter the envelope number", { position:"top center" });
            $("#submit_button").prop("disabled", true);
            return;
        }

        if(!batch_number) {
            $("#batch_number").notify( "Please enter the batch number", { position:"top center" });
            $("#submit_button").prop("disabled", true);
            return;
        }


        $("#submit_button").prop("disabled", false);

        var uniq = {};
            uniq.batch_number = batch_number;
            uniq.envelope_number = pre_envelope_number+""+envelope_number;


        $.get(  "/vBatchNo",
                uniq, 
                function(batchNo){ 

                    var err_msg;

                    if(batchNo.already_exists){
                    
                        err_msg = "Batch Number already exists. Please add an R to it (at the end)";
                        $("#batch_number").notify( err_msg, { position:"top center" });
                        $("#submit_button").prop("disabled", true);
                        return;
                    }


                    console.log("This batch_number is unique");
                    $("#submit_button").prop("disabled", false);                
                }, 
                "json"
        );
    }


    function ajax_saveBatch(){

    // step 0: initialize some variables:
        var batch_data = $("#batch_data").serializePanel("=");
        var ajax_batch_data = batch_data.o;

        console.log("Batch data:");
        console.log(ajax_batch_data);


    // step 1: use MySQL's standard date format


        var date_dispatched = moment(ajax_batch_data.date_dispatched_from_facility, 'Do MMM YYYY').format('YYYY-MM-DD');
        var date_received = moment(ajax_batch_data.date_rcvd_by_cphl, 'Do MMM YYYY').format('YYYY-MM-DD');
        var today = moment().format('YYYY-MM-DD');

        console.log("In ajax_saveBatch()...");
        console.log("date_dispatched = " + date_dispatched);
        console.log("date_received = " + date_received);



    // step 2: are there any changes to save?
        var old_checksum = ajax_batch_data.batch_checksum;// checksum of the last data we saved
        var new_checksum = calc_batch_checksum(ajax_batch_data);// calculate checksum of the current data
            
        if(old_checksum === new_checksum){  return; /* no need to save. data hasn't changed. stop here */ }


    // step 3: save data

        $("#batch_checksum").val( new_checksum);
        ajax_batch_data.batch_checksum = new_checksum;

        ajax_batch_data.date_dispatched_from_facility = date_dispatched;
        ajax_batch_data.date_rcvd_by_cphl = date_received;
        ajax_batch_data.date_entered_in_DB = today;

        ajax_batch_data.envelope_number=ajax_batch_data.pre_envelope_number+""+ajax_batch_data.envelope_number;


        console.log("sending this batch data (added checksum and dates):");
        console.log(ajax_batch_data);

        console.log("here is the same batch data as a string:");
        console.log( JSON.stringify(ajax_batch_data,null, 4) );

        $.get(  "/batch",// destination url 
                ajax_batch_data, 
                function  (d){ 
                    console.log(d);

                    $("#id").val(d.batch_id);
                    save_sample( unsaved_row );
                    unsaved_row = NONE;

                    $("#submit_button").notify( "Successfully saved the batch data", { position:"top center" , className: "success"});
                    enableRow(1);


                    ajax_saveBatch.done = ajax_saveBatch.done || false;

                    if( !ajax_saveBatch.done ){
                        var e, event_data;

                        ajax_saveBatch.done = true;
                        event_data = {
                            batch_id        : d.batch_id,
                            batch_number    : $("#batch_number").val().trim(),
                            date_dispatched_from_facility   : date_dispatched,
                            date_rcvd_by_cphl   : date_received,
                            operation : DBS.BATCH_HEADER_SAVED                            
                        };

                        e = new CustomEvent('dbs_data_changed', {'detail' : event_data });
                        document.dispatchEvent(e);
                    }
                }, 
                "json" // dataType of AJAX reply
        );
        return;


        
    // utility functions:
        function calc_batch_checksum(){// calculates batch's checksum [merge with other calc_]

            var data, data_str, current_checksum;
                
                data = ajax_batch_data;
                data.batch_checksum = "";

                data_str = JSON.stringify(data);
                current_checksum = CryptoJS.MD5( data_str ) + "";

            return current_checksum;
        }
    }

function saveDBS(sample_data, DBScallback){

console.log(sample_data);

    // step 1: use MySQL's standard date format
        var i = sample_data.rowNumber;
        var dbst = moment($("#date_dbs_taken_"+i).val(), 'Do MMM YYYY').format('YYYY-MM-DD');
        console.log('saveDBS: Moment\n\nIN = ' + $("#date_dbs_taken_"+i).val() + "\n" +
                       "OUT = " + dbst);
        

    // step 2: are there any changes to save?
        var old_checksum = getValue("checksum_");// checksum of the last data we saved
            setValue("date_dbs_taken_", dbst, false);// this doesn't affect checksum, unless date changes
            setValue("checksum_", calc_dbs_checksum(sample_data.o));// NB: calculate checksum after setting date format (i.e. after dbst)
        var new_checksum = getValue("checksum_");// checksum of the new data we intend to save now


        // console.log("old_checksum = " + old_checksum + ", new_checksum = " + new_checksum );
        // console.log("old_checksum === new_checksum => " + (old_checksum === new_checksum) );

        if(old_checksum === new_checksum){  return; /* no need to save. data hasn't changed */ }


    // step 3: save data

        var url = "/dbs";
        var dataType = "json";                
        
        var batch_id = $("#id").val();

        if(!batch_id ){
            unsaved_row = sample_data.rowNumber;
            setValue("checksum_", "__row_to_be_saved_after_batchData_is_entered__");
            
            console.log("failed to save Row #: " + unsaved_row + " because... no batch");            
            window.alert( "ERROR!\n\nFirst save the batch data" );

            return;
        }

        ajax_sample_data = sample_data.o;
        ajax_sample_data.rowNumber = sample_data.rowNumber;
        ajax_sample_data.batch_id = batch_id;

        console.log("sending this ajax_sample_data:");
        console.log(ajax_sample_data);

        console.log("here is the same ajax_sample_data as a string:");
        console.log( JSON.stringify(ajax_sample_data, null, 4) );

        $.get( url, ajax_sample_data, DBScallback, dataType);
        return;


    // utility functions:
        function calc_dbs_checksum(){// calculates sample's checksum

            var i = sample_data.rowNumber;
            var data, data_str, current_checksum;
                
                // console();
                console.log( sample_data.o );
                data = sample_data.o;
                data["checksum_" + i] = "";

                data_str = JSON.stringify(data);
                current_checksum = CryptoJS.MD5( data_str ) + "";

            return current_checksum;
        }

        function setValue(key, newValue, changeSourceField){

            
            var key_field;
            var key_location = findKey( key );
            if( key_location < 0){  return; /* invalid key */ }
            
            key_field = sample_data.fields[ key_location ];   
            
            sample_data.o[ key_field ] = newValue;
            sample_data.values[ key_location ] = newValue;

            if(changeSourceField === undefined) { changeSourceField = true; }
            if(changeSourceField === true){
                var srcField = $("#" + key + sample_data.rowNumber);
                var srcFieldExists = (srcField.length > 0);
                
                if( srcFieldExists ){ srcField.val(newValue); }
            }
        }

        function getValue(key){
            var key_location = findKey( key );
            if( key_location < 0){  return NaN; /* not found */ }

            return sample_data.values[ key_location ];
        }

        function findKey( key ){
        
            var thisField = key + sample_data.rowNumber; 
            return sample_data.fields.indexOf( thisField );
        }
    }

    function DBScallback( json_data ){

        var nextRow;
        var save_failed = (!json_data || !json_data.row_number || !json_data.row_id)? true : false ;

        console.log( "save DBS sample returned: ");
        console.log( json_data );

        if(save_failed){
            window.alert("Failed to save the sample");
            return;
        }

        $("#sample_" + json_data.row_number ).val( json_data.row_id );

        $("#row_" + json_data.row_number).notify( "Successfully saved this row's data",

            { position:"top center", className: "success" }
        );


        $("#row_" + json_data.row_number).removeClass("highlight_row");

        nextRow = parseInt(json_data.row_number) + 1;

        enableRow(nextRow);
        trigger_custom_event('dbs_data_changed', DBS.SAMPLE_FINISHED, json_data);
    }

    function trigger_custom_event (evt_name, operation, sample) {

        var i, e, event_data;
                
        if(operation === DBS.BATCH_FINISHED){
            event_data = {
                operation : DBS.BATCH_FINISHED
            };
        }
        else if(operation === DBS.SAMPLE_FINISHED){
            i = sample.row_number;
            event_data = {
                operation : operation,
                sample_id : sample.row_id,
                infant_name : ajax_sample_data['infant_name_' + i],
                infant_exp_id : ajax_sample_data['infant_exp_id_' + i],
                date_dbs_taken : ajax_sample_data['date_dbs_taken_' + i],
                PCR_test_requested : ajax_sample_data['PCR_test_requested_' + i],
                SCD_test_requested : ajax_sample_data['SCD_test_requested_' + i]
            };
        }

        e = new CustomEvent('dbs_data_changed', {'detail' : event_data });
        document.dispatchEvent(e);
    }



    function startsWith(h, n, case_sensitive){

        var needle = n.trim();
        var haystack = h.trim();
        
        case_sensitive = (case_sensitive === true) ? true : false;
        
        if( case_sensitive )
            return haystack.search( needle ) === 0;
        else
            return haystack.toLowerCase().search( needle.toLowerCase() ) === 0;
    }

    function format_phone_number(phone){

        var phone_number = phone.replace(/[^0-9]/g, '');
        var phone_number_length = phone_number.length;

        if(phone_number === EMPTY_STRING) return "";

        if(startsWith(phone_number, "2567")){  return (phone_number_length == 12)? phone_number: ""; }
        if(startsWith(phone_number, "2563")){  return (phone_number_length == 12)? phone_number: ""; }
        if(startsWith(phone_number, "2564")){  return (phone_number_length == 12)? phone_number: ""; }
        if(startsWith(phone_number,   "04")){  return (phone_number_length == 10)? "256" + phone_number.substring(1): ""; }
        if(startsWith(phone_number,   "07")){  return (phone_number_length == 10)? "256" + phone_number.substring(1): ""; }
        if(startsWith(phone_number,    "7")){  return (phone_number_length ==  9)? "256" + phone_number: ""; }

        return "";
    }

    $(".phone").on("change", function() {
        
        var formattedPhoneNo = format_phone_number(this.value);
        if(formattedPhoneNo === EMPTY_STRING){
            window.alert("Phone Number is NOT valid: Please type it again");
            $(this).addClass('data_error');
            // this.value = ""; // 
            this.value = this.value.replace(/\D+/g, "");
            return false;
        }
        this.value = "+" + formattedPhoneNo.replace(/([\S\s]{3})/g , "$1 ");
        $(this).removeClass('data_error');
    });


    function getRequestedTests(rowNumber) {


        var requestedTests = {};
        var nTestsRequested = 0;

        var PCR_test_requested = $("#PCR_test_requested_" + rowNumber).is(':checked');
        var SCD_test_requested = $("#SCD_test_requested_" + rowNumber).is(':checked');

        if(PCR_test_requested){
            nTestsRequested++;
            requestedTests["PCR"] = "yes";
        }

        if(SCD_test_requested){
            nTestsRequested++;
            requestedTests["SCD"] = "yes";
        }

        if(nTestsRequested === 0) requestedTests = null;

        return requestedTests;
    }


    function dataEntryCompleted( rowNumber ){// returns the data (or null if incomplete)
    
        var optionalFields = optionalFields || getOptionalFields(rowNumber);
            optionalFields["checksum_" + rowNumber] = true;
            optionalFields["sample_" + rowNumber] = true;
        
        var the_tests = getRequestedTests(rowNumber);
        var tests_requested = the_tests || {};

        $("#PCR_test_requested_"+rowNumber).val( ("PCR" in tests_requested) ? "YES" : "NO" );
        $("#SCD_test_requested_"+rowNumber).val( ("SCD" in tests_requested) ? "YES" : "NO" );

        if(the_tests === null){
            $("#PCR_test_requested_"+rowNumber).val("YES");// default test is PCR
            $("#PCR_label_"+rowNumber).addClass('highlight_label');
        }

        var this_row = $("#row_" + rowNumber).serializePanel("=");
        var nCols = this_row.k.length;

        if(nCols === 0){
            console.log("unexpected place to die!");
            return null;// row doesnt exist or it has no data  
        }

        var col_name;
        var col_value;
        var nullable_column;

        console.log( this_row );

        for(var i=0; i < nCols; i++){
            
            col_name = this_row.k[i];
            col_value = this_row.v[i].trim();

            this_row.v[i] = col_value;
            this_row.o[col_name] = col_value;

            nullable_column = col_name in optionalFields;
            
            if(col_value === EMPTY_STRING && !nullable_column){
                console.log("optionalFields: [nullable_column = " + nullable_column + "]");
                console.log(optionalFields);
                console.log("INCOMPLETE: {col_name = " + col_name + ", col_value = " + col_value + "}" );
                return null;// incomplete: this value is required
            }
        }

        var data = {};
            data.o = this_row.o;
            data.fields = this_row.k;
            data.values = this_row.v;
            data.rowNumber = rowNumber;
        
        return data;
    }


    $.fn.serializePanel = function(kv_sep) {
        //
        //  @Author: Richard K. Obore, Tornado Unit, The Better Data Company
        //
        //  3rd implementation I made based on uncle Tobias's code.
        //      2nd verion = serializeAllObjects() = I modified it to include radio buttons, un-ticked checkboxes, etc...
        //      3rd version = serializePanel() = It returns an object, p, with the following fields
        //
        //          p.o = the exact same object as would be returned by serializeAllObjects()
        //          p.k = an array containing the field names (i.e. keys) in the object
        //          p.v = an array containing the values of each field in the object, in the same order as their keys (as per p.k) 
        //          p.kv = a string containing key-value pairs, adjusted for use in an SQL update query
        //
        //
        //  You can convert all the input fields inside "this" jQuery object (which is usually a form) into a JSON object
        //  as follows:
        //      json_object = $('form_id').serializePanel().o;
        //
        //  You can then stringify() it for database storage or AJAX transmission:
        //      json_string = JSON.stringify(json_object);
        //


        var o = {};
        var p = {};
        var keys = [];
        var values = [];
        var quoted_values = [];
        var kv = [];//

        var a = this.find(":input");
        

        $.each(a, function() {

            var field_name = this.id || this.name;
            var field_value = this.value;


            if (o[field_name] !== undefined) {
                if (!o[field_name].push) {
                    o[field_name] = [o[field_name]];
                }
                o[field_name].push(field_value || "");
            } else {
                o[field_name] = field_value || "";
            }

            var this_key = field_name;
            var this_value = "'"+ replaceAll(field_value, "'", "`") + "'";// modified for use in DB (note the single quotes) 
            
            kv_sep = kv_sep || " = ";

            kv.push(this_key + kv_sep + this_value);
            keys.push(this_key);
            quoted_values.push(this_value);
            values.push(field_value);// the unmodified value        
        });
         
        p.o = o;
        p.k = keys;
        p.v = values;
        p.kv = kv;
        p.qv = quoted_values;   

        return p;


    // helper functions:
        function escapeRegExp(str) {
            return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        }

        function replaceAll(haystack, needle, replace) {
            // This function handles many cases correctly - but test with your data before you use e.g.  XML or anything with $$ fails 
            return haystack.replace(new RegExp(escapeRegExp(needle), "g"), replace);
        }
    };

    var NUM_ROWS_IN_TABLE = 14;
    var nRows_WithData = 0;

    for(var n=1; n<=NUM_ROWS_IN_TABLE; n++){
        
        var row_has_data = $("#sample_"+n).val();
        var row_is_editable = rowIsEditable(n);
        
        if(row_has_data && row_is_editable) {
            nRows_WithData++;
            enableRow(n);
        }
        else 
            disableRow(n);
    }

    if(rowIsEditable(1) && nRows_WithData < NUM_ROWS_IN_TABLE){

        var first_empty_row = nRows_WithData + 1;
        enableRow(first_empty_row);
    }

    function disableRow(rowNumber) {

        lockRow(rowNumber, true);
    }

    function enableRow(rowNumber) {
        
        if(rowIsEditable(rowNumber))
            unlockRow(rowNumber);
    }

    function unlockRow(rowNumber) {
        lockRow(rowNumber, false);
    }

    function rowIsEditable(rowNumber) {

        return true; // un-comment code below if bosses decide to use it again


        // var sv = document.getElementById("sample_verified_" + rowNumber);
        // var sample_has_been_verified = sv && (sv.value === "YES");
        
        // if(sample_has_been_verified) 
        //     return false;
        // else 
        //     return true;
    }

    function lockRow(rowNumber, lock_this_row) {

        if( ! isFinite(rowNumber) ) return;// bad input. stop.

        var row = 'tr#row_'+rowNumber+'.parent_row :input';

        var new_state = lock_this_row ? true : false;

        $(row).prop('disabled', new_state); 
    }

            $(document.body).on("change","#facility_selector",function(){

                var this_facility = JSON.parse( this.value );

                $("#facility_id").val(this_facility.facility_id);
                $("#facility_name").val(this_facility.facility_name);
                $("#facility_district").val(this_facility.district.toUpperCase());
                if(this_facility.scd_high_burden=='YES'){
                    $("#scd_high_burden").show();
                }else{
                    $("#scd_high_burden").hide();
                } 
            });

            $("#facility_selector").on("focus", function function_name (argument) {
                alert('xx');
            });

            function format_dates(){
            
                var formatted_date;
                var dateFields = $(".datepicker");
                var nDateFields = dateFields.length;
                var current_dateField;

                for(var i=0; i<nDateFields; i++){
                    current_dateField = dateFields[i];

                    if(current_dateField.defaultValue === "") continue;
                    formatted_date = moment(current_dateField.defaultValue).format("Do MMM YYYY");
                    
                    if(formatted_date === "Invalid date")
                        current_dateField.value = "";
                    else
                        current_dateField.value = formatted_date;

                }
            }


            $('.dbs_date').each(function (){
    
                var this_dateField = $(this);

                    this_dateField.pikaday({
                                    firstDay: 1,
                                    minDate: new Date('2010-01-01'),
                                    maxDate: new Date(),
                                    defaultDate: new Date(this.value),
                                    setDefaultDate: true,
                                    format: 'Do MMM YYYY'
                    });
            });

            $('#date_dispatched_from_facility').pikaday({
                    firstDay: 1,
                    minDate: new Date('2010-01-01'),
                    maxDate: new Date(),
                    defaultDate: extractDate('date_dispatched_from_facility'),
                    setDefaultDate: true,
                    format: 'Do MMM YYYY'
            });


            function extractDate(field_id, defaultDate) {
                var the_date = $('#'+field_id).val();

                if(!the_date ){
                    return defaultDate;
                }else{    
                    return new Date(moment(the_date, ['Do MMM YYYY','YYYY-MM-DD']).format('YYYY-MM-DD'));
                }
            }



            $('#date_rcvd_by_cphl').pikaday({
                    
                    firstDay: 1,
                    minDate: new Date('2010-01-01'),
                    maxDate: new Date(),
                    defaultDate: extractDate('date_rcvd_by_cphl'),
                    setDefaultDate: true,
                    format: 'Do MMM YYYY',


                    onSelect: function xx (argument) {

                    var dispatch_date = $('#date_dispatched_from_facility').val();
                    var received_date = $('#date_rcvd_by_cphl').val();

                    if(! dispatch_date ){
                        alert('Select the dispatch date first!');
                        $('#date_rcvd_by_cphl').val('');
                        return;
                    }

                    dispatch_date = moment(dispatch_date, 'Do MMM YYYY').format('YYYY-MM-DD');
                    received_date = moment(received_date, 'Do MMM YYYY').format('YYYY-MM-DD');

                    if(moment(dispatch_date).isAfter(received_date)){
                        alert('Check your dates:\n\nSamples cant be Received before they are dispatched ');
                        $('#date_rcvd_by_cphl').val('');
                    }


                    $('.datepicker').pikaday({
                        maxDate: new Date(dispatch_date),
                        firstDay: 1,
                        format: 'Do MMM YYYY'
                    });

                },
                maxDate: new Date(),
                format: 'Do MMM YYYY'
            });

            $("input:checkbox[do_pcr]").on("change", function() {

                var rowNum = this.getAttribute('do_pcr');

                if(this.checked)
                    $(".pcr_"+rowNum).prop( "disabled", false );
                else
                    $(".pcr_"+rowNum).prop( "disabled", true );

                tryToSaveRow(rowNum);

            });

            $("input:checkbox[do_scd]").on("change", function() {


                var rowNum = this.getAttribute('do_scd');
                var pcr = document.getElementById('PCR_test_requested_'+rowNum);

                var scd_is_checked = this.checked;
                var pcr_is_checked = pcr.checked;

                if(scd_is_checked && !pcr_is_checked ){
                    $(".pcr_"+rowNum).prop( "disabled", true );
                    tryToSaveRow(rowNum);
                }
                
            });

            $(".js-example-basic-single").select2();
            format_dates();

            $("#show_axn_numbers").on("click", function axn() {

                $("input[name=sample]").each(function (i) {
                    if(this.value) {
                        var options = {position: "right", autoHide: false, className: "info"};
                        $("#infant_age_"+(i+1)).notify("Accession Number: " + this.value, options);
                    }
                });

                trigger_custom_event('dbs_data_changed', DBS.BATCH_FINISHED);
            });

            $("#submit_samples_bottom").on("click", function () {
                
                var i = 1;

                for(i=1; i<=NUM_ROWS_IN_TABLE; i++){
                    tryToSaveRow(i);
                }

                location.href='/samples';
            });

// var xxx = moment().format("YYYY-MM-DD HH:mm:ss");
// alert(xxx);

});