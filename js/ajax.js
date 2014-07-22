//Holds the Javascript for form validation as well as the .ajax for the user creation pass to update.php

//Grab the user information from the page and send it to be processed
$(function addUser () {
	//Hide the error messages
    $('.error').hide();
    //When the create button is pressed
    $(".addUser").click(function() {
	  // validate the name input field      
	  $('.error').hide();
	  var realName = $("input#realName").val();
	  var campusId = $("input#campusId").val();
	  var addedBy = $("input#addedBy").val();
		  
		if (realName == "") {
			//show the error if the field is empty
			$("label#realName_error").show();
			$("input#realName").focus();
			return false;
		}
		if (campusId == "") {
			//show the error if the field is empty
			$("label#campusId_error").show();
			$("input#campusId").focus();
			return false;
		}

	  //create the POST values to pass to callBack.php
	  var dataString = 'realName=' + realName + '&campusId=' + campusId + '&addedBy=' + addedBy + '&action=' + 'adduser';  		
		$.ajax({  
		  type: "POST",  
		  url: "update.php",	
		  data: dataString, 
		  cache: false,
		  dataType: 'html', 
		  error: function(jqXHR, exception) {
			if (jqXHR.status === 0) {
				alert('Not connect.\n Verify Network.');
			} else if (jqXHR.status == 404) {
				alert('Requested page not found. [404]');
			} else if (jqXHR.status == 500) {
				alert('Internal Server Error [500].');
			} else if (exception === 'parsererror') {
				alert('Requested JSON parse failed.');
			} else if (exception === 'timeout') {
				alert('Time out error.');
			} else if (exception === 'abort') {
				alert('Ajax request aborted.');
			} else {
				alert('Uncaught Error.\n' + jqXHR.responseText);
			}
		},
		  success: function(response) {
				if(response.trim() == "duplicate"){
					document.getElementById('createStatus').innerHTML='<span style="color:red">ERROR: User already an Admin.</span>';
				}
				else{		  
					//Print out the makePad response (a link to the pad) and the password if there was one;
					document.getElementById('createStatus').innerHTML+='<span style="color:green;">SUCCESS: Added User</span>';
					document.getElementById('currentAdmins').innerHTML+=response;
				}
			}
		});  
		return false; 
    });
});

//Pass a username to remove it from the admin users
function delAdmin(campusID, uid){
	//Check if they really want to delete the admin
	var answer = confirm('Are you sure you want to remove admin: '+campusID+'?');
	if (answer)	{
		  console.log('Remove Admin: '+campusID);
		  //Build the string to POST
		  var dataString = 'campusID=' + campusID + '&uid=' + uid + '&action=' + "deluser";  		
		$.ajax({  
		  type: "POST",  
		  url: "update.php",	
		  data: dataString, 
		  cache: false,
		  dataType: 'html', 
		  error: function(jqXHR, exception) {
			if (jqXHR.status === 0) {
				alert('Not connect.\n Verify Network.');
			} else if (jqXHR.status == 404) {
				alert('Requested page not found. [404]');
			} else if (jqXHR.status == 500) {
				alert('Internal Server Error [500].');
			} else if (exception === 'parsererror') {
				alert('Requested JSON parse failed.');
			} else if (exception === 'timeout') {
				alert('Time out error.');
			} else if (exception === 'abort') {
				alert('Ajax request aborted.');
			} else {
				alert('Uncaught Error.\n' + jqXHR.responseText);
			}
		},
		  success: function(response) {
				//Print out the makePad response (a link to the pad) and the password if there was one;
				if (typeof response != 'undefined') {
					console.log(response);
					//Print out the pad that was deleted
					document.getElementById('delStatus').innerHTML='<span style="color:green">SUCCESS:'+response+'</span>';
					//Hide the row that was deleted
					document.getElementById("admin-"+uid).style.display="none";
				}
				else{
					console.log(response);
					document.getElementById('delStatus').innerHTML+='<span style="color:red">ERROR: removing user';
				} 
			}
		  
		})
	}
	else
	{
	  console.log('Don\'t Delete!');
	}
}

$(function addGuide () {
  	 //Hide the error messages
    $('.error').hide();
    //When the create button is pressed
    $(".addGuide").click(function() {	
		// validate the name input field      
		$('.error').hide();
		var subject = $("input#subject").val();
		var code = $("input#code").val();
		var link = $("input#link").val();
		var addedBySub = $("input#addedBySub").val();
		if (subject == "") {
			//show the error if the field is empty
			$("label#subject_error").show();
			$("input#subject").focus();
			return false;
		}
		if (code == "") {
			//show the error if the field is empty
			$("label#code_error").show();
			$("input#code").focus();
			return false;
		}
		if (link == "") {
			//show the error if the field is empty
			$("label#link_error").show();
			$("input#link").focus();
			return false;
		}

		//create the POST values to pass to callBack.php
		var dataString = 'subject=' + subject + '&code=' + code + '&link=' + link + '&addedBySub=' + addedBySub + '&action=' + 'addguide';  		
		$.ajax({  
		  type: "POST",  
		  url: "update.php",	
		  data: dataString, 
		  cache: false,
		  dataType: 'html', 
		  error: function(jqXHR, exception) {
			if (jqXHR.status === 0) {
				alert('Not connect.\n Verify Network.');
			} else if (jqXHR.status == 404) {
				alert('Requested page not found. [404]');
			} else if (jqXHR.status == 500) {
				alert('Internal Server Error [500].');
			} else if (exception === 'parsererror') {
				alert('Requested JSON parse failed.');
			} else if (exception === 'timeout') {
				alert('Time out error.');
			} else if (exception === 'abort') {
				alert('Ajax request aborted.');
			} else {
				alert('Uncaught Error.\n' + jqXHR.responseText);
			}
		},
		  success: function(response) {
				//Print out the makePad response (a link to the pad) and the password if there was one;
				document.getElementById('createStatusGuide').innerHTML=' ';
				document.getElementById('createStatusGuide').innerHTML+='<span style="color:green;">Added Guide: '+response+'</span>';
			}
		});  
		return false;		  
	});
});
//Check and then pass to delete on user selection  
function checkDel(guideID, guideName){
		//Check if they really want to delete the guide
		var answer = confirm('Delete the LibGuide for: '+guideName);
		if (answer)	{
			  console.log('Delete: '+guideName);
			  //Build the string to POST
			  var dataString = 'guideName=' + guideName + '&cid=' + guideID + '&action=' + "deleteGuide";  		
			$.ajax({  
			  type: "POST",  
			  url: "update.php",	
			  data: dataString, 
			  cache: false,
			  dataType: 'html', 
			  error: function(jqXHR, exception) {
				if (jqXHR.status === 0) {
					alert('Not connect.\n Verify Network.');
				} else if (jqXHR.status == 404) {
					alert('Requested page not found. [404]');
				} else if (jqXHR.status == 500) {
					alert('Internal Server Error [500].');
				} else if (exception === 'parsererror') {
					alert('Requested JSON parse failed.');
				} else if (exception === 'timeout') {
					alert('Time out error.');
				} else if (exception === 'abort') {
					alert('Ajax request aborted.');
				} else {
					alert('Uncaught Error.\n' + jqXHR.responseText);
				}
			},
			  success: function(response) {
					//Check if the delete was successful
					if (response == 1 && typeof response != 'undefined'  ) {
						console.log(response);
						//Print out the pad that was deleted
						document.getElementById('deleteStatus').innerHTML='<span style="color:green">Successfully deleted guide: '+guideName+'</span>';
						//Hide the row that was deleted
						document.getElementById("guide-"+guideID).style.display="none";
					}
					else{document.getElementById('deleteStatus').innerHTML+='<span style="color:red">Error deleting pad: '+guideName;} 
				}
			  
			})
		}
		else
		{
		  console.log('Don\'t Delete!');
		}

} 

//User manual force refresh of the guides from the API
function refreshAPI(){

	document.getElementById('apiStatus').innerHTML='<span style="color:orange">Working... This takes a few minutes.</span>';
	
	$.ajax({  
	  type: "POST",  
	  url: "cronAdd.php",	
	  data: {refresh: 'manual'}, 
	  error: function(jqXHR, exception) {
		if (jqXHR.status === 0) {
			alert('Not connect.\n Verify Network.');
		} else if (jqXHR.status == 404) {
			alert('Requested page not found. [404]');
		} else if (jqXHR.status == 500) {
			alert('Internal Server Error [500].');
		} else if (exception === 'parsererror') {
			alert('Requested JSON parse failed.');
		} else if (exception === 'timeout') {
			alert('Time out error.');
		} else if (exception === 'abort') {
			alert('Ajax request aborted.');
		} else {
			alert('Uncaught Error.\n' + jqXHR.responseText);
		}
	},
	  success: function(response) {
			//Print out the makePad response (a link to the pad) and the password if there was one;
			if (response == "done" && typeof response != 'undefined'  ) {
				console.log(response);
				//Print out the pad that was deleted
				document.getElementById('apiStatus').innerHTML='<span style="color:green">Update Completed. You\'ll need to refresh the tool to see the changes above.</span>';
			}
			else{document.getElementById('apiStatus').innerHTML+='<span style="color:red">Updated Failed</span>';} 
		}	  
	})
}


