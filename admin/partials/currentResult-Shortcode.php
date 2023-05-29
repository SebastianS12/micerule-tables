<?php

/*
* creates html for currentResult Table Shortcode, gets data from ajax call
*
*/
function currentTable(){

  global $wpdb;
  global $post;

  //Get Seasons
  $seasons= $wpdb->get_results("SELECT dateFrom,dateTo FROM ".$wpdb->prefix."micerule_result_tables WHERE seasonTable =1 ORDER BY dateTo DESC");

  $date2= $seasons[0]->dateTo +1;

  //-----------------------------Results-----------------------

  $allPoints = 0;

  //Date and IDs of micerule_tables
  $data = (array) $wpdb->get_results('SELECT post_id,meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_time" ORDER BY meta_value ASC');

  //array for name and points
  $results = array();

  //array for judges
  $judgeCounter= array();

  //iterate through every ResultTable
  for ($j=0;$j<count($data);$j++) {
    //Check if Table is within time limit
    if((empty($date2))||(strtotime($data[$j]->meta_value) >= $date2 && strtotime($data[$j]->meta_value) <= time())){
      //Get Results from Table
      $table = (array) $wpdb->get_results('SELECT meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_settings" AND post_id = '.(int)$data[$j]->post_id);

      //Transform String from meta_value into Array
      $table2 = unserialize($table[0]->meta_value);

      //Iterate through Array and update Points and Names
      for($i=0; $i<12; $i++){

        if($table2['name'][$i] != ''){
          if(isset($results[$table2['name'][$i]])){
            $results[$table2['name'][$i]] += $table2['points'][$i];
          }else{
            $results[$table2['name'][$i]] = $table2['points'][$i];
          }
        }
      }

      //Count Judge appearences or their partnerships
      for($k=0; $k<3; $k++){
        if(isset($table2['judges'][$k])){
          if($table2['judges'][$k] != ''){
            if(isset($table2['pShip'][$k])){
              if($table2['pShip'][$k]==''){
                if(isset($judgeCounter[$table2['judges'][$k]])){
                  $judgeCounter[$table2['judges'][$k]] += 1;
                }else{
                  $judgeCounter[$table2['judges'][$k]] = 1;
                }
              }else{
                if(isset($judgeCounter[$table2['pShip'][$k]])){
                  $judgeCounter[$table2['pShip'][$k]] += 1;
                }else{
                  $judgeCounter[$table2['pShip'][$k]] = 1;
                }
              }
            }
          }
        }
      }



    }

  }

  //update judge points based on judgeCounter
  foreach($judgeCounter as $key=>$value){
    if(isset($results[$key])){
      $results[$key] += floor($results[$key]*0.03*$value);
    }else{
      $results[$key]= floor(0.03*$value);
    }
  }

  //Sort Array From Best to Worst(Points)
  arsort($results);

  //limit displayed results to 20
  $topTwenty = array_slice($results,0,20);
  //---------------------------Results End--------------------------

  //---------------------------Table HTML---------------------------
  //start HTML
  $html= "<div class = 'resultTable'>";
  $html .= "<table id = 'micerule_resultTable'>";

  //Create header rows
  $html .= "<thead><tr>";
  $html .= "<th class = 'resultHeader'>Name</th>";
  $html .= "<th class = 'resultHeader'>Points</th>";
  $html .= "</tr></thead><tbody>";

  //table contents
  foreach($topTwenty as $key=> $value){
    $html .= "<tr>";
    $html .= "<td class = 'resultCell'>".$key."</td>";
    $html .= "<td class = 'resultCell2'>".$value."</td>";
    $html .= "</tr>";
  }

  //close HTML
  $html .= "</tbody>";
  $html .= "</table>";
  $html .= "</div>";

  //return HTML
  return $html;

  //------------------------Table HTML End-------------------

}

add_shortcode('resultTable','currentTable');
