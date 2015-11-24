<?php
//mbrousseau - CPI - Created June 2014 - Updated July 2014
//The php processes from our ajax (both ajax.js and datatables edit)

<<<<<<< HEAD
=======
error_reporting(E_ALL);
ini_set('display_errors', 1);	

>>>>>>> Added default IV for mcrypt to remove warning
//Start the session
session_start();

//Bring in the DB credentials and other info
require_once("info.php");

//Make sure the admin user has a time session set
<<<<<<< HEAD
$timeUnHash = mcrypt_decrypt (MCRYPT_RIJNDAEL_128, $timeKey, $_SESSION['secure'], MCRYPT_MODE_CBC);
=======
$timeUnHash = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $timeKey, $_SESSION['secure'], MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
>>>>>>> Added default IV for mcrypt to remove warning

//Make sure the ajax request isn't over an hour old
if (time() <= ($timeUnHash+3600)){

	//PDO to the database
	$dbConnection = new PDO('mysql:dbname='.$database.';host='.$host.';charset=utf8', $username, $password);
	$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//Make sure it's an ajax request - assuming they don't fake it
	if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
		//Check if the refer is on our domain - assuming they don't fake it
		if(@isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']==$hostUrl){

			//If we're adding a new admin user
			if($_POST['action']=="adduser"){
			
				//Strip any @ that's on it (ie email)
				$atSymbol = strpos($_POST['campusId'], "@");
				
				//If they tried to add an email strip the @ part
				if ($atSymbol !== FALSE){
					//Strip the email part
					$_POST['campusId'] = substr($_POST['campusId'], 0, $atSymbol);
				}
								
				//Check if that account already exists
				$statment = 'SELECT `campusId` FROM `adminUsers` WHERE `campusId` = ?';
				$exec = $dbConnection->prepare($statment);
				$exec->execute(array($_POST['campusId']));
				
				//Grab the results to see if that user is in the db
				$userLookup = $exec->fetch(PDO::FETCH_ASSOC);
				
				//Let's make sure it's not a duplicate
				if($userLookup['campusId'] == $_POST['campusId']){
					echo "duplicate";
				}
				//Otherwise we're good to add the user
				else{
					//Insert them into the db
					$statement = 'INSERT INTO `adminUsers`(`realName`, `campusId`, `addTime`, `addedBy`) VALUES (?,?,?,?)';
					$exec = $dbConnection->prepare($statement);
					$exec->execute(array($_POST['realName'],$_POST['campusId'],time(),$_POST['addedBy']));

					$uid = $dbConnection->lastInsertId();
					
					//Add the x and the action here for removal
					echo '<span id="admin-'.$uid.'"><a onclick="delAdmin(\''.$_POST['campusId'].'\', \''.$uid.'\');"><img src="/library/image/silk/cross.png" height="16" width="16"></a>'.$_POST['realName'].'</span>';
				}
			}
			//If we're deleting an admin user
			elseif($_POST['action']=="deluser"){
				
				//Delete the specified user from the DB
				$statement = 'DELETE FROM `adminUsers` WHERE `campusId` = ? AND `uid` = ?';
				$exec = $dbConnection->prepare($statement);
				$exec->execute(array($_POST['campusID'],$_POST['uid']));
				
				//Check to see if we actually deleted anything
				$remove = $exec->rowCount();
				
				//We deleted something. Let's assume it was the user we wanted :)
				if($remove >= 1){
					//Tell the user about the good news
					echo '<i>'.$_POST['campusID']."</i> removed from Administrators";
				}
			}
			//If we're adding a new guide
			elseif($_POST['action']=="addguide"){
				//Make sure the url has http://
				if(strpos($_POST['link'], "http") === FALSE){
					$_POST['link'] = "http://".$_POST['link'];
				}

				//Put the new guide into the database
				$statement = 'INSERT INTO `guideInfo`(`Name`, `Code`, `URL`, `addedBy`) VALUES (?,?,?,?)';
				$exec = $dbConnection->prepare($statement);
				$exec->execute(array($_POST['subject'],$_POST['code'],$_POST['link'],$_POST['addedBySub']));

				//Print out the added item
				echo $_POST['subject'];			
			}
			//If we're changing guide information
			elseif(isset($_REQUEST['value'])){
				
				//Get the cid of the guide from the id of the item being modified
				$guideCid = substr($_REQUEST['id'], 6);

				//Update the info in the DB
				$statement = 'UPDATE `guideInfo` SET `'.$_REQUEST['columnName'].'`=?,`addedBy`=? WHERE `cid`=?';
				$exec = $dbConnection->prepare($statement);
				$exec->execute(array($_REQUEST['value'],$_POST['addedBySub'], $guideCid));
				
				//Print out the updated value
				echo $_REQUEST['value'];
			}
			//If we're removing a guide
			elseif($_POST['action']=="deleteGuide"){
			
				//Put the new guide into the database
				$statement = 'DELETE FROM `guideInfo` WHERE `cid` = ? AND `Name` = ?';
				$exec = $dbConnection->prepare($statement);
				//Print out the success
				echo ($exec->execute(array($_POST['cid'],$_POST['guideName'])));
				//echo ($exec->execute(array(0,0)));
			}
			//They hit this page with no info
			else echo 'An error(1) has occurred.  Please contact '.$supportEmail.'';
		}	
	}
	//Something is going on - no access
	else echo 'An error(2) has occurred. Please contact '.$supportEmail.'';
}
else echo 'An error(3) has occurred. Please contact '.$supportEmail.'';
<<<<<<< HEAD
?>
=======
?>
>>>>>>> Added default IV for mcrypt to remove warning
