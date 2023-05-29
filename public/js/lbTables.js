/**
* Calls 'lbTables' to collect the html and chart data and displays everything
*
* Called on Page Load
*
*/
var chartData=JSON.stringify("");
var chartColour = JSON.stringify("");
var legendData = "";
jQuery(document).ready(function($){
  google.charts.load('current', {'packages':['corechart']});



  if($("#chart").length){

    // Load the Visualization API and the corechart package.

    google.charts.setOnLoadCallback(appendLeaderBoardData);

    /**
    * Executes when a different season is selected
    *
    * Calls 'lbTables' again to collect the new html and chart data for the new season
    *
    *
    */
    $("#seasonSelect").on('change',function(){
      var time= $(this).val();
      jQuery.ajax({
        type: 'POST',
        url: my_ajax_obj.ajax_url,
        data: {
          _ajax_nonce: my_ajax_obj.nonce,
          action: 'lbTables',
          time: time,
        },
        success: function (data) {
          $(".lbTables").empty();
          $("#lbTopTwenty").append(data.split("||")[0]);
          $("#lbBIS").append(data.split("||")[1]);
          $("#lbVarieties").append(data.split("||")[2]);
          drawChart((data.split("||")[3]),(data.split("||")[4]));
          console.log((data.split("||")[5]));
          chartData= data.split("||")[3];
          chartColour = data.split("||")[4];
          legendData = data.split("||")[5];

          $("#lbSectionLeaders").append(data.split("||")[6]);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          alert(errorThrown);
        }
      });


    });

  }
});

function appendLeaderBoardData(){

  var time= $("#seasonSelect").val();
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'lbTables',
      time: time,
    },
    success: function (data) {
      console.log(data);
      $("#lbTopTwenty").append(data.split("||")[0]);
      $("#lbBIS").append(data.split("||")[1]);
      $("#lbVarieties").append(data.split("||")[2]);
      drawChart((data.split("||")[3]),(data.split("||")[4]));

      chartData= data.split("||")[3];
      chartColour = data.split("||")[4];
      legendData = data.split("||")[5];

      $("#lbSectionLeaders").append(data.split("||")[6]);

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
      drawChart2(chartData,chartColour,legendData);
    }else{
      drawChart(chartData,chartColour);
    }
  });
}


//draws Chart with the data from the lbTables ajax call
function drawChart(data,colour) {

  var pieData = JSON.parse(data)

  var breedColour = JSON.parse(colour);


  //sort pieData
  var sortable = [];
  for (var prop in pieData) {
    sortable.push([prop, pieData[prop]]);
  }


  sortable.sort(function(a, b) {
    return b[1] - a[1];
  });



  // Create the data table.
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'name');
  data.addColumn('number', 'wins');
  var colours={};
  var circleFill = []
  for(var i=0;i<sortable.length;i++){
    data.addRow([sortable[i][0],sortable[i][1]]);
    colours[i] = { color: breedColour[0][sortable[i][0]]};
    circleFill.push(breedColour[1][sortable[i][0]]);
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


  for(var key in colours){
    options.slices[key]= colours[key];
  }

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.PieChart(document.getElementById('chart'));

  google.visualization.events.addListener(chart, 'ready', function () {
    var iteration = 0;
    //console.log(circleFill);
    $("#chart").find("svg").find("circle").each(function () {
      $(this).attr("class",circleFill[iteration]);
      iteration++;
    });
  });


  chart.draw(data, options);

}



//same as drawChart but adds legend underneath chart for responsiveness
function drawChart2(data,colour,legendData) {

  var pieData = JSON.parse(data)

  var breedColour = JSON.parse(colour);


  //sort pieData
  var sortable = [];
  for (var prop in pieData) {
    sortable.push([prop, pieData[prop]]);
  }


  sortable.sort(function(a, b) {
    return b[1] - a[1];
  });



  // Create the data table.
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'name');
  data.addColumn('number', 'wins');
  var colours={};
  var circleFill = []
  for(var i=0;i<sortable.length;i++){
    data.addRow([sortable[i][0],sortable[i][1]]);
    colours[i] = { color: breedColour[0][sortable[i][0]]};
    circleFill.push(breedColour[1][sortable[i][0]]);
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


  for(var key in colours){
    options.slices[key]= colours[key];
  }

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.PieChart(document.getElementById('chart'));

  google.visualization.events.addListener(chart, 'ready', function () {
    var iteration = 0;
    console.log(circleFill);
    $("#chart").find("svg").find("circle").each(function () {
      $(this).attr("class",circleFill[iteration]);
      iteration++;
    });
  });


  chart.draw(data, options);
  $("#chart").append(legendData);
}
