#!/usr/bin/php
<?php

/***********************************
// SET ALL THESE 10 SETUP PARAMETERS
***********************************/

//There are more details in the README.md

/*
1. Requirements
Written by a Ubuntu Linux user but could probably also be made to work under other Linux distributions or a Windows PHP environment
 - php-curl
 - access to cron or other scheduler
 - a website with auto-updating search-engine sitemap xml file(s), lastmod parameter is required


2. AMEND the path to php command line binary at the tope of the file, if yours is different
find it with
which php


3. Domain of site to be indexed
e.g.
$domain = 'example.com';
*/
$domain = '';


/*
4. List sitemap files in the $sitemapFiles array
At least one file must be listed
Typically this might be https://example.com/sitemap.xml
Either sitemapindex files or sitemap xml files are, and can be mixed
e.g.

$sitemapFiles = ['https://example.com/sitemap.xml',];

or

$sitemapFiles =
[
'https://example.com/sitemap.xml',
'https://example.com/typo3conf/events_sitemap_ssl.php',
'https://example.com/typo3conf/pdf_sitemap_ssl.xml'
];
*/
$sitemapFiles =
[
''
];


/*
5. IndexNow key for this site
Follow instructions at https://www.bing.com/indexnow/getstarted
*/
$key = '';

/*
6. Location of your key file
e.g. https://example.com/$key.txt
substitue $key with your key value, or use double-quotes so that $key value can be substituted
*/
$keyLocation = "";


/*
7. Dstination url to receive the IndexNow updates
below is the correct setting for Bing, but there are alternative options see https://www.indexnow.org/faq
*/
$urlIndexNow = "https://www.bing.com/indexnow";


//8. Enter the interval in hours that you want to have between IndexNow updates
$cronIntervalHours = 8;


/*
 9. Set up a cron job to run this script at the same intervals as set above
 e.g. if you have set 8 above then set cron to runs this script at 8 hourly intervals
33 1,9,17 * * * /home/anyuser/bin/indexnow.php
*/

/*
 10. Debugging
 For first test run, or for troubleshooting, set $debug to 1
 For production use, set debug to 0
*/
$debug = 1;


/********************************/
// No need to change anything below this line
/********************************/

$urlSet = (string)'';
$urlSitemap = (string)'';
$updatedUrl = '';
$lastmod = '';
$sitemapList=[];

// time of last cron run, minus 3 minutess to ensure no edits near last runtime are overlooked
// the three minutes may make some duplicate submissions occur, but not many
$lastRunTime = time()-(3600*$cronIntervalHours)-180;

// Review the $sitemapFiles and process according to whether they are index files or sitemap files
// for index files extract the index files contained and add them to $sitemapList
// for sitemap file add them to $sitemapList
if(count($sitemapFiles)>0){
  for($j=0;$j<count($sitemapFiles);$j++){
    $content = file_get_contents($sitemapFiles[$j]);
    $xml=simplexml_load_string($content);
    foreach ($xml->sitemap as $sitemapElement) {
      //this is a siteindex file
      //extract sitemaps to $sitemapList
      $urlSitemap = $sitemapElement->loc;
      array_push($sitemapList,$urlSitemap);
    }
    if(str_contains($content,'<urlset')) {
        //this is a sitemap file
        //add to $sitemapList
        array_push($sitemapList,$sitemapFiles[$j]);
        }
    }
}

if ($debug==1) {
  echo "\n\nSitemaps discovered\n";
  print_r($sitemapList);
}


if (count($sitemapList)==0) {
  die("\n\nNo sitemaps found.\nPlease configure at least one sitemap or sitemapindex at the top of the file.\nExiting\n\n");
  exit();
}

// Read each sitemap file and extract any URLs which have lastmod datetime later than $lastRunTime
for($i=0;$i<count($sitemapList);$i++){
    $content = file_get_contents($sitemapList[$i]);
    $xml=simplexml_load_string($content);
    foreach ($xml->url as $urlElement) {

        $updatedUrl = $urlElement->loc;
        $lastmod = $urlElement->lastmod;
        if (strtotime($lastmod) > $lastRunTime) {
            $urlSet = $urlSet."'".$updatedUrl."',";
        }
    }
}

// format the list of URLs to be submitted
$urlSet ="[".substr($urlSet,0,strlen($urlSet)-2)."']";

if($debug==1) {
  echo "\n\nURLs to submit discovered: ";
  if($urlSet=="[']"){
    echo "No recently modified URLs discovered\n";
  } else {
    echo $urlSet. "\n";
  }
}

// If any URLs due for reindexing have been identified, upload them to IndexNow
if($urlSet<>"[']"){
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $urlIndexNow);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json; charset=utf-8',"keyLocation: $keyLocation"));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $data = <<<DATA
{
  "host": "$domain",
  "key": "$key",
  "urlList": $urlSet,
}
DATA;

  if($debug==1) {
    echo "\n\nThis is the data about to be POSTed to IndexNow\n";
    echo $data."\n";
  }

  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  $resp = curl_exec($curl);
  // $resp shows error messages, useful to see this in case of errors, but blank if success
  echo "\n\nThis is the response from the search engine. A blank response means no error reported.\n";
  echo $resp;
  curl_close($curl);
}

?>

