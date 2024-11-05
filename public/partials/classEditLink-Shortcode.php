<?php

function classEditLink($atts){
  global $post;
  global $wpdb;

  $locationSecretaries = LocationSecretariesService::getLocationSecretaries(LocationHelper::getIDFromEventPostID($post->ID));//get_post_meta($locationPostID, 'micerule_data_location_secretaries',true);
  $classEditLink = EventProperties::getClassEditLink($post->ID);

  $locationSecretaryNameString = "";
  foreach($locationSecretaries as $locationSecretaryName){
      $locationSecretaryNameString .= $locationSecretaryName.", ";
  }

  $html = "";
  if(is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaries) || current_user_can('administrator'))){
    $html .= "<div id = 'show-sec-message'>";
    $html .= "<p>".$locationSecretaryNameString." you can edit your classes <a href='".$classEditLink."'>here</a>. Only you can see this message</p>";
    $html .= "</div>";
  }

  return $html;
}

add_shortcode('classEditLink','classEditLink');
