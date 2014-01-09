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
  $cursor["result"][$i]["code"] = (int) $document["_id"];
  $i++;
}

echo json_encode($cursor["result"]);

// disconnect from Mongo
require_once('../includes/include_mongo_disconnect.php');
?>