<?php

/**************************************************

January 8, 2015

Wade Guidry, University of Puget Sound, wguidry@pugetsound.edu


Script for generating an HTML / CSS / javascript-based book cover carousel using Alma Analytics, the Analytics API, and some jquery and CSS.

An example of the output of this script can currently be seen at:

http://www.pugetsound.edu/academics/academic-resources/collins-memorial-library/new-resources/recently-read-collins/

In this particular example, the script generates a carousel of books recently checked out (and already returned) at Collins Memorial Library.

If you are an Ex Libris Alma customer, and have access to Alma Analytics, a copy of the Analytics report against which this script runs can be found in the Alma Analytics Community folder, at:

	/Shared Folders/Community/Reports/University of Puget Sound/trending_titles
	
This script relies on the following additional resources:

	1. JCarousel

		jCarousel is a jQuery plugin for controlling a list of items in horizontal or vertical order.

		http://sorgalla.com/jcarousel/ AND https://github.com/jsor/jcarousel

	2. OpenLibrary

		The cover art used in this script comes from the Open Library.

		Open Library is an initiative of the Internet Archive, a 501(c)(3) non-profit.

		http://openlibrary.org

		Further information about the Open Library Covers API, including appropriate use, can be found at:

		https://openlibrary.org/dev/docs/api/covers
		
	3. CSS and JS files
	
		To obtain the specific CSS and js files in use in the implmentation example cited above, visit:
		
		http://github.com/wadeguidry/Primo-Book-Cover-Carousel
		
		Note that the repository contains static versions of jQuery and the jcarousel jQuery plugin for convenience. Newer versions of these may well be available.

In my environment, I have the script running once daily, and use Windows Server 2008 task scheduler to schedule the job.

**************************************************/


/* get the XML from the Analytics API and load into PHP var */

/* in this case, a report of recently checked out and returned titles */



/* START - prevent initial redirect on image queries sent to openlibrary.org */

$context = stream_context_create(
    array(
        'http' => array(
            'follow_location' => false
        )
    )
);

/* END - prevent initial redirect on image queries sent to openlibrary.org */


/* START - Read in the analytics report  */

$ResumptionToken = "";

while (($IsFinished != 'true') Or ($rowcount == 0)) {
	
	$report = "https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?path=/shared/University%20of%20Puget%20Sound/Reports/Wade/trending_titles&limit=1000&apikey=[your api key]";
	$xml = simplexml_load_file($report); 
	$ResumptionToken = (string) $xml->QueryResult->ResumptionToken;
	$IsFinished = (string) $xml->QueryResult->IsFinished;
		
	/* register the "rowset" namespace */
	
	$xml->registerXPathNamespace('rowset', 'urn:schemas-microsoft-com:xml-analysis:rowset');
	
	/* use xpath to get rows of interest */
	
	$result = $xml->xpath('/report/QueryResult/ResultXml/rowset:rowset/rowset:Row');
	
	/* using rowcount of 0 to repeat call until data is obtained is a low-tech way of addressing the issue that calls to the analytics API sometimes just fail;
	   Since I know for certain that there should be data in the report */
	
	$rowcount = count($result);
	}

/* unique-ify the results , since the report is on items, but what we really want is titles, and there may be item duplicates */

array_unique($result);

/* randomize the list, to keep it interesting */

shuffle($result);

/* start outputting desired results to new html file, which will replace the old file once the new file is built */

$output = 'trending_c_new.html';

/* echo $output; */

$oldfile = 'trending_c.html';

/* echo $oldfile; */

/* write the first part of the html file */

file_put_contents($output,'
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Trending at Collins Library</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="jcarousel.basic.css">
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery.jcarousel.min.js"></script>
<script type="text/javascript" src="jcarousel.basic.js"></script>
 </head>
  <body>
        <div class="wrapper">
            <div class="jcarousel-wrapper">
                <div class="jcarousel">
                    <ul class="img-list">
');

/* parse the analytics report data into the HTML */

foreach ($result as $row) {

	$call = (string) $row->Column5;
	$author = (string) $row->Column1;
	$mms = (string) $row->Column3;
	$title = (string) trim($row->Column4, " /");
	$isbns = (string) $row->Column2;
	$loc = (string) $row->Column6;
	$location = (string) $row->Column7;
	
	/* create an array of the ISBN numbers, to check each number for the availability of cover art */
	
	$isbn_array = explode (';' , $isbns);
	
	/* go through each ISBN for a title to see if cover art exists for one of them */
	
	foreach ($isbn_array as $value) { 	
		$size = getimagesize(('http://covers.openlibrary.org/b/isbn/'.$value.'-S.jpg?default=false'));
		
		if ($size !== false) {
		$isbn = trim($value);
		break;
		}
	}
	
	/* if the cover art exists, then download the large size cover from openlibrary, do a bit of cleanup, and add the title to the html file
	
	   depending on the use case, you could instead include a default book cover for titles for which a cover is not available */

	if ($size !== false) {
	
		$html = file_get_contents('http://covers.openlibrary.org/b/isbn/'.$isbn.'-L.jpg', false, $context);
		
		$url_loc = trim($http_response_header[5], "Location: ");
	
		file_put_contents($isbn.'.jpg', file_get_contents($url_loc));
		
		if (getimagesize($isbn.'.jpg') !== false) {
		
			file_put_contents($output, '<li><a href="http://primo.pugetsound.edu/primo_library/libweb/action/search.do?fn=search&ct=search&initialSearch=true&mode=Basic&tab=default_tab&indx=1&dum=true&srt=rank&vid=UPUGS&frbg=&tb=t&vl%28freeText0%29='.$mms.'&scp.scps=scope%3A%28UPUGS%29%2Cscope%3A%28NZ%29%2Cprimo_central_multiple_fe" target="_blank" title="" class="rsslink"><img src="'.$isbn.'.jpg" style="width:180px;" /><span class="text-content"><span>'.$title.'<br /><br />'.$call.'<br /><br />'.$author.'</span></span></a></li>', FILE_APPEND);
			}
	
	}
	
} 

/* finish outputting the HTML */

file_put_contents($output, '</ul> </div>

 
                <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
                <a href="#" class="jcarousel-control-next">&rsaquo;</a>
                

            </div>
        </div></body></html>', FILE_APPEND);

/* copy the new output to the file actually in use */

copy($output, $oldfile);

?>
