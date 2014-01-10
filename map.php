<link rel="stylesheet" href="styles/map.css">
<?php $hi = 100; ?>
<div id="map"></div>
<script>

var mapdata;

d3.json("data/json-map.php?<?php echo $_SERVER["QUERY_STRING"]; ?>", function(error, json) {
  if (error) return console.warn(error);
  mapdata = json;
});

var width = 900,
    height = 450;

var color = d3.scale.category10();

var projection = d3.geo.equirectangular()
    .scale(143)
    .translate([width / 2, height / 2])
    .precision(.1);

// downloadScale was here, once

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

svg.append("path")
    .datum(graticule)
    .attr("class", "graticule")
    .attr("d", path);


d3.json("data/world-50m.json", function(error, world) {
  var countries = topojson.feature(world, world.objects.countries).features,
      neighbors = topojson.neighbors(world.objects.countries.geometries);

  var downloadScale = d3.scale.linear()
    .domain([0,Math.max.apply(Math,mapdata.map(function(o){return o.downloads;}))])
    .range([0,5]);

  svg.selectAll(".country")
      .data(countries)
      .enter().insert("path", ".graticule")
      .attr("class", "country")
      .attr("d", path)
      .attr("class", function(d, i) { 
        for(j=0;j<mapdata.length;j++){
          if(mapdata[j]["code"]===d.id) {
            return "dl_"+Math.floor(downloadScale(mapdata[j]["downloads"]));
          }
        }
        return "dnull d"+d.id
      } );

  svg.insert("path", ".graticule")
      .datum(topojson.mesh(world, world.objects.countries, function(a, b) { return a !== b; }))
      .attr("class", "boundary")
      .attr("d", path);
});


d3.select(self.frameElement).style("height", height + "px");

</script>