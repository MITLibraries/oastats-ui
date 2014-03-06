<?php 

date_default_timezone_set('America/New_York');

require_once('../includes/salt.php'); 

session_start();

// connect to Mongo
require_once('../includes/include_mongo_connect.php');

$boolDebug = false;
if(isset($_GET["debug"])){
  error_reporting(E_ALL);
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);

  $boolDebug = true;
}

/*
The end state of the data is this:
{
  "dates" : [1230786000000,1230872400000,1230958800000,1231045200000,1231131600000,1231218000000,1231304400000,1231390800000,1231477200000,1231563600000],
  "dataNamesRaw" : ["Computer Science","DUSP","Lincoln Lab"],
  "dataRaw" : [
    [1,2,3,4,5,6,7,8,9,10],
    [2,4,6,8,10,12,14,16,18,20],
    [3,3,4,4,5,5,6,6,7,7]
  ]
}

The general Mongo query is:
db.summaries.find({'_id':'Overall'},{'_id':1,'dates':1})
*/

// Get querystring
// This includes determining what level of data is being queried, and any filters being applied
$arrCriteria = array('_id'=>'Overall');
$arrProjection = array(
  '_id'=>1,
  'dates'=>1
);

if(isset($_GET["a"])) {
  // if this is coming from an author view, pull that author
  $arrCriteria = array('type' => 'author','_id.mitid'=>$salt.$_SESSION["hash"]);
}

if(isset($_GET["filter"])) {
  $reqFilter = $_GET["filter"];
  $arrFilter = array();
  // iterate over reqFilter, padding out values
  foreach($reqFilter as $term) {
    array_push($arrFilter,array('_id'=>$term));
  }
  $arrCriteria = array( '$or' => $arrFilter);
}

debugData('Criteria',$arrCriteria,$boolDebug);
debugData('Projection',$arrProjection,$boolDebug);

$cursor = $summaries->find($arrCriteria,$arrProjection);

/* 
Sample returned record
{
  '_id' : 'DLC Name',
  'dates' : [
    {
      'date' : '2009-01-01',
      'downloads' : 2
    },
    {
      'date' : '2009-01-04',
      'downloads' : 2
    }
  ]
}

OR 

{
  '_id' : {
    'mitid' : 'HASH',
    'name' : 'Doe, John'
  }
  'dates' : [
    {
      'date' : '2009-01-01',
      'downloads' : 2
    },
    {
      'date' : '2009-01-04',
      'downloads' : 2
    }
  ]
}
*/

// init three arrays
$arrDates = array();
$arrDataNamesRaw = array();
$arrDataRaw = array();

/*
Combine data
The intermediate set of data is arrSubData, which has a shape of:
{
  date: date
  set1: number
  set2: number
}
(as many sets as there are items in the filter)
*/
$arrSubData = array();
foreach($cursor as $document) {

  debugData('Document',$document,$boolDebug);

  array_push($arrDataNamesRaw,$document["_id"]);

  foreach($document["dates"] as $date) {

    debugData('Date',$date,$boolDebug);

    // check if we've seen this date already
    if(isset($_GET["a"]) && !isset($_GET["filter"])) {
      $arrSubData[$date["date"]][$document["_id"]["mitid"]] = $date["downloads"];
    } else {
      $arrSubData[$date["date"]][$document["_id"]] = $date["downloads"];
    }
  }
}
ksort($arrSubData);
/* 
Build final data
This builds the final data format
*/
$arrCounters = array();
foreach($arrDataNamesRaw as $record) {
  $arrCounters[$record] = 0;
}
$i = 0;
foreach($arrSubData as $key=>$val) {
  // add date to arrDates
  $tempDate = date_create_from_format('Y-m-d',$key);
  array_push($arrDates,$tempDate->format('U')*1000);
  // increment counters
  $j = 0;
  foreach($val as $subkey=>$subval) {
    $arrCounters[$subkey] += $subval;
    $j++;
  }
  $j = 0;
  foreach($arrCounters as $subkey=>$subval) {
    $arrDataRaw[$i][$j] = $arrCounters[$subkey];
    $j++;
  }
  $i++;
}

debugData('dataRaw',$arrDataRaw,$boolDebug);

function debugData($msg,$data,$boolDebug) {
  if($boolDebug) {
    echo '<h2>'.$msg.'</h2>';
    echo '<pre>';
    print_r($data);
    echo '</pre>';
  }
}

// now need to flip arrDataRaw
// from http://stackoverflow.com/questions/797251/transposing-multidimensional-arrays-in-php
function transpose($array) {
    array_unshift($array, null);
    return call_user_func_array('array_map', $array);
}
$arrDataRaw = transpose($arrDataRaw);

debugData('<hr>','',$boolDebug);

// build final data structure
$arrOutput = array("dates" => $arrDates,"dataNamesRaw" => $arrDataNamesRaw,"dataRaw" => $arrDataRaw);
echo json_encode($arrOutput);

// Disconnect from Mongo
require_once('../includes/include_mongo_disconnect.php'); 

?>