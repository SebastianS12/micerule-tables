<?php

class AbsenteesView
{
    public static function getHtml($eventPostID)
    {
        $judgesModel = new EventJudgesHelper();

        $html = "<div class = 'absentees content' style = 'display : none'>";

        foreach ($judgesModel->getEventJudgeNames($eventPostID) as $judgeName) {
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
            foreach (AbsenteesModel::getAbsentees($eventPostID, $judgeName) as $absenteeData) {
                $html .= "<tr>
                                <td class='abs-class'>" . $absenteeData['class_index']. "</td>
                                <td>" . $absenteeData['pen_number'] . "</td>
                             </tr>";
            }
            $html .=      "</tbody>
                    </table>
                  </div>";
        }

        $html .= "</div>";

        return $html;
    }
}
