<?php

function sectionTablesFrontend($atts)
{
  global $post;
  global $wpdb;

  $user = wp_get_current_user();
  $userName = $user->display_name;

  $sectionNames = EventProperties::SECTIONNAMES;
  $challengeNames = EventProperties::CHALLENGENAMES;

  $locationID = EventProperties::getEventLocationID($post->ID);
  $eventRegistrationData = EventRegistrationData::create($post->ID); //get_post_meta($post->ID, 'micerule_data_event_class_registrations', true);
  $eventOptionalSettings = EventOptionalSettings::create($locationID);

  $html = "<div id='eventSectionTables'>";

  //$html .= "<p>".var_export(EntryBookData::create($post->ID), true)."</p>";

  $registrationTables = new RegistrationTables($post->ID, $userName);
  $html .= $registrationTables->getHtml();
  $html .= "</div>";
  $html .= "<div class = 'header-info'>";
  if ($eventOptionalSettings->allowOnlineRegistrations) {
    $html .= "<h3>Total Entries: " . $eventRegistrationData->getEntryCount() . "</h3>";
    $html .= "<h3>Total Exhibits: " . $eventRegistrationData->getExhibitCount() . "</h3>";
  }
  $html .= "<hr>";
  $html .= "</div>";

  if ($eventOptionalSettings->allowOnlineRegistrations && current_user_can('administrator')) {
    $html .= "<button type ='button' id = 'create-show-post'>Create Show Report</button>";
    $showReportPostID = get_post_meta($post->ID, "show_report_post_id", true);
    if (isset($showReportPostID))
      $html .= "<div class = 'show-report-gen'><a href = '" . get_post_permalink($showReportPostID) . "'>Show Report Draft</a></div>";
  }

  $html .= AdminTabs::getAdminTabsHtml($post->ID);

  $html .= "<div id = 'spinner-div' style = 'display:none'><div class = 'spinner-wrapper'><img src = '" . get_stylesheet_directory_uri() . "/Assets/mouse-loader.svg'></div></div>";

  return $html;
}

add_shortcode('sectionTablesFrontend', 'sectionTablesFrontend');


function addClassToShowCalendar($output, $event)
{
  if (is_page(2862)) {
    $eventOptionalSettings = EventOptionalSettings::create($event->location_id);
    if ($eventOptionalSettings->allowOnlineRegistrations) {
      $output = "<div class = 'online-registration'>" . $output . "</div>";
    }
  }

  return $output;
}

add_filter('em_event_output', 'addClassToShowCalendar', 3, 3);
