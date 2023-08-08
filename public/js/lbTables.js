/**
* Calls 'lbTables' to collect the html and chart data and displays everything
*
* Called on Page Load
*
*/
jQuery(document).ready(function($){
  google.charts.load('current', {'packages':['corechart']});
  var chartDataJson = JSON.stringify("");
  var chartMobileLegendHtml = "";

  if($("#chart").length){
    google.charts.setOnLoadCallback(appendLeaderBoardData);

    $("#seasonSelect").on('change',function(){
      appendLeaderBoardData();
    });
  }

  function appendLeaderBoardData(){
    var time = $("#seasonSelect").val();
    jQuery.ajax({
      type: 'POST',
      url: my_ajax_obj.ajax_url,
      data: {
        _ajax_nonce: my_ajax_obj.nonce,
        action: 'lbTables',
        time: time,
      },
      success: function (data) {
        $("#lbTopTwenty").replaceWith(data.split("||")[0]);
        $("#lbBIS").replaceWith(data.split("||")[1]);
        $("#lbVarieties").replaceWith(data.split("||")[2]);
        chartDataJson = data.split("||")[3];
        drawChart(chartDataJson);
        chartMobileLegendHtml = data.split("||")[4];
  
        $("#lbSectionLeaders").replaceWith(data.split("||")[5]);
  
        if(window.innerWidth<767){
          $(window).trigger('resize');
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
  
    });
  
    //makes Chart responsive
    $(window).resize(function(){
      if(window.innerWidth<767){
        console.log(chartMobileLegendHtml);
        drawChart(chartDataJson);
        $("#chart").append(chartMobileLegendHtml);
      }else{
        drawChart(chartDataJson);
      }
    });
  }
  
  
  //draws Chart with the data from the lbTables ajax call
  function drawChart(chartDataJson) {
    var chartData = JSON.parse(chartDataJson)
  
    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'name');
    data.addColumn('number', 'wins');
    var colours={};
    var circleFill = []
    for(var i=0;i<chartData.length;i++){
      data.addRow([chartData[i]['variety_name'], parseInt(chartData[i]['times_won'])]);
      colours[i] = { color: chartData[i]['colour']};
      circleFill.push(chartData[i]['css_class']);
    }
  
  
    // Set chart options
    var options = {'width':"100%",
    'height':600,
    pieSliceText:'none',
    pieSliceTextStyle:{fontName: 'Bree Serif', fontSize: 25},
    slices:{},
    chartArea:{width:'100%',height:'360px', top: 0},
    pieSliceBorderColor:'white',
    backgroundColor:'transparent',
    enableInteractivity: 'false',
    legend: {position: "labeled",alignment:"center",textStyle: {fontName: 'Bree Serif', fontSize: 16, color:'white'}},
    'pieHole': 0.4};
  
  
    for(var index in colours){
      options.slices[index]= colours[index];
    }
    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('chart'));
  
    google.visualization.events.addListener(chart, 'ready', function () {
      var iteration = 0;
      $("#chart").find("svg").find("circle").each(function () {
        $(this).attr("class",circleFill[iteration]);
        iteration++;
      });
    });
  
    chart.draw(data, options);
  }
});
