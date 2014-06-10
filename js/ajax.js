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
		  		//Print out the makePad response (a link to the pad) and the password if there was one;
				document.getElementById('createStatus').innerHTML+='<span style="color:green;">Added User: '+response+'</span>';
				document.getElementById('currentAdmins').innerHTML+=response;
    		}
		});  
		return false;      
    });
});

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
					//Print out the makePad response (a link to the pad) and the password if there was one;
					if (response !== "An error has occurred." && response !== "FALSE" && typeof response != 'undefined'  ) {
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
