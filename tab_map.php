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
$intLimit = 0;

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
  $arrCriteria = array('type' => 'author','_id.mitid'=>$_SESSION["mitid"]);
  $arrMatch = array('$match' => array('author'=>$reqA) );
  array_push($arrQuery,$arrMatch);
  $intLimit = 1;
} else {
}

if(isset($_GET["filter"])) {
  $scope = "Filtered Records";
  $reqFilter = $_GET["filter"];
  $arrFilter = array();
  if(isset($_GET["a"])) {
    $strKey = "_id";
  } else {
    $strKey = "_id.display";
  }
  foreach($reqFilter as $term) {
    array_push($arrFilter,array($strKey=>$term));
  }
  $arrCriteria = array('$or'=>$arrFilter);
  $intLimit = 0;
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

/* Debugging query structure 
echo '<h2>Criteria</h2>';
print_r($arrCriteria);
echo '<h2>Projection</h2>';
print_r($arrProjection);
*/

$cursor = $summaries->find($arrCriteria,$arrProjection)->limit($intLimit);

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
  // call out unplottable traffic to treat separately
  $intUnplottable = 0;
  if ($code=="XXX") {
    $intUnplottable = $tempset[$code];
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
  $dblQuintMin = intval( exp( log($lo) + ($i * $dblQuintSize) ) );
  if($i>0){$dblQuintMin++;}
  $dblQuintMax = intval( exp( log($lo) + ( ($i + 1) * $dblQuintSize) ) );
  array_push($arrBinLabels,$dblQuintMin.' - '.$dblQuintMax);
}

// parse dataset, sorting records into quintiles
foreach($dataset as $key => $val) {
  $intQuintile = intval( ( (log($val['downloads']) - log($lo)) / (log($hi) - log($lo)) ) * $intBins );
  if($intQuintile == $intBins) { $intQuintile--; }
  $val['fillKey'] = $arrBinLabels[$intQuintile];
  $dataset[$key] = $val;
}

?>
<div id="map" style="position: relative; width: 100%;"></div>
<p class="unmappable"><?php echo $intUnplottable; ?> downloads could not be placed onto a map.</p>
<p>Map shows cumulative data from October 1, 2010.</p>
<?php 
  echo $datatable; 
?>
<script>
  // http://stackoverflow.com/questions/21536984/javascript-format-whole-numbers-using-tolocalestring
  var formatNumber = function(x) {
    // from http://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript
    // Using this instead of .toLocaleString() because of Safari and mobile problems
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  };

  var width = $("#map").width();
  var height = (width * 9 / 16) + 30;

  $("#map").height(height);

  var mapdata = <?php echo json_encode($dataset); ?>;

  $.getJSON( "data/countries.json", function( data ) {
  });

  var map = new Datamap({
    element: document.getElementById('map'),
    geographyConfig: {
      dataUrl: '/data/world-50m.topo.json',
      popupTemplate: function(geography, data) {
        return '<div class="hoverinfo"><strong>' + geography.properties.name + '<br>' + formatNumber(data.downloads) + '</strong></div>';
      },
      highlightFillColor: '#9E8E4D',
      highlightBorderColor: '#907D33',
      highlightBorderWidth: 1
    },
    scope: 'world-50m',
    fills: {
      defaultFill: "#e6e6e6",
      "<?php echo $arrBinLabels[0]; ?>": "rgb(193,207,230)",
      "<?php echo $arrBinLabels[1]; ?>": "rgb(143,165,196)",
      "<?php echo $arrBinLabels[2]; ?>": "rgb(96,124,166)",
      "<?php echo $arrBinLabels[3]; ?>": "rgb(50,85,135)",
      "<?php echo $arrBinLabels[4]; ?>": "rgb(8,46,102)",
    },
    data: mapdata,
    setProjection: function(element) {
      var projection = d3.geo.equirectangular()
        .center([0, 0])
        .scale(element.offsetWidth / 6.27)
        .translate([element.offsetWidth / 2, element.offsetHeight / 2]);

       var path = d3.geo.path().projection(projection);
       return {path: path, projection: projection};
    }
  });

  map.legend({
    defaultFillName: "0",
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

  // Map Title
  var svgmap = d3.select('#map').select('svg');
  svgmap.append('g').append('text')
    .text('Geographic Distribution of Article Downloads')
    .attr('x', width / 2)
    .attr('y', 28)
    .attr('text-anchor', 'middle')
    .attr('class', 'title');

  $(document).ready(function() {
    var dt = $( "table.mapdata" ).dataTable({
      "aaSorting" : [[1, "desc"]],
      "bFilter": false,
      "bLengthChange": false,
      "bInfo": false,
      "sPaginationType": "full_numbers",
      "iDisplayLength": 25,
      "fnDrawCallback": function() {
        var pagination = this.attr('id')+'_paginate';
        if(this.fnSettings().fnRecordsDisplay()<=25){
          $("#"+pagination).hide();
        } else {
          $("#"+pagination).show();
        }
      }      
    });

    var toggle = $(".paging_full_numbers").append('<a class="showall paginate_button">Show All</a>');

    $(".showall").click(function() {
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