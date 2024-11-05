<?php

function sectionTablesFrontend($atts){
  global $post;
  global $wpdb;

  $user = wp_get_current_user();
  $userName = $user->display_name;

  $sectionNames = EventProperties::SECTIONNAMES;
  $challengeNames = EventProperties::CHALLENGENAMES;

  $locationID = LocationHelper::getIDFromEventPostID($post->ID);
  $eventRegistrationData = EventRegistrationData::create($post->ID);//get_post_meta($post->ID, 'micerule_data_event_class_registrations', true);

  $html = "<div id='eventSectionTables'>";

  //$html .= "<p>".var_export(EntryBookData::create($post->ID), true)."</p>";

  $html .= RegistrationTablesView::getRegistrationTablesHtml($post->ID, $userName);//$registrationTables->getHtml();
  $html .= "</div>";
  $html .= "<div class = 'header-info'>";
  $showOptionsService = new ShowOptionsService();
  $eventOptionalSettings = $showOptionsService->getShowOptions(new ShowOptionsRepository(), $locationID);
  if($eventOptionalSettings->allowOnlineRegistrations){
    $entryCountRepository = new RegistrationCountRepository($post->ID, LocationHelper::getIDFromEventPostID($post->ID));
    $html .= "<h3>Total Exhibits: ".$entryCountRepository->getExhibitCount()."</h3>";
  }
  $html .= "<hr>";
  $html .= "</div>";

  $html .= ($eventOptionalSettings->allowOnlineRegistrations && current_user_can('administrator')) ? "<button type ='button' id = 'create-show-post'>Create Show Report</button>" : "";

  $html .= AdminTabs::getAdminTabsHtml($post->ID);

  $html .= "<div id = 'spinner-div' style = 'display:none'><div class = 'spinner-wrapper'><img src = '".get_stylesheet_directory_uri()."/Assets/mouse-loader.svg'></div></div>";

  return $html;
}

add_shortcode('sectionTablesFrontend','sectionTablesFrontend');


function addClassToShowCalendar($output, $event){
  if(is_page(2862)){
    $eventOptionalSettings = ShowOptionsController::getShowOptions($event->location_id, new ShowOptionsService(), new ShowOptionsRepository());
    if($eventOptionalSettings->allowOnlineRegistrations){
      $output = "<div class = 'online-registration'>".$output."</div>";
    }
  }

  return $output;
}

add_filter('em_event_output', 'addClassToShowCalendar', 3, 3);
