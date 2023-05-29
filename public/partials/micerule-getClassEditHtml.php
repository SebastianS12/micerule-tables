<?php
  global $post;
  global $wpdb;

  $section = $_POST['section'];
  $locationID = $_POST['id'];
  $className = $_POST['className'];
  $varietyOptions = $wpdb->get_results("SELECT option_name FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%".$section."'",ARRAY_A);
  $eventClasses = get_post_meta($locationID, 'micerule_data_event_classes',true);

  $usedVarieties = array();
  $selectedVarieties = array();
  foreach($eventClasses[$section] as $eventClass){
    if($eventClass[$className] != $className){
      $usedVarieties = array_merge($usedVarieties, $eventClass['varieties']);
    }
    if($eventClass['className'] == $className){
      $selectedVarieties = $eventClass['varieties'];
    }
  }

  $selectOptions['unselected'] = array();
  $selectOptions['selected'] = array();
  foreach($varietyOptions as $varietyOption){
    $varietyName = get_option($varietyOption['option_name'])['name'];
    if(!in_array($varietyName, $usedVarieties)){
      array_push($selectOptions['unselected'], $varietyName);
    }
    if(in_array($varietyName, $selectedVarieties)){
      array_push($selectOptions['selected'], $varietyName);
    }
  }


  $html = "<form><input type='text' style='z-index:10000' id='classInput' value='".$className."'><br>"; //</form>";

  foreach($selectOptions['selected'] as $selectedOption){
    $html .= '<input type="checkbox" id= "'.$selectedOption.'&-&Select" name = "'.$selectedOption.'&-&Select" class = "varietySelect" checked>';
    $html .= '<label for= "'.$selectedOption.'&-&Select">'.$selectedOption.'</label><br>';
  }

  foreach($selectOptions['unselected'] as $selectedOption){
    $html .= '<input type="checkbox" id= "'.$selectedOption.'&-&Select" name = "'.$selectedOption.'&-&Select" class = "varietySelect">';
    $html .= '<label for= "'.$selectedOption.'&-&Select">'.$selectedOption.'</label><br>';
  }

  $html .= "</form>";

  echo($html);
  wp_die();
