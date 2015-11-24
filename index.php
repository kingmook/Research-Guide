<?php
//mbrousseau - CPI - Created June 2014 - Updated July 2014
//LTI Provider for Library Research Guide Re-direction
//Very basic LTI connection. Not checking signatures or comparing nonces as redirect content is public.
//Takes a site name via context_id and parses the name in the format CODE#### (ie. ABED4F84)
//Can deal both with 4 long subject codes (ABED) and 8 long course codes (ABED4F84)

//Bring in the DB credentials
require_once("info.php");

// Load up the LTI Support code
require_once 'ims-blti/blti.php';

// Initialize, all secrets as 'secret', do not set session, and do not redirect
$context = new BLTI($lti_auth['secret'], false, false);

//PDO to the database
$dbConnection = new PDO('mysql:dbname='.$database.';host='.$host.';charset=utf8', $username, $password);
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Default HTML
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml">';
echo '<head><meta http-equiv="content-type" content="text/html;charset=utf-8" /><title>LibGuide LTI</title>';

//Grab the css
echo '<link href="css/styles.css" type="text/css" rel="stylesheet" media="all" />';				

//Make sure they have sent a course id
if (isset($context->info['context_id']) || isset($_GET['course']) ){
		//---------------------Login to Admin Area------------------------------//
		//Start a session
		session_start();
		
		//Hit the database to check if they are an administrator
		$statement = 'SELECT * FROM `adminUsers` WHERE `campusId` = ?';
		$exec = $dbConnection->prepare($statement);
		$exec->execute(array($context->info['lis_person_sourcedid']));
		
		$adminResult = $exec->fetch(PDO::FETCH_ASSOC);
			
		//They're an admin, show them the configuration options
		if(isset($adminResult['realName']) && !isset($_GET['course'])){
			if ($context->valid===TRUE){
				
				//Hash the time
				$timeHash = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $timeKey, (time()), MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
				
				//Make a cookie to decrease ajax faking
				$_SESSION['secure']=$timeHash;
				
				//Check if we're adding a new administrator
				if (isset($_POST['realName']) && isset($_POST['campusID'])){
					echo "Adding new administrative user: ".$_POST['realName']." / ".$_POST['campusID'];
				}

				//Datatables include and config
				echo '<!--Datatables include and config -->
				<style type="text/css" title="currentStyle">
				@import "css/table.css";
				body{margin: 0px !important;};
				</style>
				<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
				<script type="text/javascript" language="javascript" src="js/ajax.js"></script>
				<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
				<!--Edittable includes-->
				<script src="js/jquery.validate.js" type="text/javascript"></script>
				<script src="js/jquery.dataTables.editable.js" type="text/javascript"></script>
				<script src="js/jquery.jeditable.js" type="text/javascript"></script>';
				
				//End the head
				echo '</head><body>';			
				
				//Main header
				echo '<h1 style="padding-top:20px">LibGuide-Administration</h1>';
				
				//Return to the guide - DEBUG (see faked at end)
				echo '<a href="'.$_SERVER['PHP_SELF'].'?course='.$_POST['context_id'].'">Student view of associated LibGuide for current course: '.$_POST['context_id'].'</a><br /><br />';
				
				//Anchor for all the options on the page
				echo '<p>Jump to: <a href="#addUser">Add Admin User</a> - <a href="#new">Add New Guide</a> - <a  href="#current">Edit Current Guide</a>';
				
				//If the API is enabled display the refresh anchor
				if ($apiAvailable == TRUE){	echo ' - <a href="#update">Force Update from API</a>';}
				
				echo '</p>';				
				
				//-----------Add new administrative users------------------------
				echo '<hr /><div style="width:100%"><div id="addUser">';
				echo '<h2>Add Administrative User</h2>';
				echo '<p>Ensure the correct CampusID is entered for new users. This tool does not check for <br /> the validity of entered CampusID\'s. All fields are required.</p>';
				echo '<form name="addUsers" action="">
					<label for="realName">Full Name:&nbsp;</label>
					<input type="text" name="realName" id="realName" />
					<label class="error" id="realName_error" style="color:#CC0000;display:none;"/>Please Enter your Full name. ie. Isaak Brock</label><br />
					<label for="campusId">Campus ID:</label>
					<input type="text" name="campusId" id="campusId" title="ie. aa00aa" required/>
					<label class="error" id="campusId_error" style="color:#CC0000;display:none;"/>Please Enter your CampusID. ie. aa00aa</label><br />					
					<input type="hidden" name="addedBy" id="addedBy" value="'.$context->info['lis_person_sourcedid'].'" /><br />
					<input type="submit" name="addUser" value="Add User" class="addUser" /></form></div>			
				';

				//Print out the current admins header
				echo '<div id="currentAdmins" style="float:left;padding-left:20px">
				<h2>Current Admins</h2>';
				
				//Hit the db for the current admin users
				$statement = 'SELECT `realName`,`campusId`,`uid` FROM `adminUsers`';
				$exec4 = $dbConnection->prepare($statement);
				$exec4->execute();
				
				$userResult = $exec4->fetchAll(PDO::FETCH_ASSOC);
				
				//Print out the current admins
				foreach($userResult as $user){
					//The X and the containing span for hiding on delete
					echo '<span id="admin-'.$user['uid'].'"><a onclick="delAdmin(\''.$user['campusId'].'\', \''.$user['uid'].'\');"><img src="images/icons/cross.png" height="16" width="16"></a>';
					//The username
					echo $user['realName'].'</span><br />';
				}
				
				//Status from deleting users
				echo '<div id="delStatus"></div>';
				
				//The div for results from user creation			
				echo '</div><div id="createStatus" style="clear:both;height:10px;"></div>';
				
				//-------------Add new lib guides/change existing links----------------------
				echo '<div style="clear:both; padding:10px 0 10px 0;"><hr /><h2>Add/Edit LibGuide Links</h2></div>';
				
				//Add new libguide information
				echo '<div id="new">';
				echo '<h3>Add New Guide</h3>';
				echo '<p>When adding a new item if it\'s a subject level item the code should be 4 characters. Class level items should be the entire 8 character code for the course.</p>';
				echo '<p>Try to use a HTTPS url\'s for the guides if possible to avoid loading issues in Isaak/Sakai. All fields are required.</p>';
				//Add new libguide form
				echo '<form name="addGuide" action="">
				<label for="subject">Subject:</label>
				<input type="text" name="subject" size="50" id="subject"/>
				<label class="error" id="subject_error" style="color:#CC0000;display:none;"/>Please Enter a Subject. ie. Accounting</label><br />					
				<label for="code">Code:&nbsp;&nbsp;&nbsp;&nbsp;</label>
				<input type="text" name="code" id="code" size="10"/>
				<label class="error" id="code_error" style="color:#CC0000;display:none;"/>Please Enter the Code. ie. ACTG or ACTG2P12</label><br />					
				<label for="link">Link:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
				<input type="text" name="link" size="80" id="link"/>
				<label class="error" id="link_error" style="color:#CC0000;display:none;"/>Please Enter the Guide URL. ie. https://www.brocku.ca</label><br />					
				<input type="hidden" name="addedBySub" id="addedBySub" value="'.$context->info['lis_person_sourcedid'].'" /><br />
				<input type="submit" name="addGuide" value="Add Guide" class="addGuide" /></form></div>			
				';
				//The New guide creation status
				echo '<div id="createStatusGuide" style="clear:both;height:10px;padding-bottom:10px;"></div>';
							
				//Grab all the existing libguide info
				$statement = 'SELECT * FROM `guideInfo`';
				$exec3 = $dbConnection->prepare($statement);
				$exec3->execute();
				$libResult = $exec3->fetchAll(PDO::FETCH_ASSOC);
				
				//Print out the table header info
				echo '<div id="current"><h3>Current Guides</h3>';
				echo '<p>If both a subject guide and a course guide have both been created (ie. ECON &amp; ECON 2P91) the course guide will be shown to students</p>';
				echo '<p style="font-style:italic">Double click any item below to edit it. You must press Enter to save.</p>';
				echo '</div><div id="deleteStatus" style="clear:both;height:25px;"></div>';
				echo '<div id="table" style="width:100%">';
				echo '<table id="fancyTable" style="width:100%;">';
				echo '<thead><tr><th>Subject</th><th>Code</th><th>URL</th><th>Del</th></tr></thead>';
				echo "<tbody>";	
				
				//Print out each guides information
				foreach($libResult as $aGuide){
					echo '<tr id="guide-'.$aGuide['cid'].'"><td>'.$aGuide['Name'].'</td><td>'.$aGuide['Code'].'</td><td>'.$aGuide['URL'].'</td><td><a onclick="checkDel(\''.$aGuide['cid'].'\', \''.$aGuide['Name'].'\');"><img src="images/icons/cross.png" height="16" width="16"></a></td></tr>';	
				}
				echo '</tbody></table>';		
				//The writable config
				echo'<script type="text/javascript" charset="utf-8">
				$(document).ready(function() {
						$(\'#fancyTable\').dataTable({  
						"iDisplayLength" : 25, "aoColumns": [{sName:"Name", "asSorting": [ "asc" ]}, {sName:"Code", sWidth:"30px"}, {sName:"URL"}, {sName:"Delete"}], sReadOnlyCellClass: "read_only"})
							.makeEditable({
								sUpdateURL: "update.php"				
							});
					} );
				</script>';
				echo '</div>';
				
				//---------------LibGuide API Integration -------------//
				//If the API integration is enabled in info.php
				if($apiAvailable == TRUE){
					//Force manual refresh from the cronAdd.php LibGuide integration
					echo '<div id="update"><hr /><h2>LibGuide API Integration</h2>';	
					echo '<p>This tool can be populated manually above and/or populated automatically using the LibGuide API listing of available Library Research Guides.</p>';
					echo '<p>Populating this tool from the LigGuide API usually happens automatically.</p>';
					echo 'If you have recently created a new Research Guide and it does not appear above click below to force a refresh. Please be patient. The process can take a few minutes.';
					echo '<br /><br /><div id="apiStatus"><button onclick="refreshAPI()" id="refreshButton"> Refresh the Guide Listings Manually</button></div>';				
					echo '</div>';			
				}
			}
			//No session - No access
			else{
				echo "No valid session detected. Please ensure you enter the administration area via Isaak/Sakai";
			}
		}
					
		//----------------------Forward to Lib Guide----------------------------//
		//So they're not an admin - Send them to the Guide
		else{
			//Close the head and open the body
			echo '</head><body>';
		
			//Grab the course name from the lti object
			if(isset($context->info['context_id'])){
				$title = $context->info['context_id'];
			}
			//Or the get variable
			else{
				$title = $_GET['course'];
			}
			
			//****Check if the 4 & 8 digit course code exists****//
			//Check if there is a dash (signifying multiple courses) and grab the four digit code
			$dashLoc = strrpos($title, "-");
			//If it does have a dash add 1 to start at the next character
			if ($dashLoc !== 0){ $dashLoc++;}
			//Substring out the course name and add a space
			$fourCode = substr($title, 0, 4);
			$eightCode = $fourCode.substr($title, ($dashLoc+4), 4);			
			
			//MYSQL Query and location push	for dept code	
			$statement = 'SELECT `url` FROM `guideInfo` WHERE `Code` = ?';
			$exec2 = $dbConnection->prepare($statement);
			$exec2->execute(array($fourCode));
			//Grab the subject guide
			$urlResult = $exec2->fetch(PDO::FETCH_ASSOC);
			
			//MYSQL Query and location push	for course code
			$statement = 'SELECT `url` FROM `guideInfo` WHERE `Code` = ?';
			$exec2 = $dbConnection->prepare($statement);
			$exec2->execute(array($eightCode));
			$courseResult = $exec2->fetch(PDO::FETCH_ASSOC);
			//If there is a code guide overwrite the subject guide set above
			if(isset($courseResult['url'])){
				$urlResult = $courseResult;
			}		
			
			//If there is a location send them there
			if(isset($urlResult['url'])){		
				//If it's an HTTPS url push em there
				if(strpos($urlResult['url'], "https://") !== FALSE){
					//echo "FOUND: ".$urlResult['url'];
					header('Location: '.$urlResult['url'].'');
				}
				//Can't push them there - gogo mixed content warnings
				else{
					//The frame text for the user showing the link
					echo '<div id="links">';
					echo '<p>The Library Research Guide for this course can be found at: <a href="#"  onClick="window.open(\''.$urlResult['url'].'\')">'.$urlResult['url'].'</a></p>';
					echo '<p>Please click the link above if it does not open automatically.</p>';
					echo '<br /><p>If the link above does not resolve or you believe it to be incorrect please email <a href="mailto:'.$supportEmail.'" target="_blank">'.$supportEmail.'</a>.</div>';
					
					//Popup or warn user it was blocked from http://davidwalsh.name/popup-block-javascript
					echo "<script type='text/javascript'>
					var windowName = 'LibGuide'; 
					var popUp = window.open('".$urlResult['url']."', windowName, 'width=1280, height=720, left=24, top=24, scrollbars, resizable');
					if (popUp == null || typeof(popUp)=='undefined') { 	
						alert('Pop-up blocked. Please disable your pop-up blocker and refresh the tool.'); 
					} 
					else { 	
						popUp.focus();
					}
					</script>";				
				}
			}
			//Else sorry no url stored, forward to default libguide page
			else{
				echo "NOT FOUND";
				header('Location: '.$defaultGuide.'');
			}
			//echo "<br />".$urlResult['url']." - URL";
		}
	}
else{
	echo 'No valid session. Please refresh the page and try again. If you continue to receive this message please contact <a href="mailto:'.$supportEmail.'?Subject=LibGuide in Sakai" target="_top">'.$supportEmail.'</a>';
}

//End the HTML
echo '</body></html>';
?>
