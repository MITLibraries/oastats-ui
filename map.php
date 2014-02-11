<link rel="stylesheet" href="styles/map.css">
<?php

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

if(isset($_GET["d"])) {
  $scope = "d";
  $reqD = urldecode($_GET["d"]);
  $arrMatch = array('$match' => array('dlc'=>$reqD) );
  array_push($arrQuery,$arrMatch);
} elseif (isset($_GET["a"])) {
  $scope = "a";
  $reqA = urldecode($_GET["a"]);
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
} else {
  $scope = "All records";
  $arrCriteria = array('_id'=>'Overall');
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

// now push the data into the format needed
$datatable = '<table class="mapdata"><thead><tr><th scope="col">Country</th><th scope="col">Downloads</th></tr></thead><tbody>';
$dataset = array();
$lo = 999999999999;
$hi = 0;
foreach($tempset as $key => $val) {
  if ( $val > $hi ) { $hi = $val; }
  if ( $val < $lo ) { $lo = $val; }

  $datatable .= '<tr><td>'.$key.'</td><td>'.$val.'</td></tr>';
  $dataItem = array();
  $dataItem['code'] = (int) $key;
  $dataItem['downloads'] = (int) $val;
  array_push($dataset,$dataItem);
}
$datatable .= '</tbody></table>';


?>
<div class="export">
  <a>CSV</a>
  <a>PDF</a>
  <a>PNG</a>
</div>

<div id="map"></div>
<div id="legend">Downloads: <span class="value"></span></div>
<script>

var mapdata = <?php echo json_encode($dataset); ?>;

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    console.log(out);

    // or, if you wanted to avoid alerts...
/*
    var pre = document.createElement('pre');
    pre.innerHTML = out;
    document.body.appendChild(pre)
*/
}

var width = 900,
    height = 450;

var color = d3.scale.category10();

var projection = d3.geo.equirectangular()
    .scale(143)
    .translate([width / 2, height / 2])
    .precision(.1);

var downloadScale = d3.scale.linear()
  .domain([<?php echo $lo; ?>,<?php echo $hi; ?>])
  .range([1,5]);

var path = d3.geo.path()
    .projection(projection);

var graticule = d3.geo.graticule();

var svg = d3.select("#map").append("svg")
    .attr("width", width)
    .attr("height", height);

svg.append("defs").append("path")
    .datum({type: "Sphere"})
    .attr("id", "sphere")
    .attr("d", path);

svg.append("use")
    .attr("class", "stroke")
    .attr("xlink:href", "#sphere");

svg.append("use")
    .attr("class", "fill")
    .attr("xlink:href", "#sphere");

d3.json("data/world-50m.json", function(error, world) {
  var countries = topojson.feature(world, world.objects.countries).features,
      neighbors = topojson.neighbors(world.objects.countries.geometries);

  svg.insert("g")
    .attr("class", "base");

  svg.selectAll(".country")
      .data(countries)
    .enter().insert("path", ".graticule")
      .attr("class", "country")
      .attr("d", path)
      .attr("name", "foo")
      .attr("class", function(d, i) { 
        for(j=0;j<mapdata.length;j++){
          if(mapdata[j]["code"]===d.id) {
            return "country dl_"+Math.floor(downloadScale(mapdata[j]["downloads"]))+' n'+mapdata[j]["downloads"];
          }
        }
        return "dnull d"+d.id
      } )
      .attr("data-value", function(d, i) {
        for(j=0;j<mapdata.length;j++){
          if(mapdata[j]["code"]===d.id) {
            return mapdata[j]["downloads"];
          }
        }
        return 0;
      } );

  svg.insert("g")
    .attr("class", "focus");

  $(".country")
    .on('mouseenter', function(e) {
      $("g.focus").append(this);
      // $(this).attr('data-state','focus');
      // update legend
      var value = $(this).attr('data-value');
      $("#legend .value").text(value);
    })
    .on('mouseleave', function(e) {
      $(this).attr('data-state','');
      $("g.base").append(this);
      // clear legend
      $("#legend .value").text('');
    });

});

d3.select(self.frameElement).style("height", height + "px");

  $(document).ready(function() {
    $( "table.mapdata" ).dataTable({
      "bFilter": false,
      "bLengthChange": false,
      "bInfo": false,
      "sPaginationType": "full_numbers"
    });
  });

</script>
<?php 
/*
  echo '<p>From '.$lo.' to '.$hi.'</p>';
  echo '<p>'.$scope.'</p>';
*/
  echo $datatable; 
?>
<?php require_once('includes/include_mongo_disconnect.php'); ?>