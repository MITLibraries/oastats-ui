<link rel="stylesheet" href="styles/map.css">
<link rel="stylesheet" href="styles/jquery.dataTables.css">
<script src="scripts/jquery.dataTables.min.js" charset="utf-8"></script>
<?php

require_once('includes/salt.php'); 

session_start();

// connect to Mongo
require_once('includes/include_mongo_connect.php');

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

$scope = "All records";
$arrCriteria = array('_id'=>'Overall');

if(isset($_GET["d"])) {
  $scope = "d";
  $reqD = urldecode($_GET["d"]);
  $arrMatch = array('$match' => array('dlc'=>$reqD) );
  array_push($arrQuery,$arrMatch);
} elseif (isset($_GET["a"])) {
  $scope = "a";
  $reqA = urldecode($_GET["a"]);
  $reqA = str_replace('@mit.edu','',$reqA);
  $arrCriteria = array('_id'=>$reqA);
  $arrCriteria = array('type' => 'author','_id.mitid'=>$salt.$_SESSION["hash"]);
  $arrMatch = array('$match' => array('author'=>$reqA) );
  array_push($arrQuery,$arrMatch);
} else {
}

if(isset($_GET["filter"])) {
  $scope = "Filtered Records";
  $reqFilter = $_GET["filter"];
  $arrFilter = array();
  foreach($reqFilter as $term) {
    array_push($arrFilter,array('_id'=>$term));
  }
  $arrCriteria = array('$or'=>$arrFilter);
}

/*
Sample query:
db.summaries.find({'_id':'Overall'},{'countries':1})

Sample document:
{
  "_id": "792",
  "downloads": 32
}
...which is morphed into
{
  "_id": "792",
  "downloads": 32,
  "code": 792
}
as the min/max values are calculated
*/ 
$arrProjection = array('countries'=>1);
$cursor = $summaries->find($arrCriteria,$arrProjection);

$tempset = array();
foreach($cursor as $document) {
  foreach($document["countries"] as $record) {

    // need to check for this item in dataset
    if(array_key_exists($record['country'],$tempset)) {
      $tempset[$record['country']] += (int) $record['downloads'];
    } else {
      $tempset[$record['country']] = (int) $record['downloads'];

      // array_push($dataset, $record['country'] => (int) $record['downloads'] );
    }
    /*
    if(in_array($record['country'],$dataset[$record['country']])) {
      echo '<p>Found</p>';
    } else {
      echo '<p>Not Found</p>';
    }
    echo '<hr>';
    $dataItem = array();
    $dataItem['downloads'] = (int) $record['downloads'];
    $dataItem['code'] = (int) $record['country'];
    array_push($dataset,$dataItem);
    */

  }
}

// load countries.json, merge with $tempset
$countries = file_get_contents('data/countries.json');
$countries = json_decode($countries,true);

$datatable = '<table class="mapdata"><thead><tr><th scope="col">Country</th><th scope="col">Downloads</th></tr></thead><tbody>';
$dataset = array();
$lo = 999999999999;
$hi = 1;

// we use countries.json as the authoritative list of countries, because there's no guarantee that all the countries will come back from the Mongo store
foreach($countries as $country) {
  // print_r($country);
  $code = $country["cca3"];
  if(array_key_exists($code,$tempset)){
    $downloads = $tempset[$code];
    if($downloads > $hi) { $hi = $downloads; }
    if($downloads < $lo) { $lo = $downloads; }
  } else {
    $downloads = 0;
    $lo = 0;
  }
  $datatable .= '<tr><td>'.$country["name"].'</td><td>'.$downloads.'</td></tr>';
  $dataItem = array();
  $dataItem['fillKey'] = "q0";
  $dataItem['downloads'] = (int) $downloads;
  $dataset[$code] = $dataItem;
}
$datatable .= '</tbody></table>';

// parse dataset, sorting records into quintiles
foreach($dataset as $key => $val) {
  if($val['downloads'] == 0) { 
    $intVal = 1;
  } else {
    $intVal = $val['downloads'];
  }
  $intQuintile = intval((log($intVal) / log($hi))*4)+1;
  if($intQuintile>5) {$intQuintile=5;}
  $val['fillKey'] = "q".$intQuintile;
  $dataset[$key] = $val;
}

?>
<div id="map" style="position: relative; width: 100%;"></div>
<?php 
  echo $datatable; 
?>
<script>

  var width = $("#map").width();
  var height = width * 9 / 16;

  $("#map").height(height);

  var mapdata = <?php echo json_encode($dataset); ?>;

  $.getJSON( "data/countries.json", function( data ) {
  });

  var map = new Datamap({
    element: document.getElementById('map'),
    geographyConfig: {
      popupTemplate: function(geography, data) {
        return '<div class="hoverinfo"><strong>' + geography.properties.name + '<br>' + data.downloads.toLocaleString() + '</strong></div>';
      }
    },
    fills: {
      defaultFill: "#cccccc",
      q0: "rgb(242,242,242)",
      q1: "rgb(173,186,206)",
      q2: "rgb(132,152,181)",
      q3: "rgb(90,117,156)",
      q4: "rgb(49,83,132)",
      q5: "rgb(8,48,107)",
    },
    data: mapdata
  });

  map.legend({
    defaultFillName: "No data",
    q0: "one",
    labels: {
      q0: "one",
      q1: "two",
      q2: "three",
      q3: "four",
      q4: "five",
      q5: "six,"
    },
  });

  $(document).ready(function() {
    $( "table.mapdata" ).dataTable({
      "bFilter": false,
      "bLengthChange": false,
      "bInfo": false,
      "sPaginationType": "full_numbers"
    });

    // Set export options
    $("#exports").empty();
    $("#exports").append('<li><a data-format="csv">CSV</a></li>').append('<li><a data-format="pdf">PDF</a></li>').append('<li><a data-format="png">PNG</a></li>');    
  });

</script>
<?php require_once('includes/include_mongo_disconnect.php'); ?>
<?php
print_r($dataset);
?>