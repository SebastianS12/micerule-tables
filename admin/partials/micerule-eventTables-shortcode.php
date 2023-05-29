<?php

/*
* creates html for a single event table, based on id
*
*/
function micerule_shortcode_table($atts){

  global $wpdb;
  $micerule_settings = shortcode_atts(array(
    'id' => ''
  ), $atts);


  $defaultPath = get_home_url()."/wp-content/themes/Divi-child/Assets/spacer.gif";

  //get the postmeta for selected id
  $table_post_meta = get_post_meta($micerule_settings['id'], 'micerule_data_settings', true);

  //get ID-array from option_id
  $ids = get_option("mrOption_id");

  //start html
  $html = "<div class='micerule_table_MotherShipContainer'>";
  $html .= "<p><a href ='".get_permalink($micerule_settings['id'])."'>Calendar Page</a></p>";

  if(is_user_logged_in()){
    $html.= "<div class='judges'>";

    //header
    $html .= "<p>Judges</p>";
    //create rows with names of judges and their classes
    for($i=0;$i<3;$i++){
      if($table_post_meta['judges'][$i] != ''){
        $html .= "<p>".$table_post_meta['judges'][$i].":  ";
        $index = 0;
        foreach($table_post_meta['classes'][$i] as $value){
          if(isset($table_post_meta['classes'][$i][$index+1])){
            $html .= $value.", ";
            $index++;
          }else{
            $html .= $value;
            $index++;
          }
        }
        $html .= "</p>";
      }
    }
    $html .= "<br>";
    $html.= "</div>";
  } //End is_user_logged_in() Judges

  //start table
  $html .= "<table id = 'micerule_table_".$micerule_settings['id']."'  class='eventTable'>";


  //Create the header rows
  $html .= "<thead><tr>";
  $html .= "<th class = 'eventHeader2'> Award</th>";
  $html .= "<th class = 'eventHeader'> Name</th>";
  $html .= "<th class = 'eventHeader'> Breed</th>";
  $html .= "<th class = 'eventHeader'> Age</th>";
  $html .= "<th class = 'eventHeader'> Points</th>";
  $html .= "</tr></thead><tbody>";

  //table content
  for($i = 0; $i<count($table_post_meta['awards']); $i++){
    $html .= "<tr>";
    $html .= "<td class='eventCell2'>".$table_post_meta['awards'][$i]."</td>";
    if(is_user_logged_in()){
      $html .= "<td class='eventCell'>".$table_post_meta['name'][$i]."</td>";
    }else{
      $html .= "<td class = 'resultCellBlur'>";
      $html .= "<div class ='blurDiv' style='width:".random_int(70,175)."px; background-image: url(".plugin_dir_url(__DIR__)."../public/partials/blur.png);height:20px ;display:inline-block; background-position: ".random_int(0,500)."px 0'></div>";
      $html .= "</td>";
    }


    //get Breed from Options with ID
    //normal breed option

    if(isset(get_option("mrOption_id")[$table_post_meta['breeds'][$i]])&& isset(get_option(get_option("mrOption_id")[$table_post_meta['breeds'][$i]])['name'])){
      $breed = get_option(get_option("mrOption_id")[$table_post_meta['breeds'][$i]])['name'];
      $iconPath = get_option("mrOption_paths")[$table_post_meta['breeds'][$i]];
      $iconColour = get_option(get_option("mrOption_id")[$table_post_meta['breeds'][$i]])['colour'];
    }
    else if($i<12){//Breed is deleted
      $breed = "(No record)";
      $iconPath = $defaultPath;
      $iconColour = "#FFF";
    }else{//show Unstandardised
      $breed = $table_post_meta['breeds'][$i];
      $iconPath = $defaultPath;
      $iconColour = "#FFF";
    }

    $html .= "<td class='eventCell'><div class='variety-icon' style='background:url(".$iconPath.");background-repeat:no-repeat;background-color:".$iconColour."'></div>".$breed."</td>";
    $html .= "<td class='eventCell'>".$table_post_meta['age'][$i]."</td>";
    $html .= "<td class='eventCell'>".$table_post_meta['points'][$i]."</td>";
    $html .= "</tr>";
  }
  $html .= "</tbody>";

  //end table
  $html .= "</table>";


  //end html
  $html .= "</div>";

  return $html;

}

add_shortcode('micerule_tables', 'micerule_shortcode_table');
?>
