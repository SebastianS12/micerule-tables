<?php

/*
* creates html for judge display of event with given id 
*
*/
function micerule_shortcode_judges($atts){

  global $wpdb;
  $micerule_settings = shortcode_atts(array(
    'id' => ''
  ), $atts);



  $id = filter_var($micerule_settings['id'], FILTER_SANITIZE_NUMBER_INT);

  //get postmeta based on event ID
  $table_post_meta = get_post_meta($id, 'micerule_data_settings', true);


  //start html
  $html = '<div class="micerule_judges" style="text-align: center">';

  //header
  $html .= "<p>--Judges--</p>";

  //create rows with names of judges and their classes
  for($i=0;$i<3;$i++){
    if($table_post_meta['judges'][$i] != ''){
      $html .= "<span>".$table_post_meta['judges'][$i].":  ";
      $index=0;
      foreach($table_post_meta['classes'][$i] as $value){
        if(isset($table_post_meta['classes'][$i][$index+1])){
          $html .= $value.", ";
          $index++;
        }else{
          $html .= $value;
          $index++;
        }
      }
      $html .= "</span>";
      $html .= "<br>";
    }
  }


  //end html
  $html .= '</div>';
  return $html;

}


add_shortcode('micerule_judges', 'micerule_shortcode_judges');
?>
