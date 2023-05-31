<?php

class LocationSectionTables{
  public $locationID;

  public function __construct($locationID){
    $this->locationID = $locationID;
  }

  public function getSectionTablesHtml($eventClasses, $locationSecretaries, $locationOptionalSettings){
    $html = "<div id='locationSectionTables'>";
    $html .= "<input type='hidden' id='locationID' value=".$this->locationID.">";
    if(is_user_logged_in() && (in_array(wp_get_current_user()->display_name ,$locationSecretaries['name']) || current_user_can('administrator'))){
      $html .= "<div class='showsec-options'>";
      $html .= "<h3>SHOW OPTIONS</h3>";
      //enable online registrations checkbox
      $html .= "<div class='schedule-option'>";
      $html .= "<input type = 'checkbox' class = 'optionalSettings' id = 'enableOnlineRegistrations' ".(($locationOptionalSettings->allowOnlineRegistrations) ? 'checked' : '')."><label for = 'enableOnlineRegistrations'>Enable Online Registrations</label>";
      $html .= "</div>";

      $html .= "<div class = 'registration-fee-option'>";
      $html .= "<label for='registrationFeeInput'>Registration Fee</label>";
      $html .= "<input type = 'number' min = '0' step= '0.01' value = '".$locationOptionalSettings->registrationFee."' class = 'optionalSettings' id = 'registrationFeeInput'></input>";
      $html .= "</div>";

      $html .= "<div class = 'prize-money-option'>";
      $html .= "<label for='prizeMoney-firstPlace'>Prize Money First Place</label>";
      $html .= "<input type = 'number' min = '0' step= '0.1' value = '".$locationOptionalSettings->prizeMoney['firstPrize']."' class = 'optionalSettings' id = 'prizeMoney-firstPlace'></input>";
      $html .= "</div>";

      $html .= "<div class = 'prize-money-option'>";
      $html .= "<label for='prizeMoney-secondPlace'>Prize Money Second Place</label>";
      $html .= "<input type = 'number' min = '0' step= '0.1' value = '".$locationOptionalSettings->prizeMoney['secondPrize']."' class = 'optionalSettings' id = 'prizeMoney-secondPlace'></input>";
      $html .= "</div>";

      $html .= "<div class = 'prize-money-option'>";
      $html .= "<label for='prizeMoney-thirdPlace'>Prize Money Third Place</label>";
      $html .= "<input type = 'number' min = '0' step= '0.1' value = '".$locationOptionalSettings->prizeMoney['thirdPrize']."' class = 'optionalSettings' id = 'prizeMoney-thirdPlace'></input>";
      $html .= "</div>";

      //unstandardised + junior + auction checkboxes
      $html .= "<div class='schedule-option'>";
      $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Unstandardised' ".(($locationOptionalSettings->optionalClasses['unstandardised']) ? 'checked' : '')."><label for = 'allow-Unstandardised'>Allow Unstandardised</label>";
      $html .= "</div>";

      $html .= "<div class='schedule-option'>";
      $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Junior' ".(($locationOptionalSettings->optionalClasses['junior']) ? 'checked' : '')."><label for = 'allow-Junior'>Allow Junior</label>";
      $html .= "</div>";

      $html .= "<div class='schedule-option'>";
      $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Auction' ".(($locationOptionalSettings->optionalClasses['auction']) ? 'checked' : '')."><label for = 'allow-Auction'>Allow Auction</label>";
      $html .= "</div>";

      $html .= "</div>";
    }

    $html .= "<div class='classes-wrapper'>";
    $challengeNames = EventProperties::CHALLENGENAMES;
    foreach(EventProperties::SECTIONNAMES as $index => $sectionName){
      $sectionNameLower = strtolower($sectionName);
      $html .= "<div class='show-section'>";
      $html .= "<h3>".$sectionName."</h3>";
      $html .= "<table id = 'table".$sectionNameLower."-location'>";
      $html .= "<tbody>";
      $html .= "<tr>Ad       u8</tr>";
        foreach($eventClasses->getSectionClasses($sectionNameLower) as $position => $className){
          $html .= "<tr class = 'classRow-location' id = '".$className."-tr-location'>";
          $html .= "<td class = 'positionCell'>".$eventClasses->getClassIndex($className, "Ad")."/".$eventClasses->getClassIndex($className, "U8")."</td>";
          $html .= "<td class = 'classNameCell'>".$className."</td>";

          if(is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaries['name']) || current_user_can('administrator'))){
            $html .= "<td class='class-order'><button type = 'button' class = 'moveClassButton ".(($position > 0) ? 'active' :'')."' id = '".$sectionNameLower."&-&".$className."&-&".$position."&-&moveUp'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/up.svg'></button>";
            $html .= "<button type = 'button' class = 'moveClassButton ".(($position < $eventClasses->getSectionClassCount($sectionNameLower) - 1 && $position + 1 < $eventClasses->getSectionClassCount($sectionNameLower)) ? 'active' :'')."'  id = '".$sectionNameLower."&-&".$className."&-&".$position."&-&moveDown'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/down.svg'></button></td>";
            $html .= "<td class='class-delete'><button type = 'button' class = 'deleteClassButton' id = '".$sectionNameLower."&-&".$className."&-&".$position."&-&delete'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></td>";
          }
          $html .= "</tr>";
        }

      //add challenge row
      $html .= "<tr class = 'classRow-location' id = '".$challengeNames[$sectionNameLower]."-tr-location'>";
      $html .= "<td class = 'positionCell ad'>".$eventClasses->getClassIndex($challengeNames[$sectionNameLower], "Ad")."/".$eventClasses->getClassIndex($challengeNames[$sectionNameLower], "U8")."</td>";
      $html .= "<td class = 'classNameCell'><span>".$challengeNames[$sectionNameLower]."</span></td>";
      $html .= "<td></td><td></td>";
      $html .= "</tr>";

      $html .= "</tbody>";
      $html .= "</table>";
      if(is_user_logged_in() && (in_array(wp_get_current_user()->display_name ,$locationSecretaries['name']) || current_user_can('administrator'))){
        $html .= "<button type = 'button' id = '".$sectionNameLower."AddButton' class = 'addBreedButton'>Add Class</button>";
      }
      $html .= "</div>";
    }

    $html .= $this->getOptionalClasses($eventClasses, $locationSecretaries);

    $html .= "</div>";
    $html .= "</div>";

    return $html;
  }

  function getOptionalClasses($eventClasses, $locationSecretaries){
    $html = "<div class='show-section'>";
    $html .= "<h3>GRAND CHALLENGE</h3>";
    $html .= "<table id = 'tableoptional-location'>";
    $html .= "<tbody>";
    $html .= "<tr>Ad       u8</tr>";
    $html .= "<tr class = 'classRow-location' id = 'GRAND CHALLENGE-tr-location'>";
    $html .= "<td class = 'positionCell ad'>".($eventClasses->getClassIndex("GRAND CHALLENGE", "Ad"))."/".($eventClasses->getClassIndex("GRAND CHALLENGE", "U8"))."</td>";
    $html .= "<td class = 'classNameCell'><span>GRAND CHALLENGE</span></td>";
    $html .= "</tr>";
    foreach($eventClasses->optionalClasses as $position=> $className){
      $html .= "<tr class = 'classRow-location' id = '".$className."-tr-location'>";
      $html .= "<td class = 'positionCell'>".$eventClasses->getClassIndex($className, "AA")."</td>";
      $html .= "<td class = 'classNameCell'>".$className."</td>";

      if(is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaries['name']) || current_user_can('administrator'))){
        $html .= "<td class='class-order'><button type = 'button' class = 'moveClassButton ".(($position > 0) ? 'active' :'')."' id = 'optional&-&".$className."&-&".$position."&-&moveUp'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/up.svg'></button>";
        $html .= "<button type = 'button' class = 'moveClassButton ".(($position < count($eventClasses->optionalClasses)-1) ? 'active' :'')."'  id = 'optional&-&".$className."&-&".$position."&-&moveDown'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/down.svg'></button></td>";
        $html .= "<td class = 'class-delete' id = '".$position."&-&delete'></td>";
      }
      $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";

    return $html;
  }
}

function sectionTables($atts){
  global $post;
  global $wpdb;

  $sectionData_atts = shortcode_atts(array(
    'id' => ''
  ), $atts);

  $eventClasses = EventClasses::create($sectionData_atts['id']);
  $locationSecretaries = EventProperties::getLocationSecretaries($sectionData_atts['id']);//get_post_meta($locationSecretaryID, 'micerule_data_location_secretaries',true);
  $locationOptionalSettings = EventOptionalSettings::create($sectionData_atts['id']);//get_post_meta(v, 'micerule_data_location_optional_settings', true);

  $locationSectionTables = new LocationSectionTables($sectionData_atts['id']);
  return $locationSectionTables->getSectionTablesHtml($eventClasses, $locationSecretaries, $locationOptionalSettings);
}

add_shortcode('sectionTables','sectionTables');
