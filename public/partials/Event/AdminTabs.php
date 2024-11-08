<?php

class AdminTabs{

  public static function getAdminTabsHtml($eventID){
    $showOptions = ShowOptionsController::getShowOptions(LocationHelper::getIDFromEventPostID($eventID), new ShowOptionsService(), new ShowOptionsRepository());

    $html = "<div class = 'adminTabs'>";
    $locationSecretaries = LocationSecretariesService::getLocationSecretaries(LocationHelper::getIDFromEventPostID($eventID));
    $eventJudges = new EventJudges($eventID);
    if(($showOptions->allowOnlineRegistrations && is_user_logged_in()) && ((in_array(wp_get_current_user()->display_name, $locationSecretaries) || in_array(wp_get_current_user()->display_name, $eventJudges->judgeNames)) || current_user_can('administrator'))){
      $html .= "<ul class='tabbed-summary' id='admin-tabs'>";
      if((in_array(wp_get_current_user()->display_name, $locationSecretaries)) || current_user_can('administrator')){
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
      $empty = "";
      if((in_array(wp_get_current_user()->display_name, $locationSecretaries)) || current_user_can('administrator')){
        $html .= "<div class = 'fancierEntries content'>".FancierEntriesView::getFancierEntriesHtml($eventID)."</div>
                  <div class = 'label content' style = 'display : none'>".LabelView::getHtml($eventID)."</div>
                  <div class = 'entrySummary content' style = 'display : none'>".EntrySummaryView::getEntrySummaryHtml($eventID)."</div>
                  <div class = 'judgingSheets content' style = 'display : none'>".JudgingSheetsView::getHtml($eventID)."</div>
                  <div class = 'entryBook content' style = 'display : none'>".EntryBookView::getEntryBookHtml($eventID)."</div>
                  <div class = 'absentees content' style = 'display : none'>".AbsenteesView::getHtml($eventID)."</div>
                  <div class = 'prizeCards content' style = 'display : none'>".PrizeCardsView::getHtml($eventID)."</div>";
      }
      $html .= "<div class = 'judgesReport content' style = 'display: none'>".JudgesReportView::getHtml($eventID)."</div>";
    }
    $html .= "</div>";

    return $html;
  }
}
