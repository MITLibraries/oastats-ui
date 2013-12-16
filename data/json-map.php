<?php
// connect to Mongo
require_once('../includes/include_mongo_connect.php');

/*
db.requests.aggregate(
    [
        { 
            $group : { _id : "$country" , downloads: { $sum : 1 } }
        }
    ]
)
*/

// Query builder
$arrQuery = array();

if(isset($_GET["d"])) {
  
  $reqD = urldecode($_GET["d"]);
  $arrMatch = array('$match' => array('dlc'=>$reqD) );
  array_push($arrQuery,$arrMatch);

} elseif (isset($_GET["a"])) {

  $reqA = urldecode($_GET["a"]);
  $arrMatch = array('$match' => array('author'=>$reqA) );
  array_push($arrQuery,$arrMatch);

} else {

}

$arrGroup = array('$group' => array(
    '_id'=>'$country',
    'downloads'=>array('$sum'=>1),
    )
  );
array_push($arrQuery,$arrGroup);

$cursor = $collection->aggregate($arrQuery);
/*  array('$group' => array(
    '_id'=>'$country',
    'downloads'=>array('$sum'=>1),
    )
  )
);
*/

// Augment resultset - calculate hi/low, and add ISO_3166-1 country codes
$lo = 99999999;
$hi = 0;
$i = 0;
foreach($cursor["result"] as $document) {
  if ( $document["downloads"] > $hi ) { $hi = $document["downloads"]; }
  if ( $document["downloads"] < $lo ) { $lo = $document["downloads"]; }
  switch($document["_id"]) {
    case "CA":
      $cursor["result"][$i]["code"] = 124;
      break;
    case "DE":
      $cursor["result"][$i]["code"] = 276;
      break;
    case "ES":
      $cursor["result"][$i]["code"] = 724;
      break;
    case "FI":
      $cursor["result"][$i]["code"] = 246;
      break;
    case "FR":
      $cursor["result"][$i]["code"] = 250;
      break;
    case "GB":
      $cursor["result"][$i]["code"] = 826;
      break;
    case "IT":
      $cursor["result"][$i]["code"] = 380;
      break;
    case "MX":
      $cursor["result"][$i]["code"] = 484;
      break;
    case "NO":
      $cursor["result"][$i]["code"] = 578;
      break;
    case "SE":
      $cursor["result"][$i]["code"] = 752;
      break;
    case "US":
      $cursor["result"][$i]["code"] = 840;
      break;
  }
  $i++;
}

echo json_encode($cursor["result"]);

// disconnect from Mongo
require_once('../includes/include_mongo_disconnect.php');
?>