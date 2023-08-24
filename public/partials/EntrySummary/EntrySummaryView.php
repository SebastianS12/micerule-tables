<?php

class EntrySummaryView
{
    public static function getEntrySummaryHtml($eventPostID)
    {
        $entrySummaryData = EntrySummaryController::getEntrySummaryData($eventPostID);

        $html = "<div class = 'entrySummary content'>";
        foreach ($entrySummaryData as $userName => $fancierEntrySummary) {
            $checkBoxState = ""; //TODO:($this->entryData->allAbsent($userName)) ? "checked" : "";
            $html .= "<div class='fancier-entry-summary'>
                    <div class='set-absent'>
                        <input type = 'checkbox' id = 'setAllAbsent'  class = 'setAllAbsent' name = 'setAllAbsent' " . $checkBoxState . ">
                        <label for = 'setAllAbsent'>Set all absent</label>
                    </div>
                    <div class='table-wrapper'>
                    <table>
                        <thead class='header-wrapper'>
                        <tr>
                            <th colspan=3><p>" . $userName . "</p></th>
                        </tr>
                        <tr>
                            <th class='js-pen-no'>№</th>
                            <th class='js-notes'>Class</th>
                        </tr>
                        </thead>
                        <tbody>";

            foreach ($fancierEntrySummary as $fancierEntry) {
                $html .= "<tr>
                        <td class='js-pen-no'>" . $fancierEntry['pen_number'] . "</td>
                        <td class='js-notes'>" . $fancierEntry['class_index'] . " | " . $fancierEntry['class_name'] . " " . $fancierEntry['age'] . "</td>
                    </tr>";
            }
            $html .= "<tr><td colspan = 2>Entry Fee: " .EntrySummaryController::getRegistrationFee($eventPostID, $userName). "£</td></tr>";

            $html .= "    </tbody>
                    </table>
                    </div>
                </div>";
        }
        $html .= "  </div>";

        return $html;
    }
}
