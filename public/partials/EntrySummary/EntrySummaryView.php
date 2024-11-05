<?php

class EntrySummaryView
{
    public static function getEntrySummaryHtml(int $eventPostID)
    {
        $entrySummaryService = new EntrySummaryService($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $viewModel = $entrySummaryService->prepareViewModel();

        $html = "<div class = 'entrySummary content'>";
        foreach ($viewModel->fancierEntrySummaries as $userName => $fancierEntrySummary) {
            $checkBoxState = $fancierEntrySummary['allEntriesAbsent'] ? "checked" : "";
            $html .= "<div class='fancier-entry-summary'>
                    <div class='set-absent'>
                        <input type = 'checkbox' id = 'setAllAbsent-".$userName."'  class = 'setAllAbsent' name = 'setAllAbsent' " . $checkBoxState . ">
                        <label for = 'setAllAbsent-".$userName."'>Set all absent</label>
                    </div>
                    <div class='table-wrapper'>
                    <table>
                        <thead class='header-wrapper'>
                        <tr>
                            <th colspan=3><p class = 'fancier-name'>" . $userName . "</p></th>
                        </tr>
                        <tr>
                            <th class='js-pen-no'>№</th>
                            <th class='js-notes'>Class</th>
                        </tr>
                        </thead>
                        <tbody>";

            foreach ($fancierEntrySummary['entries'] as $fancierEntry) {
                $html .= "<tr>
                        <td class='js-pen-no'>" . $fancierEntry['penNumber'] . "</td>
                        <td class='js-notes'>" . $fancierEntry['classIndex'] . " | " . $fancierEntry['className'] . " " . $fancierEntry['age'] . "</td>
                    </tr>";
            }
            $html .= "<tr><td colspan = 2>Entry Fee: £" .$fancierEntrySummary['registrationFee']. "</td></tr>";

            $html .= "    </tbody>
                    </table>
                    </div>
                </div>";
        }
        $html .= "  </div>";

        return $html;
    }
}
