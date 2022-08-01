<?php

/*
* updates season Table based on given id 
*
*/

global $wpdb;
global $post;

//Get Table ID to update
$id = $_POST['id'];

//Get Checkbox Status
$checked = $_POST['checked'];

//Get From/To- Dates from Table to update
$oldTable = (array) $wpdb->get_results("SELECT dateFrom, dateTo FROM ".$wpdb->prefix."micerule_result_tables WHERE mrtable_id =".$id);

$dateTo = (int)$oldTable[0]->dateTo;
$dateFrom = (int)$oldTable[0]->dateFrom;



$allPoints = 0;

//Date and IDs of micerule_tables
$data = (array) $wpdb->get_results('SELECT post_id,meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_time" ORDER BY meta_value ASC');

//array for name and points
$results = array();

//array for judges
$judgeCounter= array();

//Iterate through all Result Tables
for ($j=0;$j<count($data);$j++) {
  //Check if Table is within time limit
  if(strtotime($data[$j]->meta_value) >= $dateFrom && strtotime($data[$j]->meta_value) <= $dateTo){
    //Get Results from Table
    $table = (array) $wpdb->get_results('SELECT meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_settings" AND post_id = '.(int)$data[$j]->post_id);

    //Transform String from meta_value into Array
    $table2 = unserialize($table[0]->meta_value);

    //Iterate through Array and update Points and Names
    for($i=0; $i<12; $i++){
      $results[$table2['name'][$i]] += $table2['points'][$i];
    }

    //Points for Judges
    //$allPoints += 16;
    //$judgePoints = 0;
    for($k=0; $k<3; $k++){
      if($table2['judges'][$k] != ''){
        if($table2['pShip'][$k]==''){
          $judgeCounter[$table2['judges'][$k]] += 1;
        }else{
          $judgeCounter[$table2['pShip'][$k]] += 1;
        }
      }
    }
    //$allPoints += $judgePoints;


  }

}

foreach($judgeCounter as $key=>$value){
  $results[$key] += floor($results[$key]*0.03*$value);
}

//Sort Array From Best to Worst(Points)
arsort($results);

//prepare data for sql query
$jsonResult= json_encode(array_slice($results,0,19));
$dbData=array('season_results'=> $jsonResult,
'dateFrom'=> $dateFrom,
'dateTo'=> $dateTo,
'seasonTable'=>(int)$checked,);
$where = [ 'mrtable_id' => $id ];

//update Database
$wpdb->update($wpdb->prefix.'micerule_result_tables',$dbData,$where);

echo var_dump($checked);

wp_die();
