<link rel="stylesheet" href="styles/map.css">
<link rel="stylesheet" href="styles/jquery.dataTables.css">
<script src="scripts/jquery.dataTables.min.js" charset="utf-8"></script>
<?php

require_once('includes/salt.php'); 

session_start();

// connect to Mongo
require_once('includes/include_mongo_connect.php');

require_once('includes/query_builder.php');

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

echo '<h2>Criteria</h2>';
print_r($arrCriteria);
echo '<h2>Projection</h2>';
print_r($arrProjection);

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
  // $dataItem['fillKey'] = "q0";
  $dataItem['downloads'] = (int) $downloads;
  $dataset[$code] = $dataItem;
}
$datatable .= '</tbody></table>';

if($lo==0){$lo=1;}
// net to set custom quintile labels
$intBins = 5;
$arrBinLabels = array();
$dblLogRange = log($hi) - log($lo);
$dblQuintSize = $dblLogRange / $intBins;
for($i=0;$i<$intBins;$i++) {
  $dblQuintMin = intval(exp(log($lo) + ($i * $dblQuintSize)));
  $dblQuintMax = intval(exp(log($lo) + (($i + 1) * $dblQuintSize)));
  array_push($arrBinLabels,$dblQuintMin.' - '.$dblQuintMax);
}

// parse dataset, sorting records into quintiles
foreach($dataset as $key => $val) {
  if($val['downloads'] == 0) { 
    $intVal = 1;
  } else {
    $intVal = $val['downloads'];
  }
  // $intQuintile = intval( ( log($intVal) / log($hi) ) * 4 );
  $intQuintile = intval( ( log($intVal - $lo) / log($hi - $lo) ) * $intBins );
  if($intQuintile == $intBins) { $intQuintile--; }

  // $val['fillKey'] = "q".$intQuintile;
  $val['fillKey'] = $arrBinLabels[$intQuintile];
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
      "<?php echo $arrBinLabels[0]; ?>": "rgb(173,186,206)",
      "<?php echo $arrBinLabels[1]; ?>": "rgb(132,152,181)",
      "<?php echo $arrBinLabels[2]; ?>": "rgb(90,117,156)",
      "<?php echo $arrBinLabels[3]; ?>": "rgb(49,83,132)",
      "<?php echo $arrBinLabels[4]; ?>": "rgb(8,48,107)",
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
    var dt = $( "table.mapdata" ).dataTable({
      "bFilter": false,
      "bLengthChange": false,
      "bInfo": false,
      "sPaginationType": "full_numbers",
      "iDisplayLength": 25
    });

    var toggle = $(".paging_full_numbers").append('<a class="showall paginate_button">Show All</a>');

    $(".showall").click(function() {
      console.log("clicked");
      var dtSettings = dt.fnSettings();
      var label = $(this).html();
      if(label == "Show All") {
        dtSettings._iDisplayLength = -1;
        $(this).text("Show 25");
      } else {
        dtSettings._iDisplayLength = 25;
        $(this).text("Show All");
      }
      dt.fnDraw();
      console.log("changed");
    });

    // Set export options
    $("#exports").empty();
    $("#exports").append('<li><a data-format="csv">CSV</a></li>');
  });

</script>
<?php require_once('includes/include_mongo_disconnect.php'); ?>
<?php
// print_r($dataset);
?>