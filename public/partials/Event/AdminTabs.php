<?php

class AdminTabs{

  public static function getAdminTabsHtml($eventID){
    /*$tabsHtml = array();

    $tabsHtml["Label"] = $this->getLabelHtml();
    $tabsHtml["EntrySummary"] = $this->getEntrySummaryHtml();
    $tabsHtml["Judging Sheets"] = $this->getJudgingSheetsHtml();
    $tabsHtml["Entry Book"] = $this->getEntryBookHtml();
    $tabsHtml["Absentees"] = $this->getAbsenteesHtml();
    $tabsHtml["Prize Cards"] = $this->getPrizeCardsHtml();
    $tabsHtml["Judges Reports"] = $this->getJudgesReportsHtml();

    return json_encode($tabsHtml);*/

    $fancierEntries = new FancierEntries($eventID);
    $label = new Label($eventID);
    $entrySummary = new EntrySummary($eventID);
    $judgingSheets = new JudgingSheets($eventID);
    $entryBook = new EntryBook($eventID);
    $absentees = new Absentees($eventID);
    $prizeCards = new PriceCards($eventID);
    $judgesReports = new JudgesReport($eventID);

    $html = "<div class = 'adminTabs'>";

    $locationSecretaries = EventProperties::getLocationSecretaries(EventProperties::getEventLocationID($eventID));
    $eventJudges = new EventJudges($eventID);
    if(is_user_logged_in() && ((in_array(wp_get_current_user()->display_name, $locationSecretaries['name']) || in_array(wp_get_current_user()->display_name, $eventJudges->judgeNames)) || current_user_can('administrator'))){
      $html .= "<ul class='tabbed-summary' id='admin-tabs'>";
      if(is_user_logged_in() && ((in_array(wp_get_current_user()->display_name, $locationSecretaries['name'])) || current_user_can('administrator'))){
        $html .= " <li class = 'fancierEntries tab active' style='height: 26px;'>Entries per Fancier</li>
                   <li class = 'label tab' style='height: 26px;'>Label</li>
                   <li class = 'entrySummary tab' style='height: 26px;'>Entry Summary</li>
                   <li class = 'judgingSheets tab' style='height: 26px;'>Judging Sheets</li>
                   <li class = 'entryBook tab' style='height: 26px;'>Entry Book</li>
                   <li class = 'absentees tab' style='height: 26px;'>Absentees</li>
                   <li class = 'prizeCards tab' style='height: 26px;'>Prize Cards</li>";
      }
      $html .= " <li class = 'judgesReport tab' style='height: 26px;'>Judge's Report</li>
              </ul>";
      if(is_user_logged_in() && ((in_array(wp_get_current_user()->display_name, $locationSecretaries['name'])) || current_user_can('administrator'))){
        $html .= "<div class = 'fancierEntries content'>".$fancierEntries->getHtml()."</div>
                  <div class = 'label content' style = 'display : none'>".$label->getHtml()."</div>
                  <div class = 'entrySummary content' style = 'display : none'>".$entrySummary->getHtml()."</div>
                  <div class = 'judgingSheets content' style = 'display : none'>".$judgingSheets->getHtml()."</div>
                  <div class = 'entryBook content' style = 'display : none'>".$entryBook->getHtml()."</div>
                  <div class = 'absentees content' style = 'display : none'>".$absentees->getHtml()."</div>
                  <div class = 'prizeCards content' style = 'display : none'>".$prizeCards->getHtml()."</div>";
      }
      $html .= "<div class = 'judgesReport content' style = 'display: none'>".$judgesReports->getHtml()."</div>";
    }
    $html .= "</div>";

    return $html;
  }
}
