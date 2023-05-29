<?php

/*
* creates html for chart svgs to insert into the page 
*
*/
function getSvg(){

  //Get Uploads for List
  require_once plugin_dir_path(__FILE__).'/getUploads.php';

  $defaultPath = get_home_url()."/wp-content/themes/Divi-child/Assets/spacer.gif";

  $html="";
  $html .="<div id='mr_Svg'>";

  //create default
  $html .="<style>";
  $html .=".default{";
    $html .="fill:url(#default);}";
    $html .="</style>";

    //svg
    $html .="<svg>";
    $html .="<defs>";
    $html .="<pattern id ='".$id."' width='100%' height='100%' patternUnits='objectBoundingBox'>";
    $html .="<image href='".$defaultPath."' width='50' height='50'>";
    $html .="</svg>";



    foreach($uploads as $value){

      //file name without extension
      $id = basename($value,".".pathinfo($value)['extension']);

      //style Element
      $html .="<style>";
      $html .=".".$id."{";
        $html .="fill:url(#".$id.");}";
        $html .="</style>";

        //svg
        $html .="<svg>";
        $html .="<defs>";
        $html .="<pattern id ='".$id."'width='100%' height='100%' x='4%' y='-2%' patternUnits='objectBoundingBox' patternTransform='scale(0.999)'>";
        $html .="<image href='".$value."'width='50' height='50'>";
        $html .="</svg>";
      }
      $html .= "</div>";
      return $html;
    }

    add_shortcode('mr_SVG','getSvg');
