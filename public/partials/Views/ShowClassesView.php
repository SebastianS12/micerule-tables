<?php

class ShowClassesView{
    public static function render(int $locationID): string
    {
        $viewModel = ShowClassesController::prepareViewModel($locationID, new ShowClassesService());
        $html = "<div class='classes-wrapper'>";
        foreach ($viewModel->standardClasses as $sectionName => $sectionClassesData) {
            $section = Section::from($sectionName);
            $html .= "<div class='show-section'>";
            $html .= "<h3>" .$section->getDisplayString(). "</h3>";
            $html .= "<table id = 'table" . $section->value . "-location'>";
            $html .= "<tbody>";
            $html .= "<tr>Ad       u8</tr>";
            foreach ($sectionClassesData as $sectionPosition => $classData) {
                $html .= "<tr class = 'classRow-location' id = '" . $classData['className'] . "-tr-location' data-class-id = ".$classData['classID'].">";
                $html .= "<td class = 'positionCell'>" .$classData['adIndex']. "/" .$classData['u8Index']. "</td>";
                $html .= "<td class = 'classNameCell'>" .$classData['className']. "</td>";

                if ($viewModel->canEditShowClasses) {
                    $html .= "<td class='class-order'><button type = 'button' class = 'moveClassButton " . (($sectionPosition > 0) ? 'active' : '') . "' data-move-action = 'moveUp' data-class-id = ".$classData['classID']."><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/up.svg'></button>";
                    $html .= "<button type = 'button' class = 'moveClassButton " . (($sectionPosition < count($sectionClassesData) - 1 && $sectionPosition + 1 < count($sectionClassesData)) ? 'active' : '') . "'  data-move-action = 'moveDown' data-class-id = ".$classData['classID']."><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/down.svg'></button></td>";
                    $html .= "<td class='class-delete'><button type = 'button' class = 'deleteClassButton' data-class-id = ".$classData['classID']." data-section = ".$sectionName."><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></td>";
                }
                $html .= "</tr>";
            }

            //add challenge row
            $challengeData = $viewModel->challenges[$section->value];
            $html .= self::getChallengeRowHtml($challengeData);
            
            $html .= "</tbody>";
            $html .= "</table>";
            if ($viewModel->canEditShowClasses) {
                $html .= "<button type = 'button' id = '" . $section->value . "AddButton' class = 'addBreedButton'>Add Class</button>";
            }
            $html .= "</div>";
        }
        $html .= self::getShowOptionalClassesHtml($viewModel);
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    private static function getShowOptionalClassesHtml(ShowClassesViewModel $viewModel){
        $html = "<div class='show-section'>";

        
        $html .= "<h3>GRAND CHALLENGE</h3>";
        $html .= "<table id = 'tableoptional-location'>";
        $html .= "<tbody>";
        $html .= "<tr>Ad       u8</tr>";
        $html .= self::getChallengeRowHtml($viewModel->challenges[Section::GRAND_CHALLENGE->value]);
        foreach($viewModel->optionalClasses as $sectionPosition => $classData){
            $html .= "<tr class = 'classRow-location' id = '".$classData['className']."-tr-location' data-class-id = ".$classData['classID'].">";
            $html .= "<td class = 'positionCell'>".$classData['index']."</td>";
            $html .= "<td class = 'classNameCell'>".$classData['className']."</td>";

            if($viewModel->canEditShowClasses){
                $html .= "<td class='class-order'><button type = 'button' class = 'moveClassButton " . (($sectionPosition > 0) ? 'active' : '') . "' data-move-action = 'moveUp' data-class-id = ".$classData['classID']."><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/up.svg'></button>";
                $html .= "<button type = 'button' class = 'moveClassButton " . (($sectionPosition < count($viewModel->optionalClasses) - 1 && $sectionPosition + 1 < count($viewModel->optionalClasses)) ? 'active' : '') . "' data-move-action = 'moveDown' data-class-id = ".$classData['classID']."><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/down.svg'></button></td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";

        return $html;
    }

    private static function getChallengeRowHtml(?array $challengeData): string
    {
        $html = "";
        if($challengeData !== null){
            $html .= "<tr class = 'classRow-location' id = '" . $challengeData['challengeName'] . "-tr-location'>";
            $html .= "<td class = 'positionCell ad'>" .$challengeData['adIndex'] . "/" .$challengeData['u8Index'] . "</td>";
            $html .= "<td class = 'classNameCell'><span>" . $challengeData['challengeName'] . "</span></td>";
            $html .= "<td></td><td></td>";
            $html .= "</tr>";
        }

        return $html;
    }
}