<?php

class LabelView
{
    public static function getHtml($eventPostID)
    {
        $labelModel = new LabelModel();
        $html = "<div class = 'label content' style = 'display : none'>";
        $html .= "<div class='print-tray-header'><h3>Labels Print Preview<span class='print-alert'>On Mac, these must be printed from Safari</span></h3><a class='print-button'><img src='/wp-content/plugins/micerule-tables/admin/svg/print.svg'></a></div>";
        $html .= "<div class = 'printLabels'>";
        foreach ($labelModel->getLabelData($eventPostID) as $userName => $userLabelData) {
            $html .= "<div class = 'label-block'>";
            $html .= "<ul class='card-wrapper'>";
            $html .= "<li class='pen-label fancier-name-label'>
                  <div>
                    <span>" . $userName . "</span>
                  </div>
                </li>";
            foreach ($userLabelData as $userLabel) {
                    $absentClass = ($userLabel['absent']) ? "absent" : "";
                    $html .= "<li class='pen-label " . $absentClass . "'>
                      <div class='label-class'>
                        <span class='label-header'>CLASS</span>
                        <span class='label-class-no'>" . $userLabel['class_index']. "</span>
                      </div>
                      <div class='label-pen'>
                        <span class='label-header'>PEN</span>
                        <span  class='label-pen-no'>" . $userLabel['pen_number'] . "</span>
                      </div>
                    </li>";
            }
            $html .= "</ul>";
            $html .= "</div>";
        }
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}
