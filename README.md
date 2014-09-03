Research Guide LTI Forwarder
========

##LTI Provider for Library Research Guide (LibGuide) Re-direction 

Takes URL input and automatically re-directs students to them when the tool is added to a site.
Takes the name of the course site and matches the department code (COSC) or course code (COSC2P01) to the setup URLS.

Users in the Instructor role will see the administration panel.
Student will be immeditaely directed to the URL specified.

Very basic LTI connection. Not checking signatures or comparing nonces as redirect content is public. 
Takes a site name via context_id and parses the name in the format CODE#### (ie. ABED4F84)
Can deal both with 4 long subject codes (ABED) and 8 long course codes (ABED4F84)

Configure the tool in info.php with your DB and support information.

Can use the LibGuide v1 or v2 bulleted list of guides API (widget in V2) to pull in library research guides automatically. 
To automate this process add cronAdd.php to your CronTab.
In addition you can allow the user to manually force an API refresh in index.php

* Uses jQuery Datatables (https://datatables.net/)  to present the list of libguides to administrators.
* Uses PHP BLTI (http://developers.imsglobal.org/phpcode.html) to make an LTI connection.
* Uses Silk Icons (http://www.famfamfam.com/lab/icons/silk/) because they're great.
