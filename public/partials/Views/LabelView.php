<?php

class LabelView
{
    public static function getHtml(int $eventPostID)
    {
        $labelService = new LabelService($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $viewModel = $labelService->prepareViewModel();
        $html = "<div class = 'label content' style = 'display : none'>";
        $html .= "<div class='print-tray-header'><h3>Labels Print Preview<span class='print-alert'>On Mac, these must be printed from Safari</span></h3><a class='print-button'><img src='/wp-content/plugins/micerule-tables/admin/svg/print.svg'></a></div>";
        $html .= "<div class = 'printLabels'>";
        foreach ($viewModel->userLabels as $userName => $userLabelData) {
            $html .= "<div class = 'label-block'>";
            $html .= "<ul class='card-wrapper'>";
            $html .= "<li class='pen-label fancier-name-label'>
                  <div>
                    <span>" . $userName . "</span>
                  </div>
                </li>";
            foreach ($userLabelData as $userLabel) {
                    $absentClass = ($userLabel['absent']) ? "absent" : "";
                    $html .= self::getUserLabelHtml($absentClass, $userLabel['classIndex'], $userLabel['penNumber'], $userLabel['className'], $userLabel['age'], $userLabel['section']);
            }
            $html .= "</ul>";
            $html .= "</div>";
        }
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    private static function getUserLabelHtml(string $absentClass, int $classIndex, int $penNumber, string $className, string $age, string $section){
      $html = "<li class='pen-label " . $absentClass . "'>
                <div class='label-class'>
                  <span class='label-header'>CLASS</span>
                  <div class='label-class-no'>
                    <span class='label-no'>".$classIndex."</span>
                    <span class='label-class-age'>".$age."</span>
                  </div>
                    <span class='label-class-desc'>".$className."</span>
                </div>
                <div class='label-pen'>
                  <span class='label-header'>PEN</span>
                  <span  class='label-pen-no'>" .$penNumber. "</span>
                  <span class='label-section'>".$section."</span>
                </div>
              </li>";

      return $html;
    }
}
