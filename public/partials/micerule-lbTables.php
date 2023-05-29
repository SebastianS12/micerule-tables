<?php

/*
* ajax action
*
* creates html for leaderboard page
*
*/

global $post;
global $wpdb;


//Get dateTo and dateFrom
$time= explode("/",$_POST['time']);

$defaultPath = get_home_url()."/wp-content/themes/Divi-child/Assets/spacer.gif";

//Date and IDs of micerule_tables
$data = (array) $wpdb->get_results('SELECT post_id,meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_time" ORDER BY meta_value ASC');

//select tables where tableFrom < $time[0], order by Desc Top 1
//get dateFrom, dateTo -> get varieties result
$lastYear = (array) $wpdb->get_results('SELECT dateFrom,dateTo FROM '.$wpdb->prefix.'micerule_result_tables WHERE dateTo < "'.(int)$time[0].'" AND seasonTable=1  ORDER BY dateTo DESC LIMIT 1');


//----------------------------Top Twenties--------------------
//array for name and points
$results = array();

//array for BIS-Winners
$bisResults= array();

//array for Varieties
$varieties = array();

//array for variety points
$varietyResults = array();

//
$breedColourOption = array(array(),array());

//array for judges
$judgeCounter= array();

//array for section leaders
$sectionLeaders = array();


//iterate through every ResultTable
for ($j=0;$j<count($data);$j++) {
  //Check if Table is within time limit
  if(strtotime($data[$j]->meta_value) >= (int)$time[0] && ((isset($time[1])==false)||strtotime($data[$j]->meta_value) <= (int)$time[1])){
    //Get Results from Table
    $table = (array) $wpdb->get_results('SELECT meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_settings" AND post_id = '.(int)$data[$j]->post_id);



    //Transform String from meta_value into Array
    $table2 = unserialize($table[0]->meta_value);

    if($table2['name'][0] != ''){
      //Update BIS-array
      $bisResults[$table2['name'][0]] += 1;

      //Update varieties-array
      //get Breed from Options with ID
      //normal breed option
      if(isset(get_option(get_option("mrOption_id")[$table2['breeds'][0]])['name'])){
        $varietyCounter = get_option(get_option("mrOption_id")[$table2['breeds'][0]])['name'];
        $breedColourOption[0][$varietyCounter] = get_option(get_option("mrOption_id")[$table2['breeds'][0]])['colour'];
        $breedColourOption[1][$varietyCounter] = get_option(get_option("mrOption_id")[$table2['breeds'][0]])['class'];
        $legendPaths[$varietyCounter]= get_option("mrOption_paths")[$table2['breeds'][0]];
      }else{
        $varietyCounter = "No record";
        $breedColourOption[0][$varietyCounter] = "#FFFFFF";
        $breedColourOption[1][$varietyCounter] = "default";
        $legendPaths[$varietyCounter]= $defaultPath;
      }
      $varieties[$varietyCounter] += 1;
    }

    //Iterate through Array and update Points and Names+ Varieties
    for($i=0; $i<12; $i++){
      if($table2['name'][$i] != ''){
        $results[$table2['name'][$i]] += $table2['points'][$i];

        //update sectionLeaders array
        if($i > 1){
          $section = ($i % 2 == 0) ? explode(" ", $table2['awards'][$i])[1] : explode(" ", $table2['awards'][$i])[3];

          if(array_key_exists($section, $sectionLeaders) && array_key_exists($table2['name'][$i], $sectionLeaders[$section])){
            $sectionLeaders[$section][$table2['name'][$i]] += $table2['points'][$i];
          }else{
            $sectionLeaders[$section][$table2['name'][$i]] = $table2['points'][$i];
          }
        }




        //get Breed from Options with ID
        //normal breed option
        if(isset(get_option(get_option("mrOption_id")[$table2['breeds'][$i]])['name'])){
          $varietyCounter = get_option(get_option("mrOption_id")[$table2['breeds'][$i]])['name'];
          $legendPathsVariety[$varietyCounter]= get_option("mrOption_paths")[$table2['breeds'][$i]];
          $iconsVarietyColour[$varietyCounter]=get_option(get_option("mrOption_id")[$table2['breeds'][$i]])['colour'];
        }//old EventTable
        else{
          $varietyCounter = "No record";
          $legendPathsVariety[$varietyCounter]= $defaultPath;
          $iconsVarietyColour[$varietyCounter]= "#FFF";
        }
        //update varietyResults with Variety Name from Option
        $varietyResults[$varietyCounter] += $table2['points'][$i];
      }
    }

    //Points for Judges
    for($k=0; $k<3; $k++){
      if($table2['judges'][$k] != '' && $table2['name'][0] != ''){
        if($table2['pShip'][$k]==""){


          $judgeCounter[$table2['judges'][$k]] += 1;

          continue;
        }

        $judgeCounter[$table2['pShip'][$k]] += 1;

      }
    }



  }

}
$results2 = $results;

foreach($judgeCounter as $key=>$value){
  $results2[$key] += $results2[$key]*0.03*$value;
}

//Sort Array From Best to Worst(Points)
arsort($results2);
arsort($varietyResults);
arsort($bisResults);

//limit displayed results to 20
$topTwenty = array_slice($results2,0,20);


//start HTML
$html= "<div class = 'resultTable'>";
$html .= "<table id = 'micerule_resultTable' style='width:100%'>";

//Create header rows
$html .= "<thead><tr>";
$html .= "<th class = 'resultHeader'></th>";
$html .= "<th class = 'avatarHeader'></th>";
$html .= "<th class = 'resultHeader'>Name</th>";
$html .= "<th class = 'resultHeader'>Points Accum'd</th>";
$html .= "<th class = 'resultHeader'>Times Judged</th>";
$html .= "<th class = 'resultHeader'>Adjusted Points</th>";
$html .= "<th class = 'resultHeader'>Grand Total</th>";
$html .= "</tr></thead><tbody>";

if(is_user_logged_in()){

  //table contents
  $position = 1;
  $keys = array_keys($topTwenty);
  //$users = $wpdb->get_results("SELECT * FROM $wpdb->usermeta");
  for($i = 0; $i < count($topTwenty); $i++){
    if(isset($judgeCounter[$keys[$i]])){$jPoints=($results[$keys[$i]]+($results[$keys[$i]]*0.03*$judgeCounter[$keys[$i]]));}else {$jPoints = "";}
    if($i > 0 && round($topTwenty[$keys[$i]]) != round($topTwenty[$keys[$i-1]])){
      $position ++;
    }
    $firstPosClass = ($position == 1) ? 'firstPos' : '';
    $userID = $wpdb->get_results("SELECT ID FROM $wpdb->users WHERE display_name = '".$keys[$i]."'");

    $html .= "<tr class = ".$firstPosClass.">";
    $html .= "<td class = 'season-position'>".$position."</td>";
    $html .= "<td class = 'avatarCell'>".get_avatar($userID[0]->ID, 96,'monsterid')."</th>";
    $html .= "<td class = 'resultCell'>".$keys[$i]."</td>";
    $html .= "<td class = 'resultCell2'>".$results[$keys[$i]]."</td>";
    $html .= "<td class = 'resultCell2'>".$judgeCounter[$keys[$i]]."</td>";
    $html .= "<td class = 'resultCell2'>".$jPoints."</td>";
    $html .= "<td class = 'resultCell2'>".round($topTwenty[$keys[$i]])."</td>";
    $html .= "</tr>";
  }

}else{
  $position = 1;
  $keys = array_keys($topTwenty);
  for($i = 0; $i < count($topTwenty); $i++){
    if(isset($judgeCounter[$keys[$i]])){$jPoints=($results[$keys[$i]]+($results[$keys[$i]]*0.03*$judgeCounter[$keys[$i]]));}else {$jPoints = "";}
    if($i > 0 && round($topTwenty[$keys[$i]]) != round($topTwenty[$keys[$i-1]])){
      $position ++;
    }
    $firstPosClass = ($position == 1) ? 'firstPos' : '';

    $html .= "<tr ".$firstPosClass.">";
    $html .= "<td class = 'season-position'>".$position."</td>";
    $html .= "<td class = 'avatarCell'><img src = '".plugin_dir_url(__FILE__)."lock.svg' style = 'height:96px; width:96px'></td>";
    $html .= "<td class = 'resultCellBlur'>";
    $html .= "<div class ='blurDiv' style='width:".random_int(35,82)."px;background-image: url(".plugin_dir_url(__FILE__)."blur.png);height:20px ;display:inline-block;".random_int(0,500)."px 0'></div><span> </span>
    <div class ='blurDiv' style='width:".random_int(35,90)."px;background-image: url(".plugin_dir_url(__FILE__)."blur.png);height:20px ;display:inline-block;".random_int(0,500)."px 0'></div>";
    $html .= "</td>";
    $html .= "<td class = 'resultCell2'>".$results[$keys[$i]]."</td>";
    $html .= "<td class = 'resultCell2'>".$judgeCounter[$keys[$i]]."</td>";
    $html .= "<td class = 'resultCell2'>".$jPoints."</td>";
    $html .= "<td class = 'resultCell2'>".round($topTwenty[$keys[$i]])."</td>";
    $html .= "</tr>";
  }
}

//close HTML
$html .= "</tbody>";
$html .= "</table>";
$html .= "</div>";
$html .="||";

//-------------------------Top Twenties End----------------

//-------------------------BIS-Winners html-----------------
$html .= "<div class = 'bisResultTable'>";
$html .= "<table id = 'micerule_bis_resultTable' style='width:100%'>";

//Create header rows
$html .= "<thead><tr>";
$html .= "<th class = 'bisResultHeader'>Pos.</th>";
$html .= "<th class = 'bisResultHeader'>Fancier</th>";
$html .= "<th class = 'bisResultHeader'>Shows Won</th>";
$html .= "</tr></thead><tbody>";

//table contents
$position = 1;
$keys = array_keys($bisResults);
if(is_user_logged_in()){
  for($i = 0; $i < count($bisResults); $i++){
    if($i > 0 && $bisResults[$keys[$i]] != $bisResults[$keys[$i-1]]){
      $position ++;
    }
    $firstPosClass = ($position == 1) ? 'firstPos' : '';
    $html .= "<tr class = ".$firstPosClass.">";
    $html .= "<td class = 'resultCell'>".$position."</td>";
    $html .= "<td class = 'resultCell'>".$keys[$i]."</td>";
    $html .= "<td class = 'resultCell2'>".$bisResults[$keys[$i]]."</td>";
    $html .= "</tr>";
  }
}else{
  foreach($bisResults as $key=> $value){
    $firstPosClass = ($position == 1) ? 'firstPos' : '';
    $html .= "<tr class = ".$firstPosClass.">";
    $html .= "<td class = 'resultCell'>".$position."</td>";
    $html .= "<td class = 'resultCellBlur'>";
    $html .= "<div class ='blurDiv' style='width:".random_int(35,82)."px;background-image: url(".plugin_dir_url(__FILE__)."blur.png);height:20px ;display:inline-block;".random_int(0,500)."px 0'></div><span> </span>
    <div class ='blurDiv' style='width:".random_int(35,90)."px;background-image: url(".plugin_dir_url(__FILE__)."blur.png);height:20px ;display:inline-block;".random_int(0,500)."px 0'></div>";
    $html .= "</td>";
    $html .= "<td class = 'resultCell2'>".$value."</td>";
    $html .= "</tr>";
    $position++;
  }
}
//close HTML
$html .= "</tbody>";
$html .= "</table>";
$html .= "</div>";
$html .="||";
//-----------------------------BIS-Winners html End-------------

//-----------------------------Varieties Chart------------------

//-----------------------------Varieties Comparison--------------

//-----------------------lastYear Tables-----------------------

if(isset($lastYear)){

  $lyVarieties= array();
  $lyVarietyResults = array();
  for ($j=0;$j<count($data);$j++) {
    //Check if Table is within time limit
    if(strtotime($data[$j]->meta_value) >= (int)$lastYear[0]->dateFrom && strtotime($data[$j]->meta_value) <= (int)$lastYear[0]->dateTo){
      //Get Results from Table
      $lyTable = (array) $wpdb->get_results('SELECT meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_settings" AND post_id = '.(int)$data[$j]->post_id);


      //Transform String from meta_value into Array
      $lyTable2 = unserialize($lyTable[0]->meta_value);



      //Update lyVarieties-array
      //get Breed from Options with ID
      //normal breed option
      if(isset(get_option(get_option("mrOption_id")[$table2['breeds'][0]])['name'])){
        $lyVarietyCounter = get_option(get_option("mrOption_id")[$lyTable2['breeds'][0]])['name'];
      }else{
        $lyVarietyCounter = $lyTable2['breeds'][0];
      }
      $lyVarieties[$lyVarietyCounter] += 1;

      //Iterate through Array Varieties
      for($i=0; $i<12; $i++){
        if($lyTable2['name'][$i] != ''){

          //get Breed from Options with ID
          //normal breed option
          if(isset(get_option(get_option("mrOption_id")[$lyTable2['breeds'][$i]])['name'])){
            $lyVarietyCounter = get_option(get_option("mrOption_id")[$lyTable2['breeds'][$i]])['name'];
          }//old EventTable
          else{
            $lyVarietyCounter = $lyTable2['breeds'][$i];
          }
          //update varietyResults with Variety Name from Option
          $lyVarietyResults[$lyVarietyCounter] += $lyTable2['points'][$i];
        }
      }
  }

    arsort($lyVarietyResults);
    $lyKeys = array_keys($lyVarietyResults);

    $lyPosition= array();
    $hPosition = 1;

    //fill array with key-name and position
    for($i=0;$i<count($lyVarietyResults);$i++){
      if($i>0 && $lyVarietyResults[$lyKeys[$i]] != $lyVarietyResults[$lyKeys[$i-1]]){
        $hPosition ++;
      }
      $lyPosition[$lyKeys[$i]]= $hPosition;
    }
  }
}
//-------------------------End-----------------------------

//-------------------------VarietyResults Tables html------------
$keys = array_keys( $varietyResults );


$html2 ="<div id = 'varieties_table'>";

$html .= "<div class = 'varietiesResultTable'>";
$html .= "<table id = 'micerule_varieties_resultTable' style='width:100%'>";

//Create header rows
$html .= "<thead><tr>";
$html .= "<th class = 'varietiesResultHeader'>Pos.</th>";
$html .= "<th class = 'varietiesResultHeader'></th>";
$html .= "<th class = 'varietiesResultHeader'><span>Variety</span></th>";
$html .= "<th class = 'varietiesResultHeader'><span>Points Won</span></th>";
$html .= "<th class = 'varietiesResultHeader'><span>Last Y Pos</span></th>";
$html .= "</tr></thead><tbody>";

$position=1;
//table contents
for($i=0;$i<count($varietyResults);$i++){
  if($i>0 && $varietyResults[$keys[$i]] != $varietyResults[$keys[$i-1]]){
    $position ++;
  }
  $firstPosClass = ($position == 1) ? 'firstPos' : '';
  $html .= "<tr class = ".$firstPosClass.">";
  $html .= "<td class = 'resultCell'>".$position."</td>";
  $html .= "<td class = 'resultCell'><div class='variety-icon' style='background:url(".$legendPathsVariety[$keys[$i]].");background-repeat:no-repeat;background-color:".$iconsVarietyColour[$keys[$i]]."'></div></td>";
  $html .= "<td class = 'resultCell' >".$keys[$i]."</td>";
  $html .= "<td class = 'resultCell2'>".$varietyResults[$keys[$i]]."</td>";
  $html .= "<td class = 'resultCell' >".$lyPosition[$keys[$i]]."</td >";
  $html .= "</tr>";
}

//close HTML
$html .= "</tbody>";
$html .= "</table>";
$html .= "</div>";

$html .="</div>";

//--------------------------------End---------------------------

//-------------------------------Chartlegend mobile-------------
//sort
arsort($varieties);

//get count of shows
$hVarieties = $varieties;
$showCount = array_reduce($hVarieties,function($sum,$a){return( $sum+$a);});

$legendHtml = "<div class = 'chartLegend mobile'>";
$legendHtml .= "<table id = 'chartLegend_Table' >";

//table header
$legendHtml .= "<thead>";
$legendHtml .= "<tr>";
$legendHtml .= "<th>#</th>";
$legendHtml .= "<th>Variety</th>";
$legendHtml .= "<th>Shows Won</th>";
$legendHtml .= "<th>%</th>";
$legendHtml .= "</tr>";
$legendHtml .= "</thead>";

//table contents
foreach($varieties as $key=> $value){
  $legendHtml .= "<tr>";
  $legendHtml .= "<td class = 'legendCellNumber'><div class='legend-number' style='background-color:".$breedColourOption[0][$key]." '></div></td>";
  $legendHtml .= "<td class = 'legendCellVariety'>".$key."</td>";
  $legendHtml .= "<td class = 'legendCellNumber'>".$value."</td>";
  $legendHtml .= "<td class = 'legendCellNumber'>".sprintf("%.1f",(($value/$showCount)*100))."%</td>";
  $legendHtml .= "</tr>";
}

//close HTML
$legendHtml .= "</table>";
$legendHtml .= "</div>";

//---------------------------------End---------------------------

//--------------------------------Section Leaders Table Html------------------------------
$sectionLeadersHtml = "";
foreach($sectionLeaders as $section => $leaders){
  //start HTML
  $sectionLeadersHtml .= "<div class='section-card'>";
  $sectionLeadersHtml .= "<p class='sectionTitle'>".$section."</p>";
  $sectionLeadersHtml .= "<div class='sectionResultHeader'>";
  $sectionLeadersHtml .= "<p>Rank</p>";
	$sectionLeadersHtml .= "<p>Name</p>";
  $sectionLeadersHtml .= "<p>Points</p>";
	$sectionLeadersHtml .= "</div>";


  //Create table
	$sectionLeadersHtml .= "<div id='micerule_section_resultTable'>";

  //table contents
  arsort($leaders);
  $keys = array_keys($leaders);
  $position = 1;
  for($i = 0; $i < count($leaders); $i++){
    if($i>0 && $leaders[$keys[$i]] != $leaders[$keys[$i-1]]){
      $position ++;
    }
    $firstPosClass = ($position == 1) ? 'firstPos' : '';
    $sectionLeadersHtml .= "<ul class = ".$firstPosClass.">";

		$sectionLeadersHtml .= "<li>";
		$sectionLeadersHtml .= "<span class='resultCell0'>".$position."</span>";
    if(is_user_logged_in()){
      $sectionLeadersHtml .= "<span class='resultCell'>".$keys[$i]."</span>";
    }else{
      $sectionLeadersHtml .= "<span class = 'resultCellBlur'>";
      $sectionLeadersHtml .= "<div class ='blurDiv' style='width:".random_int(35,82)."px;background-image: url(".plugin_dir_url(__FILE__)."blur.png);height:20px ;display:inline-block;".random_int(0,500)."px 0'></div><span> </span>
      <div class ='blurDiv' style='width:".random_int(35,90)."px;background-image: url(".plugin_dir_url(__FILE__)."blur.png);height:20px ;display:inline-block;".random_int(0,500)."px 0'></div>";
      $sectionLeadersHtml .= "</span>";
    }
    $sectionLeadersHtml .= "<span class='resultCell2'>".$leaders[$keys[$i]]."</span>";
		$sectionLeadersHtml .= "</li>";
    $sectionLeadersHtml .= "</ul>";
  }

  //close HTML
	$sectionLeadersHtml .= "</div>";
	$sectionLeadersHtml .= "</div>";

}

//--------------------------------End Section Leaders Table Html---------------------------


echo $html;
echo '||';
echo json_encode($varieties);
echo '||';
echo json_encode($breedColourOption);
echo '||';
echo ($legendHtml);
echo '||';
echo($sectionLeadersHtml);

wp_die();
