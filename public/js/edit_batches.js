"use strict";
/*jshint quotmark: false */
/* global $, console, moment, odiff, APPROVAL_MODULE  */

var DBS = {};    
    DBS.BATCH_STARTED = "BATCH_STARTED";
    DBS.BATCH_HEADER_SAVED = "BATCH_HEADER_SAVED";
    DBS.BATCH_FINISHED = "BATCH_FINISHED";

    DBS.SAMPLE_STARTED = "SAMPLE_STARTED";
    DBS.SAMPLE_FINISHED = "SAMPLE_FINISHED";

    DBS.DATA_ENTRY = "DATA_ENTRY";
    DBS.DATA_VERIFICATION = "DATA_VERIFICATION";
    DBS.VERIFICATION_COMPLETED = "VERIFICATION_COMPLETED";
    DBS.IGNORE_EVENTS = false;

$(function  () {
    document.addEventListener('dbs_data_changed', getNewData, false);

    var buffer = {};
        buffer.batch = {};
        buffer.samples = {};
        buffer.operation = "";
        buffer.durations = {};
        buffer.batch_duration = null;
        buffer.sample_durations = {};

    function getNewData(evt) { 

        // alert('reached getNewData()');
        // console.log('getNewData().  DBS.IGNORE_EVENTS = ' + DBS.IGNORE_EVENTS);        

        var e = evt.detail;

        if(DBS.IGNORE_EVENTS) 
            return true; /* stop here - ignore the new data */

        if ((e.operation === DBS.BATCH_STARTED) || (e.operation === DBS.BATCH_HEADER_SAVED)) {
            get_batch_data(e);
            return true;
        }

        if ((e.operation === DBS.SAMPLE_STARTED) || (e.operation === DBS.SAMPLE_FINISHED)) {
            get_sample_data(e);
            return true;
        }

        if (e.operation === DBS.BATCH_FINISHED) {
            ajax_flush( buffer );
        }

        if(e.operation === DBS.VERIFICATION_COMPLETED){
            update_batch_data(e);
            ajax_flush( buffer );   
        }

        return true;
    }

    function update_batch_data(e){
        var batch_data = {
                batch_number    : e.batch_number,
                batch_id        : String(buffer.batch.batch_id),
                date_dispatched_from_facility   : e.date_dispatched_from_facility,
                date_rcvd_by_cphl               : e.date_rcvd_by_cphl
        };

        buffer.batch = batch_data;
    }

    function get_sample_data ( e ) {

        var this_sample = "sample_" + e.sample_id;

        buffer.samples[this_sample] = {
            sample_id : String(e.sample_id),
            infant_name : e.infant_name,
            infant_exp_id : e.infant_exp_id,
            date_dbs_taken : e.date_dbs_taken,
            PCR_test_requested : e.PCR_test_requested,
            SCD_test_requested : e.SCD_test_requested
        };

        buffer.sample_durations[this_sample] = moment().unix();

        console.log('get_sample_data()...buffer = ');
        console.log(buffer);
    }

    function get_batch_data ( e ) {

        if(e.operation === DBS.BATCH_STARTED){
            get_batch_data.start_time = get_batch_data.start_time || moment().format("YYYY-MM-DD HH:mm:ss");
            console.log("get_batch_data.start_time = " + get_batch_data.start_time);
            return;
        }

        if(e.operation === DBS.BATCH_HEADER_SAVED){
            get_batch_data.stop_time = get_batch_data.stop_time || moment().format("YYYY-MM-DD HH:mm:ss");

            var batch_data = {
                    batch_number    : e.batch_number,
                    batch_id        : String(e.batch_id),
                    date_dispatched_from_facility   : e.date_dispatched_from_facility,
                    date_rcvd_by_cphl               : e.date_rcvd_by_cphl
            };
            var duration_data = {
                    batch_started : get_batch_data.start_time,
                    batch_header_saved : get_batch_data.stop_time
            };
            var checking_data = (APPROVAL_MODULE && APPROVAL_MODULE.original_dbs_data);

            buffer.batch = batch_data;
            buffer.operation =  checking_data ? DBS.DATA_VERIFICATION : DBS.DATA_ENTRY;
            buffer.batch_duration = buffer.batch_duration || duration_data;

            console.log("get_batch_data(). buffer = ");
            console.log(buffer);
        }
    }


    function time_between (t1, t2) {

        console.log('reached time_between()...');

        var start = moment(t1);
        var end = moment(t2);        
        var t = end.diff(start, 'seconds', false);// true = return decimal part
            t = parseFloat(t);
            t = Math.abs( t );
            t = +t.toFixed(0);

        console.log("time_between(" + t1 + ", " + t2 + ") = " + t + " seconds");
        return t;
    }

    function ajax_flush (buf) { // submits the data that had accumulated in the buffer
        
        var this_operation = buffer.operation;
        var old_data, this_data, url, data_to_send;

        buffer.batch_duration.batch_finished = moment().format("YYYY-MM-DD HH:mm:ss");

        this_data = to_odiff_object(buf);
        data_to_send = this_data;
        url = "/data_entry_speed";

        console.log("this_data = ");
        console.log(JSON.stringify(this_data, null, 4));

        if(this_operation == DBS.DATA_VERIFICATION){
            
            old_data = JSON.parse(APPROVAL_MODULE.original_dbs_data);

            data_to_send = {};
            data_to_send.batch_id = this_data.batch_id;
            data_to_send.meta_data = this_data.meta_data;

            delete old_data.meta_data;
            delete this_data.meta_data;

            data_to_send.changes = odiff( old_data, this_data);

            console.log("old_data (sans meta_data) = ");
            console.log(JSON.stringify(old_data, null, 4));

            console.log("ajax_flush(). data_to_send (i.e. meta_data + changes between new and old data) = ");
            console.log( JSON.stringify(data_to_send, null, 4) );
        }
        
        $.get( url, data_to_send);
    }

    // function ajax_flush (buf) { // submits the data that had accumulated in the buffer
        
    //     var odiff_obj, odiff_str;

    //     buffer.batch_duration.batch_finished = moment().format("YYYY-MM-DD HH:mm:ss");
    //     console.log("ajax_flush(). buffer = ");
    //     console.log( JSON.stringify(buf, null, 4) );

    //     odiff_obj = to_odiff_object(buf);
    //     console.log("ajax_flush(). to_odiff_object = ");
    //     odiff_str = JSON.stringify(odiff_obj, null, 4);
    //     console.log(odiff_str);

    //     $.get( "/data_entry_speed", odiff_obj);

    //     if(APPROVAL_MODULE && APPROVAL_MODULE.original_dbs_data){
            
    //     }
    // }

    function to_odiff_object(data_buffer) { /* input param = a buffer object containing batch data */

        var batch_id = String(data_buffer.batch.batch_id);

        var odiff_obj = {};
            odiff_obj["batch_id"] = batch_id;
            odiff_obj["batch_" + batch_id] = data_buffer.batch;
            odiff_obj["samples"] = data_buffer.samples;
            odiff_obj["meta_data"] = {
                operation : data_buffer.operation,
                time_stamps: buffer.batch_duration,
                durations : mk_duration_obj(buffer) /* laravel to add: data_entry_date and user_id */
            };

        return odiff_obj;
    }


    function count_samples (obj) {
        var n = 0;
        for(var x in obj) n++;
        return n;
    }

    function mk_duration_obj (data_buffer) {

        var durations_obj = [];
        var batch_started = data_buffer.batch_duration.batch_started;
        var batch_header_saved = data_buffer.batch_duration.batch_header_saved;
        var batch_finished = data_buffer.batch_duration.batch_finished;

        var batch_dur = {
                data_type :   'BATCH_HEADER',
                batch_id : data_buffer.batch.batch_id,  
                sample_id : 0, // zero, because this is a batch header not a sample
                time_stamp : moment(batch_header_saved).unix(),
                seconds_used : time_between(batch_started, batch_header_saved)
        };

        durations_obj.push(batch_dur);

        var tot_time_to_process_batch = time_between(batch_started, batch_finished);// batch header + all samples
        var nSamples_in_batch = count_samples(data_buffer.samples);
        var avg_time_per_sample = Math.ceil(tot_time_to_process_batch / nSamples_in_batch);

        for(var sample_id in data_buffer.sample_durations){

            var this_sample_id = sample_id.replace("sample_", "");
            var sample_completion_time = data_buffer.sample_durations[sample_id];

            var sample_dur = {
                data_type : 'SAMPLE',
                batch_id : String(data_buffer.batch.batch_id),
                sample_id : this_sample_id,
                time_stamp : sample_completion_time,
                seconds_used : avg_time_per_sample
            };

            durations_obj.push(sample_dur);
        }
        return durations_obj;
    }
});
