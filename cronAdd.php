<?php
//mbrousseau - CPI - Created July 2014 - Updated July 2014

//Pulls the list of guides from the LibGuide API and enters the data as best as possible into the LibGuide forwarder
//I have no output. Put me in the crontab.

//Our log for debugging
$log = "Time started: ".time(). ".<br />";

//Bring in our info file
require("info.php");

//A really fragile way of grabbing each link 
$arrayAPI = explode('<BR>', $rawAPI);

//For each link lets do some processing
foreach ($arrayAPI as $tag){
	
	//Parsing the a tag for it's components
	$url = getHref($tag);
	$subject = getSubject($tag);
	$code = getCode($url);
	
	//Make sure everything has parsed properly and there is a 4 or 8 length code
	if($url != FALSE && $subject != FALSE && $code != FALSE){
		//Check to make sure the URL actually resolves
		if (check404($url) != FALSE){
			//Add it to the listing of guides
			$result = addGuide($url, $subject, $code);
			
			//Debugging - Don't print this with cron
			if ($result == TRUE){
				//All's well
				$log .= $code." guide added successfully.<br />";
			}
			//Guide was not added successfully. 
			else{ $log .= "Unable to Add Guide. CODE = ".$code.", ERROR: ".$result.".<br />"; }
		}
		else{ $log .= "The URL came back with a 404. CODE = ".$code.", URL = ".$url.".<br />"; }
	}
	else{ $log .= "Tag info came back false. CODE = ".$code.", SUBJECT = ".$subject.", URL = ".$url.".<br />";}
}

//If it was a manual refresh let them know it's done.
if($_POST['refresh'] == "manual"){
	echo "done";
}

//Log output for debugging
$log .= "Time finished: ".time(). ".<br />";
//echo $log;

//Takes an <a> tag and rips out the URL
function getHref($tag){

	//Find the first quote and the get ? to grab the actual url from the a href
	$firstQuote = strpos($tag, '"');
	$firstQM = strpos($tag, '?');
	
	//Strip it down to just the link using previous strpos
	$url = substr($tag, ($firstQuote+1), (($firstQM-$firstQuote)-1));
	
	//Make sure it's more than just http:// in there
	if (strlen($url) > 7){		
		//Send back the url
		return $url;	
	}
	//Likely not a real url. Abort.
	else { return FALSE; }
		
}

//Takes a URL and extracts the subject and/or course number
function getCode($url){
	
	//Parse the url so we can get the path
	$urlInfo = parse_url($url);
	//Get rid of that starting slash
	$urlInfo['path'] = str_replace("/", "", $urlInfo['path']);
	
	//If it's a subject code
	if (strlen($urlInfo['path']) == 4){
		return $urlInfo['path'];
	}
	//If it's a course code
	elseif (strlen($urlInfo['path']) == 8){
		return $urlInfo['path'];
	}
	//Not one of those. Abort.
	else{ return FALSE; }
}

//Take the tag and pull out the subject description
function getSubject($tag){
	$firstPointy = strpos($tag, ">");
	$endA = strpos($tag, "</a>");
	
	//Grab just the subject text
	$subj = substr($tag, ($firstPointy+1), (($endA-$firstPointy)-1));
	
	//Return the subject text
	if (isset($subj)){	return $subj; }

	//It didn't get set for reasons. Abort.
	else { return FALSE; }
}

//Make sure the url actually resolves
function check404($url){
	//Make sure the link actually resolves
	$handle = curl_init($url);
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

	//Get the HTML or whatever is linked in $url
	$response = curl_exec($handle);

	//Check for 404 (file not found)
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	if($httpCode == 404) {
		return FALSE;
	}
	//Otherwise send the url back
	else {
		return $url;
	}
}

//Actually add the libguide checking to make sure it doesn't already exist
function addGuide($url, $subject, $code){

	//Bring in the DB credentials
	require("info.php");

	//PDO to the database
	$dbConnection = new PDO('mysql:dbname='.$database.';host='.$host.';charset=utf8', $username, $password);
	$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//Update old records if subject is the same or insert a new record
	$statement = 'INSERT INTO `guideInfo` (`Name`, `Code`, `URL`, `addedBy`) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE `Name` = ?, `addedBy` = ?, `URL` = ?';
	$exec = $dbConnection->prepare($statement);
	
	//If the record was updated/inserted
	if ($exec->execute(array($subject, $code, $url, "PHP", $subject, "PHP", $url))){
		return TRUE;
	}
	//Didn't work return the PDO error
	else { return $exec->errorCode(); }
}

//A better way of grabbing each link;
//Use the DOM to grab the a's and their href's
//PHP 5.3.6+
/*
$dom = new DOMDocument;
$dom->loadHTML($rawAPI);
$links = $list->getElementsByTagName('a');
foreach ($links as $link) {
	$href = $link->getAttribute('href');
	echo $href."<br />";
}*/

?>