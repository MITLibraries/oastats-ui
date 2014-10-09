<?php
if(isset($_GET["d"])) {
  $strGroup = "authors";
} elseif(isset($_GET["a"])) {
  $strGroup = "papers";
} else {
  $strGroup = "departments, labs or centers";
}
?>
<link rel="stylesheet" href="styles/time.css">
<p id="instructions">Use the "Show Only" filter at left to select items for display. "All <?php echo $strGroup; ?>" will display a single line for all values. Alternatively, you can make up to ten individual selections for display on the Timeline. Data for additional selections can be downloaded via the export tools.</p>
<div class="vis new" id="time">
  <div id="time-tooltip" class="tooltip"></div>
</div>
<script>
$(document).ready(function() {

  var width = $("#time").width();
  var height = width * 3 / 4;

  var color = ["#1792e4", "#ff4248", "#51b23b", "#ff6e00", "#9574D4", "#008751", "#ac51ad", "#044187", "#ff3467", "#e7a609"];
  var colorLight = ["#97d2ff", "#ff9298", "#a1f27b", "#ffae50", "#9574D4", "#008751", "#ac51ad", "#044187", "#ff3467", "#e7a609"];
  var timeFormat = d3.time.format('%b %d, %Y');
  var tickFormat = d3.time.format('%b %Y');

  var dataRaw = new Array;

  var graphNew = function(id, num, title) {
    var data = dataRaw.slice(0, num);
    var dataNames = dataNamesRaw.slice(0, num);

    var margin = {
      top: 50,
      bottom: 90,
      left: 70,
      right: 30
    };      

    var tooltip = id + '-tooltip';
    var tooltipElem = $('#' + tooltip);
    var tooltipSingleTemplate = "<div class='tooltip-single'>  <strong><%- date %></strong>: <%- value %></div>";
    var tooltipTitleTemplate = "<div class='tooltip-title'><%- title %></div>";
    var tooltipSectionTemplate = "<div class='tooltip-section'>  <div class='tooltip-swatch' style='background-color: <%- color %>'></div>  <div class='tooltip-label' title='<%- name %>'><%- name %></div>  <div class='tooltip-value'><%- value %></div></div>";

    var curIndex = -1;
    var single = true, multi = false;

    var findSeries = function(data, d, i) {
      for(var j = 0; j < data.length; j++) {
        if(data[j][i] === d) return j;
      }
    };

    var resetTooltip = function() {
      if (single) {
        return tooltipElem.hide();
      } else {
        updateTooltip(margin.left + 10, false, -1, '');
        return curIndex = -1;
      }
    };

    var updateTooltip = function(xVal, yVal, index, date) {
      var animObj, d, value, _i, _len, _results;
      tooltipElem.show();
      if (single) {
        animObj = {
          left: xVal,
          top: yVal ? yVal : void 0
        };
        tooltipElem.stop().animate(animObj, 50, 'linear');
      }
      tooltipElem.html('');
      if (_.isDate(date)) {
        date = timeFormat(date);
      }
      if (single) {
        tooltipElem.append(_.template(tooltipSingleTemplate, {
          value: formatNumber(data[0][index]),
          date: date
        }));
        return;
      }
      if (!date) {
        date = 'Legend';
      }
      tooltipElem.append(_.template(tooltipTitleTemplate, {
        title: date
      }));
      _results = [];
      for (_i = 0, _len = data.length; _i < _len; _i++) {
        d = data[_i];
        if (index < 0) {
          value = '';
        } else {
          value = formatNumber(d[index]);
        }
        _results.push(tooltipElem.append(_.template(tooltipSectionTemplate, {
          name: dataNames[_i],
          value: value,
          color: color[_i]
        })));
      }
      return _results;
    };

    var formatNumber = function(x) {
      // from http://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript
      // Using this instead of .toLocaleString() because of Safari and mobile problems
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    var render = function() {
      if(data.length > 1) {
        multi = true;
        // adjust right margin
        margin.right = 185;
        // 
        $('#' + tooltip).css('top', 30);
        $('#' + tooltip).css('right', 0);
        $('#' + tooltip).show();
        // calculate maxY
        maxY = 0;
        for(j=0;j<data.length;j++){
          maxY = maxY>data[j][data[j].length-1] ? maxY : data[j][data[j].length-1]
        }

      } else {
        maxY = data[0][data[0].length-1];
        $('.tooltip').addClass('single');
      }
      single = !multi;

      var minY = 0;
      var minX = dates[0], maxX = dates[dates.length - 1];

      var x = d3.time.scale().domain([minX, maxX]).range([margin.left, width - margin.right]);
      var y = d3.scale.linear().domain([minY, maxY]).range([height - margin.bottom, margin.top]);

      var xAxis = d3.svg.axis()
        .tickFormat(d3.format('d'))
        .scale(x)
        .ticks(7)
        .tickFormat(tickFormat)
        .tickSize(10);

      var yAxis = d3.svg.axis()
        .scale(y)
        .orient('left')
        .ticks(10)
        .tickSize(margin.right + margin.left - width);

      var line = d3.svg.line()
        .x(function(d,i) { return x(dates[i]); })
        .y(function(d,i) { return y(d); });

      var area = d3.svg.area()
        .x(function(d,i) { return x(dates[i]); })
        .y0(function(d) { return y(minY); })
        .y1(function(d) { return y(d); });

      var svg = d3.select('#' + id)
        .append('svg')
        .attr('width', width)
        .attr('height', height);

      // LINE GRAPH
      var graphBg = svg.append('g').append('rect')
        .attr('x', 0)
        .attr('y', 0)
        .attr('width', width)
        .attr('height', height)
        .attr('fill', '#fff');

      var titleElem = svg.append('g').append('text')
        .text(title)
        .attr('x', width / 2)
        .attr('y', 28)
        .attr('text-anchor', 'middle')
        .attr('class', 'title');

      var xAxisAdjust = 2;
      var xAxisElem = svg.append('g')
        .attr('class', 'x axis')
        .attr('transform', 'translate(0,' + y(minY) + ')')
        .call(xAxis)
        .selectAll('text')
        .attr('transform', function(d) { return 'rotate(-90,' + (+this.getAttribute('x')-2) + ',' + (+this.getAttribute('y')+2) + ')'; })
        .style('text-anchor', 'end')

      var yAxisElem = svg.append('g')
        .attr('class', 'y axis')
        .attr('transform', 'translate(' + x(minX) + ',0)')
        .call(yAxis);

      /*
      var yAxisLabel = svg.append('g').append('text')
        .text('Cumulative Item Downloads')
        .attr('x', margin.left - 65)
        .attr('y', height / 2)
        .attr('text-anchor', 'middle')
        .attr('transform', 'rotate(270, ' + (margin.left - 65) + ', ' + (height / 2) + ')')
        .attr('class', 'label');
      */

      var lineGraph = svg.append('g').selectAll('path')
        .data(data)
        .enter().append('path')
        .attr('d', function(d) { return line(d); })
        .attr('stroke', function(d, i) { return color[i]; })
        .attr('fill', 'none')
        .attr('stroke-width', '1.5px');

      var circle = svg.append('g').selectAll('g')
        .data(data)
        .enter().append('g').append('circle')
        .attr('cx', x(dates[0]))
        .attr('cy', function(d) { return y(d[0]); })
        .attr('opacity', 0)
        .attr('r', 4)
        .attr('fill', function(d, i) {
          return color[i];
        });

      resetTooltip();

      $('#' + id)
        .on('mouseenter', function(e) {
          circle.attr('opacity', 1);
          if(!multi) {
            $('#' + tooltip).show();
          }
        })
        .on('mouseleave', function(e) {
          resetTooltip();
          circle.attr('opacity', 0);
        })
        .on('mousemove', function(e) {
          var mouseX = e.pageX - $('svg').offset().left;

          // find closest circle
          var closestDate = x.invert(mouseX);
          closestDate = _.min(dates, function(d) { return Math.abs(d - closestDate); });
          var index = dates.indexOf(closestDate);

          if(index != curIndex) {
            curIndex = index;

            var xVal = x(closestDate);

            var tooltipX = xVal - 40;
            if(multi) tooltipX = xVal - 60;
            var ttminX = margin.left;
            if(multi) ttminX = margin.left + 10;
            var ttmaxX = width - 120;
            if(multi) ttmaxX = width - margin.right - 150;

            if(tooltipX < ttminX) tooltipX = ttminX;
            if(tooltipX > ttmaxX) tooltipX = ttmaxX;

            var tooltipY = y(data[0][index]) - 45;
            if(multi) tooltipY = 50;

            updateTooltip(tooltipX, tooltipY, index, closestDate);
            circle.transition().duration(50)
              .attr('cx', xVal)
              .attr('cy', function(d) { return y(d[curIndex]); });
          }
        });

    };

    var init = function() {
      render();
    };

    init();
  };


  var showTooLong = function() {
    $("#instructions").html('<div class="warning"><strong>Please note:</strong> You have selected more filter items than this chart can display. Only the first ten have been displayed, although the full set is available using the export tools.</div>');
  };

  $.getJSON('data/json-time-running.php?<?php echo $_SERVER["QUERY_STRING"];?>',function(data) {
    $.each(data,function(key,val) {
      if(key=="dates") {
        dates = [];
        $.each(val,function(key, val) {
          temp = new Date(val);
          dates.push(new Date(val));
        });

      } else if(key=="dataNamesRaw") {
        dataNamesRaw = [];
        $.each(val,function(key, val) {
          dataNamesRaw.push(val);
        });
        if(dataNamesRaw.length > 10) {
          showTooLong();
        }
      } else if(key=="dataRaw") {
        $.each(val,function(key, val) {
          dataRaw.push(val);
        });
      } else {
        console.log("What?");
      }
    });
  })
  .done(function() {
    graphNew('time', 10, 'Cumulative Article Downloads');
  });

  // Set export options
  $("#exports").empty();
  $("#exports").append('<li><a data-format="csv">CSV</a></li>');
});

</script>
<!-- Debugging query structure 
<p>Data called from <a href="data/json-time-running.php?<?php echo $_SERVER["QUERY_STRING"];?>">data/json-time-running.php?<?php echo $_SERVER["QUERY_STRING"];?></a></p>
-->