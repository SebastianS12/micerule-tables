<?php

function sectionTablesFrontend($atts)
{
  global $post;

  $user = wp_get_current_user();
  $userName = $user->display_name;

  $locationID = LocationHelper::getIDFromEventPostID($post->ID);

  $html = "<div id='eventSectionTables'>";

  //$html .= "<p>".var_export(EntryBookData::create($post->ID), true)."</p>";

  $html .= RegistrationTablesView::getRegistrationTablesHtml($post->ID, $userName);//$registrationTables->getHtml();
  $html .= "</div>";
  $html .= "<div class = 'header-info'>";
  $showOptionsService = new ShowOptionsService();
  $eventOptionalSettings = $showOptionsService->getShowOptions(new ShowOptionsRepository(), $locationID);
  if($eventOptionalSettings->allow_online_registrations){
    $entryCountRepository = new RegistrationCountRepository($post->ID, LocationHelper::getIDFromEventPostID($post->ID));
    $html .= "<h3>Total Exhibits: ".$entryCountRepository->getExhibitCount()."</h3>";
  }
  $html .= "<hr>";
  $html .= "</div>";

  if ($eventOptionalSettings->allow_online_registrations && current_user_can('administrator')) {
    $html .= "<div class = 'show-report-gen'><button type ='button' id = 'create-show-post'>Create Show Report</button>";
    $showReportPostID = get_post_meta($post->ID, "show_report_post_id", true);
    if ($showReportPostID != "")
      $html .= "<a href = '" . get_post_permalink($showReportPostID) . "'>Show Report Draft</a>";
    $html .= "</div>";
  }

  $adminTabsController = new AdminTabsController();
  $html .= $adminTabsController->getViewHtml(EventHelper::getEventPostID());

  $html .= "<div id = 'spinner-div' style = 'display:none'><div class = 'spinner-wrapper'><img src = '" . get_stylesheet_directory_uri() . "/Assets/mouse-loader.svg'></div></div>";

  return $html;
}

add_shortcode('sectionTablesFrontend', 'sectionTablesFrontend');


function addClassToShowCalendar($output, $event){
  if(is_page(2862)){
    $eventOptionalSettings = ShowOptionsController::getShowOptions($event->location_id, new ShowOptionsService(), new ShowOptionsRepository());
    if($eventOptionalSettings->allow_online_registrations){
      $output = "<div class = 'online-registration'>".$output."</div>";
    }
  }

  return $output;
}

add_filter('em_event_output', 'addClassToShowCalendar', 3, 3);
