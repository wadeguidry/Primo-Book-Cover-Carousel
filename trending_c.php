<?php

/*

Necessary components, and credits:

The source for the jquery and CSS techniques came from:
http://sorgalla.com/jcarousel/ (https://github.com/jsor/jcarousel)

The cover art comes from the following site:
http://openlibrary.org

*/

/* get the XML from the Analytics API and load into PHP var */

/* in this case, a report of recently checked out and returned titles */


/* function to prevent initial redirect on image query */

$context = stream_context_create(
    array(
        'http' => array(
            'follow_location' => false
        )
    )
);


/* read the analytics report in */

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
	$rowcount = count($result);
	}




/* unique-ify the results , since the report is on items, but what we really want is titles, and there may be item duplicates */

array_unique($result);

/* function to sort array by specific key, call number in this case */

/* function cmp($a, $b)
    {
        return strcmp($a->Column5, $b->Column5);
    } */
	
/* use the function */

/* usort($result, "cmp");	*/

/* randomize the list */

shuffle($result);

/* start outputting desired results to new html file, which will replace the old file */

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
	
	$isbn_array = explode (';' , $isbns);
	
	/* if a title has book cover available on openlibrary, download the image and include the book in the carousel, otherwise, do not include the book */
	
	/* go through each ISBN for a title to see if cover art exists for one of them */
	
	foreach ($isbn_array as $value) { 	
		$size = getimagesize(('http://covers.openlibrary.org/b/isbn/'.$value.'-S.jpg?default=false'));
		
		if ($size !== false) {
		$isbn = trim($value);
		break;
		}
	}
	
	/* if the cover art exists, then download the large size cover from openlibrary, do a bit of cleanup, and add the title to the html file */

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
