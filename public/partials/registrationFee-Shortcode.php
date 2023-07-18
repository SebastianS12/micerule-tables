<?php
function showRegistrationFee(){
  global $post;
  $eventOptionalSettings = EventOptionalSettings::create(EventProperties::getEventLocationID($post->ID));//get_post_meta($post->ID, 'micerule_data_event_optional_settings', true);

  $html = "<div class='entry-fee-wrapper'>";
  $html .= "<h1 class='blockentry' style= 'font-family: Bree Serif; color: #926939; display: flex; vertical-align: middle; text-align: center; font-size: 46px; font-weight: bold'>";
  $html .= "£".$eventOptionalSettings->registrationFee;
  $html .= "</h1>";
  $html .= "</div>";
  $html .= "<h3 style='top: 2px; position: relative;'>Prize Money: ";

  $numberFormatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
  $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
  foreach($eventOptionalSettings->prizeMoney as $prizeMoney){
    if($prizeMoney != ""){
      $html .= ($prizeMoney >= 1.0) ? $numberFormatter->formatCurrency((float)$prizeMoney, "GBP").", " : ($prizeMoney * 100)."p, ";
    }
  }
  //remove last comma
  $html = rtrim($html, ", ");
  $html .= "</h3>";

  return $html;
}

add_shortcode('registrationFee', 'showRegistrationFee');
