"use strict";
/* global $, console*/
$(document).ready(function() { 
	const RETAIN_BATCH = "NO";
	const RELEASE_BATCH = "YES";

	$("#tab_id").DataTable({"bSort" : false, "deferRender": true});

	$(".trigger_button").on("click", function (){
		checkClasses(this);

		if($(this).hasClass("click_to_release")){
			update_batch(this, RELEASE_BATCH);
		}
		else if($(this).hasClass("click_to_retain")){
			update_batch(this, RETAIN_BATCH);
		}
		return false;
	});


	$("#release_all").on("click", function  () {

		var this_batch;
		var ajax_request;
		var release_batches = {};
			release_batches.yes = [];
			release_batches.no = [];

		$("input.batches").each(function  () {
			this_batch = this.getAttribute("key");
			
			if(this.value === "YES"){
				release_batches.yes.push(this_batch);
			}
			else if(this.value === "NO"){
				release_batches.no.push(this_batch);
			}
		});

		release_batches.no = release_batches.no.join(", ");
		release_batches.yes = release_batches.yes.join(", ");
		release_batches.worksheet_no=$("#wksht_id").val();

        ajax_request = $.ajax({url: "/eid_release", data: release_batches});

		ajax_request.done(function (argument) {
        	location.href = "/wlist";
        });
		
		ajax_request.fail(function (argument) {
        	alert("failed to save the data");
        });

	});


	function update_batch (link, new_status) {

		var batch_id = $(link).attr("batch");
		var hidden_input = $("#release_"+batch_id);
			hidden_input.val(new_status);
			update_css(link, new_status,batch_id);
			console.log( "Set PCR_results_released = " + new_status + " [ Batch: " + batch_id + "]");
		return false;
	}

	function update_css (target_element, release_this_batch,batch_id) {

		var link = $(target_element);

		if(release_this_batch == "YES"){
			link.text("Ratain");
			link.removeClass("btn-default click_to_release");
			link.addClass("btn-danger  click_to_retain");
			$("#status"+batch_id).html("<span class='status_ok'>Released</span>");
			return;
		}

		if(release_this_batch == "NO"){
			link.text("Release");
			link.removeClass("btn-danger click_to_retain");
			link.addClass("btn-default click_to_release");
			$("#status"+batch_id).html("<span class='status_danger'>Not released</span>");
			return;
		}
	}

/*
	go thru every hidden input with class batches
*/
	function checkClasses (elem) {
		var link = $(elem);
		console.log("the clicked link has the following classes");
			console.log("\t btn-danger = " + link.hasClass("btn-danger") );
			console.log("\t btn-default = " + link.hasClass("btn-default") );
			console.log("\t click_to_release = " + link.hasClass("click_to_release") );
			console.log("\t click_to_retain = " + link.hasClass("click_to_retain") );
	}
});