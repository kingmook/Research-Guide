LibGuide
========

##LTI Provider for Library Research Guide Re-direction 

Very basic LTI connection. Not checking signatures or comparing nonces as redirect content is public. 
Takes a site name via context_id and parses the name in the format CODE#### (ie. ABED4F84)
Can deal both with 4 long subject codes (ABED) and 8 long course codes (ABED4F84)


* Uses jQuery Datatables (https://datatables.net/)  to present the list of libguides to administrators.
* Uses PHP BLTI (http://developers.imsglobal.org/phpcode.html) to make an LTI connection.
* Uses Silk Icons (http://www.famfamfam.com/lab/icons/silk/) because they're great.
