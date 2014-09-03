<?php
//mbrousseau - CPI - Created June 2014 - Updated July 2014

//The DB Credentials for LibGuides
//The account should have at very least INSERT, UPDATE, DELETE and SELECT access
$username = "";
$password = "";
$host = "";
$database = "";

//The LTI Credentials for index.php
//You need to set the LTI Key and LTI Secret (no longer than 32)
$ltiKey = "YourKeyHere";
$ltiSecret = "YourSecretHere";

$lti_auth = array('key' => $ltiKey, 'secret' => $ltiSecret);

//The secret we're using for the time hashing of ajax requests
//Should be 12 characters long
$timeKey = 'Imtwelvechar';

//The hosting server url to help validate ajax query destination
$hostUrl = 'https://www.acoolwebsite.com/index.php';

//The default location to send users when there is no specified guide
$defaultGuide = 'https://www.adefaultwebsite.com';

//API Integrations for LibGuide v.1
//If the LibGuide API should be available for use in the Admin panel for manual refresh
$apiAvailable = TRUE;

//URL to the bulleted list API listing for LibGuides
$apiURL = "http://api.libguides.com/your_api_link_here";

//Use LibGuide api V1 or V2. Set to 1 or 2
$apiVersion = 2;

$rawAPI = file_get_contents($apiURL);

//Support Email shown to users in the tool
$supportEmail = "support@you.com";
?>
