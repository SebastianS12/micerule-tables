<?php

class ShowOptionsView
{
    public static function getSectionTablesHtml($locationID){
        $html = "<div id='locationSectionTables'>";
        $html .= "<input type='hidden' id='locationID' value=".$locationID.">";
        $html .= self::getShowOptionsHtml($locationID);
        $html .= self::getShowClassesHtml($locationID);
        $html .= "</div>";

        return $html;
    }

    private static function getShowOptionsHtml($locationID)
    {
        $showOptions = ShowOptionsController::getShowOptions($locationID);
        $html = "<div class='showsec-options'>";
        $html .= "<h3>SHOW OPTIONS</h3>";
        //enable online registrations checkbox
        $html .= "<div class='schedule-option'>";
        $html .= "<input type = 'checkbox' class = 'optionalSettings' id = 'enableOnlineRegistrations' " . (($showOptions['allow_online_registrations']) ? 'checked' : '') . "><label for = 'enableOnlineRegistrations'>Enable Online Registrations</label>";
        $html .= "</div>";

        $html .= "<div class = 'registration-fee-option'>";
        $html .= "<label for='registrationFeeInput'>Registration Fee</label>";
        $html .= "<input type = 'number' min = '0' step= '0.01' value = '" . $showOptions['registration_fee'] . "' class = 'optionalSettings' id = 'registrationFeeInput'></input>";
        $html .= "</div>";

        $html .= "<div class = 'prize-money-option'>";
        $html .= "<label for='prizeMoney-firstPlace'>Prize Money First Place</label>";
        $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions['pm_first_place'] . "' class = 'optionalSettings' id = 'prizeMoney-firstPlace'></input>";
        $html .= "</div>";

        $html .= "<div class = 'prize-money-option'>";
        $html .= "<label for='prizeMoney-secondPlace'>Prize Money Second Place</label>";
        $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions['pm_second_place'] . "' class = 'optionalSettings' id = 'prizeMoney-secondPlace'></input>";
        $html .= "</div>";

        $html .= "<div class = 'prize-money-option'>";
        $html .= "<label for='prizeMoney-thirdPlace'>Prize Money Third Place</label>";
        $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions['pm_third_place'] . "' class = 'optionalSettings' id = 'prizeMoney-thirdPlace'></input>";
        $html .= "</div>";

        //unstandardised + junior + auction checkboxes
        $html .= "<div class='schedule-option'>";
        $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Unstandardised' " . (($showOptions['allow_unstandardised']) ? 'checked' : '') . "><label for = 'allow-Unstandardised'>Allow Unstandardised</label>";
        $html .= "</div>";

        $html .= "<div class='schedule-option'>";
        $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Junior' " . (($showOptions['allow_junior']) ? 'checked' : '') . "><label for = 'allow-Junior'>Allow Junior</label>";
        $html .= "</div>";

        $html .= "<div class='schedule-option'>";
        $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Auction' " . (($showOptions['allow_auction']) ? 'checked' : '') . "><label for = 'allow-Auction'>Allow Auction</label>";
        $html .= "</div>";

        $html .= "</div>";

        return $html;
    }

    private static function getShowClassesHtml($locationID)
    {
        $html = "<div class='classes-wrapper'>";
        foreach (EventProperties::SECTIONNAMES as $sectionName) {
            $sectionNameLower = strtolower($sectionName);
            $html .= "<div class='show-section'>";
            $html .= "<h3>" .$sectionName. "</h3>";
            $html .= "<table id = 'table" . $sectionNameLower . "-location'>";
            $html .= "<tbody>";
            $html .= "<tr>Ad       u8</tr>";
            $sectionClassesData = ShowOptionsController::getShowSectionClassesData($locationID, $sectionNameLower);
            foreach ($sectionClassesData as $classData) {
                $html .= "<tr class = 'classRow-location' id = '" . $classData['class_name'] . "-tr-location'>";
                $html .= "<td class = 'positionCell'>" .$classData['ad_index']. "/" .$classData['u8_index']. "</td>";
                $html .= "<td class = 'classNameCell'>" .$classData['class_name']. "</td>";

                //if (is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaries['name']) || current_user_can('administrator'))) {
                    $html .= "<td class='class-order'><button type = 'button' class = 'moveClassButton " . (($classData['section_position'] > 0) ? 'active' : '') . "' id = '" . $sectionNameLower . "&-&" . $classData['class_name'] . "&-&moveUp'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/up.svg'></button>";
                    $html .= "<button type = 'button' class = 'moveClassButton " . (($classData['section_position'] < count($sectionClassesData) - 1 && $classData['section_position'] + 1 < count($sectionClassesData)) ? 'active' : '') . "'  id = '" . $sectionNameLower . "&-&" . $classData['class_name'] . "&-&moveDown'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/down.svg'></button></td>";
                    $html .= "<td class='class-delete'><button type = 'button' class = 'deleteClassButton' id = '". $classData['class_name'] . "&-&delete'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></td>";
                //}
                $html .= "</tr>";
            }
            //add challenge row
            $challengeName = EventProperties::getChallengeName($sectionNameLower);
            $html .= "<tr class = 'classRow-location' id = '" . $challengeName . "-tr-location'>";
            $html .= "<td class = 'positionCell ad'>" .ShowOptionsController::getChallengeIndex($locationID, $challengeName, "Ad") . "/" .ShowOptionsController::getChallengeIndex($locationID, $challengeName, "U8") . "</td>";
            $html .= "<td class = 'classNameCell'><span>" . $challengeName . "</span></td>";
            $html .= "<td></td><td></td>";
            $html .= "</tr>";
            
            $html .= "</tbody>";
            $html .= "</table>";
            //if (is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaries['name']) || current_user_can('administrator'))) {
                $html .= "<button type = 'button' id = '" . $sectionNameLower . "AddButton' class = 'addBreedButton'>Add Class</button>";
            //}
            $html .= "</div>";
        }
        $html .= self::getShowOptionalClassesHtml($locationID);
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    private static function getShowOptionalClassesHtml($locationID){
        $html = "<div class='show-section'>";
        $html .= "<h3>GRAND CHALLENGE</h3>";
        $html .= "<table id = 'tableoptional-location'>";
        $html .= "<tbody>";
        $html .= "<tr>Ad       u8</tr>";
        $html .= "<tr class = 'classRow-location' id = 'GRAND CHALLENGE-tr-location'>";
        $html .= "<td class = 'positionCell ad'>".ShowOptionsController::getChallengeIndex($locationID, EventProperties::GRANDCHALLENGE, "Ad")."/".ShowOptionsController::getChallengeIndex($locationID, EventProperties::GRANDCHALLENGE, "U8")."</td>";
        $html .= "<td class = 'classNameCell'><span>GRAND CHALLENGE</span></td>";
        $html .= "</tr>";
        $optionalClassesData = ShowOptionsController::getShowOptionalClassesData($locationID);
        foreach($optionalClassesData as $classData){
            $html .= "<tr class = 'classRow-location' id = '".$classData['class_name']."-tr-location'>";
            $html .= "<td class = 'positionCell'>".$classData['aa_index']."</td>";
            $html .= "<td class = 'classNameCell'>".$classData['class_name']."</td>";

            //if(is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaries['name']) || current_user_can('administrator'))){
                $html .= "<td class='class-order'><button type = 'button' class = 'moveClassButton " . (($classData['section_position'] > 0) ? 'active' : '') . "' id = 'optional&-&" . $classData['class_name'] . "&-&moveUp'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/up.svg'></button>";
                $html .= "<button type = 'button' class = 'moveClassButton " . (($classData['section_position'] < count($optionalClassesData) - 1 && $classData['section_position'] + 1 < count($optionalClassesData)) ? 'active' : '') . "'  id = 'optional&-&" . $classData['class_name'] . "&-&moveDown'><img class='button-img' src='/wp-content/plugins/micerule-tables/admin/svg/down.svg'></button></td>";
            //}
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";

        return $html;
    }
}
