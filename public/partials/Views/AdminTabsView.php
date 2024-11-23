<?php

class AdminTabsView{
    public static function render(AdminTabsViewModel $viewModel): string
    {
        $html = "<div class = 'adminTabs'>";
        if($viewModel->canViewShowReportTab){
            $html .= "<ul class='tabbed-summary' id='admin-tabs'>";
            if($viewModel->canViewAllTabs){
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
            if($viewModel->canViewAllTabs){
                $html .= "<div class = 'fancierEntries content'>".FancierEntriesView::getFancierEntriesHtml($viewModel->eventPostID)."</div>
                        <div class = 'label content' style = 'display : none'>".LabelView::getHtml($viewModel->eventPostID)."</div>
                        <div class = 'entrySummary content' style = 'display : none'>".EntrySummaryView::getEntrySummaryHtml($viewModel->eventPostID)."</div>
                        <div class = 'judgingSheets content' style = 'display : none'>".JudgingSheetsView::getHtml($viewModel->eventPostID)."</div>
                        <div class = 'entryBook content' style = 'display : none'>".EntryBookView::getEntryBookHtml($viewModel->eventPostID)."</div>
                        <div class = 'absentees content' style = 'display : none'>".AbsenteesView::getHtml($viewModel->eventPostID)."</div>
                        <div class = 'prizeCards content' style = 'display : none'>".PrizeCardsView::getHtml($viewModel->eventPostID)."</div>";
            }
            $html .= "<div class = 'judgesReport content' style = 'display: none'>".JudgesReportView::getHtml($viewModel->eventPostID)."</div>";
        }
        $html .= "</div>";

        return $html;
    }
}