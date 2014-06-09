<?php
//The php processes from our ajax (both ajax.js and datatables edit)
//You'll want to set YOURDOMAINHERE to help prevent referer forging.

//PDO to the database - modify for your DB Info
$dbConnection = new PDO('mysql:dbname=DB_NAME;host=localhost;charset=utf8', 'DB_USER', 'DB_PASSWORD');
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Make sure it's an ajax request - assuming they don't fake it
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	//Check if the refer is on our domain - assuming they don't fake it
	if(@isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']=="https://YOURDOMAINHERE"){

		//If we're adding a new user
		if($_POST['action']=="adduser"){
			//Processes the ajax processes
			echo $_POST['realName'];
			
			//Hit the database to check if they are an administrator
			$statement = 'INSERT INTO `adminUsers`(`realName`, `campusId`, `addTime`, `addedBy`) VALUES (?,?,?,?)';
			$exec = $dbConnection->prepare($statement);
			$exec->execute(array($_POST['realName'],$_POST['campusId'],time(),$_POST['addedBy']));
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
		//If we're changing guide information - - *TODO*
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
		else echo "An error has occurred. No Information was passed.";
	}	
}
//Something is going on - no access
else echo "An error has occurred.";
	
?>