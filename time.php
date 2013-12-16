<link rel="stylesheet" href="styles/time.css">
<div id="chart"></div>
<script>

var margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = 920 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.time.format("%Y-%m-%d").parse;

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .x(function(d) { return x(d._id); })
    .y(function(d) { return y(d.downloads); });

var svg = d3.select("#chart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// to switch from daily to running counts, add/remove "-running" from php file
d3.json("data/json-time-running.php?<?php echo $_SERVER["QUERY_STRING"]; ?>", function(error, data) {
  data.forEach(function(d) {
    d._id = parseDate(d._id);
    d.downloads = +d.downloads;
  });

  x.domain(d3.extent(data, function(d) { return d._id; }));
  // y.domain(d3.extent(data, function(d) { return d.downloads; }));
  y.domain([0,d3.max(data, function(d) { return d.downloads; })]);

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Downloads");

  svg.append("path")
      .datum(data)
      .attr("class", "line")
      .attr("d", line);
});

</script>
<?php echo $_SERVER["QUERY_STRING"]; ?>