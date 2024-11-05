<?php

class AbsenteesView
{
    public static function getHtml($eventPostID)
    {
        $absenteesService = new AbsenteesService($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $viewModel = $absenteesService->prepareViewModel();

        $html = "<div class = 'absentees content' style = 'display : none'>";

        foreach ($viewModel->absentees as $judgeName => $judgeAbsentees) {
            $html .= "<div class='absentees-summary'>
                        <table>
                            <thead>
                            <tr>
                                <th class='judge-absentees' colspan = 2>Judge:<br> " . $judgeName . "</th>
                            </tr>
                            <tr>
                                <th>Class</th>
                                <th>Pen №</th>
                            </tr>
                            </thead>
                            <tbody>";
            foreach ($judgeAbsentees as $classIndex => $absenteePenNumbers) {
                foreach($absenteePenNumbers as $absenteePenNumber){
                    $html .= "<tr>
                                <td class='abs-class'>" . $classIndex. "</td>
                                <td>" . $absenteePenNumber . "</td>
                             </tr>";
                } 
            }
            $html .=      "</tbody>
                    </table>
                  </div>";
        }

        $html .= "</div>";

        return $html;
    }
}
