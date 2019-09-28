// JavaScript Document

// js for responsive web menus
function toggle_Hide(id){
var e = document.getElementById(id);

if (e.style.display == 'block')
{
    e.style.display = 'none';
    e.removeAttribute('style');
} else {
    e.style.display = 'block';
}
}

// set height of navigation bar on change of data
function set_filter_height (body_height) {
	
	// get current width
	var current_window_width = $( window ).width();
	// get current height
	var current_filter_height = $( "#body_filter, #body_left, #body_right" ).outerHeight();
	
	if (current_window_width >= 920) {
				
		// if variable body_height greater than navigation extend
		if (body_height >= current_filter_height) {
			
			$( "#body_filter, #body_left, #body_right" ).outerHeight( body_height );
		
		// if less then go to 100% of page
		} else {
			
			$( "#body_filter, #body_left" ).outerHeight( 'auto' );
			// left is bigger than right so set right as same height as left
			var current_body_left_height = $( "#body_filter, #body_left" ).outerHeight();
			$( "#body_right" ).outerHeight( current_body_left_height );
			
		}
	
	}
				
}


$(document).ready(function(){

// set default height for left body
defaultheight = $("#body_page").height();
set_filter_height (defaultheight);
		
	// open the popup window
	$(".open_popup").click(function(){

	var popup_func = $(this).attr("popup_func");
	var popup_id = $(this).attr("popup_id");
		
	if(typeof popup_id != "undefined"){
	
		$.ajax({
			url: "ajax.popup_id.php",
			type: "post",
			data: { popupfunc: popup_func, popupid : popup_id },
			cache: false,
			success: function(iddata){
				$(".ticket_summary").html( iddata );
			},
			error:function(){
				$(".ticket_summary").html( "ID load failed" );
			}
		});
		
	}
		
	$(".overlay, .popup").fadeToggle();
		
	});
	
	// close the popup window
	$(".overlay, .close_popup").click(function(){
		
		$( ".overlay" ).hide();
		$( ".popup" ).hide();
			
	});
	
	// prevent popup closing on click within
	//$("#merge_search").click(function() {
	//	return false;
	//});
	
	// on ticket search
	$("#merge_search").click(function(event){
		var merge_input = $("#merge_input").val();
		var tid = $('#tid').attr('tid');
		tid = tid*1; // strip leading zeros off by * by 1.

		if (tid == merge_input) {
			
			$("#merge_results").html( "<div id=inner_merge_results>Ticket cannot be joined to itself</div>" );
			
		} else {
			
			$.ajax({
				url: "ajax.merge_results.php",
				type: "post",
				data: { from_tid : tid, merge_data : merge_input },
				cache: false,
				success: function(mergedata){
					$("#merge_results").html( mergedata );
				},
				error:function(){
					$("#merge_results").html( "Search failed" );
				}
			});
			
		}
		// prevent popup from closing
		return false;

	});
	
	
	$("#password_chg").click(function(event){
		
		var uid = $("#user_id").val();
		var newpwd = $("#newpwd").val();
		var confirmedpwd = $("#confirmpwd").val();
			
		if (newpwd == "" || confirmedpwd == "") {
			
			$("#pwd_result").html( "<span class='error'>! You must enter your password in both fields.</span>" );
				
		} else if (newpwd != confirmedpwd) {
		
			$("#pwd_result").html( "<span class='error'>! Your new passwords don't match. Try again.</span>" );

		} else if (newpwd.length < 6) {
			
			$("#pwd_result").html( "<span class='error'>! Your new password must be longer than 6 characters.</span>" );
			
		} else {
			
			$.ajax({
				url: "ajax.pwchange.php",
				type: "post",
				data: { user : uid, new_pwd : newpwd },
				cache: false,
				success: function(pwchgdata){
					$("#pwd_result").html( "<div class=\"success\">" + pwchgdata + "</div>" );
					$('#profile_pw_change').trigger("reset");
				},
				error:function(){
					$("#pwd_result").html( pwchgdata );
				}
			});
				
		}
		
		return false;
		
	});
	
	function mobile_sort () {
		var windowwidth = $(window).width();
		
		if (windowwidth <= 920) {
			$( "#search" ).insertAfter( "#header" );
			$( "#search_action" ).insertAfter( "#search_input" );
			$( "#body_right" ).insertAfter("#table_header");
		} else {
			$( "#search" ).insertAfter( "#links" );
			$( "#search_input" ).insertAfter( "#search_action" );
			$( "#body_right" ).insertAfter("#body_page");					
		}
	
	}
	
	
	// run sort on page load
	mobile_sort();
	
	// re sort if page is resized
	$(window).resize(function () {
		mobile_sort();
	});
	
	// copyright notice
	var acornaid_footer = $("#footer").html("<p>Created by <a href=\"http://www.blueswell.co.uk\">blueswell.co.uk</a></p>");
	
	if ($("#footer").not(":contains('blueswell')").length == 1) {
		
		acornaid_footer;

	} else if ($("#footer").children().css('visibility') == 'hidden') {
		
		acornaid_footer;
		
	} else if ($("#footer").children().css('display') == 'none') {
		
		acornaid_footer;

	}

	
});

