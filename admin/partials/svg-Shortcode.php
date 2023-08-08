<?php

/*
* creates html for chart svgs to insert into the page 
*
*/
function getSvg(){
  $breedIconUrls = Breed::getBreedIconUrls();
  $html ="<div id='mr_Svg'>";

    foreach($breedIconUrls as $iconUrl){
      //file name without extension
      $id = basename($iconUrl['icon_url'],".".pathinfo($iconUrl['icon_url'])['extension']);

      //style Element
      $html .="<style>";
      $html .=".".$id."{";
        $html .="fill:url(#".$id.");}";
        $html .="</style>";

        //svg
        $html .="<svg>";
        $html .="<defs>";
        $html .="<pattern id ='".$id."'width='100%' height='100%' x='4%' y='-2%' patternUnits='objectBoundingBox' patternTransform='scale(0.999)'>";
        $html .="<image href='".$iconUrl['icon_url']."'width='50' height='50'>";
        $html .="</svg>";
      }
      $html .= "</div>";
      return $html;
    }

    add_shortcode('mr_SVG','getSvg');
