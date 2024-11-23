<?php

class ShowOptionsView
{
    public static function getSectionTablesHtml($locationID){
        $html = "<div id='locationSectionTables'>";
        $html .= "<input type='hidden' id='locationID' value=".$locationID.">";
        $html .= self::getShowOptionsHtml($locationID);
        if(PermissionHelper::canEditShowClasses($locationID)){
            $html .= self::getShowClassesHtml($locationID);
        }
        $html .= "</div>";

        return $html;
    }

    private static function getShowOptionsHtml(int $locationID)
    {
        $showOptions = ShowOptionsController::getShowOptions($locationID, new ShowOptionsService(), new ShowOptionsRepository);
        $html = "<div class='showsec-options' data-option-id = ".($showOptions->id ?? null).">";
        $html .= "<div>".var_export(is_user_logged_in(), true)."</div>";
        if(PermissionHelper::canEditShowClasses($locationID)){
            $html .= "<h3>SHOW OPTIONS</h3>";
            //enable online registrations checkbox
            $html .= "<div class='schedule-option'>";
            $html .= "<input type = 'checkbox' class = 'optionalSettings' id = 'enableOnlineRegistrations' " . (($showOptions->allow_online_registrations) ? 'checked' : '') . "><label for = 'enableOnlineRegistrations'>Enable Online Registrations</label>";
            $html .= "</div>";

            $html .= "<div class = 'registration-fee-option'>";
            $html .= "<label for='registrationFeeInput'>Registration Fee</label>";
            $html .= "<input type = 'number' min = '0' step= '0.01' value = '" . $showOptions->registration_fee . "' class = 'optionalSettings' id = 'registrationFeeInput'></input>";
            $html .= "</div>";

            $html .= "<div class = 'prize-money-option'>";
            $html .= "<label for='prizeMoney-firstPlace'>Prize Money First Place</label>";
            $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions->pm_first_place . "' class = 'optionalSettings' id = 'prizeMoney-firstPlace'></input>";
            $html .= "</div>";

            $html .= "<div class = 'prize-money-option'>";
            $html .= "<label for='prizeMoney-secondPlace'>Prize Money Second Place</label>";
            $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions->pm_second_place . "' class = 'optionalSettings' id = 'prizeMoney-secondPlace'></input>";
            $html .= "</div>";

            $html .= "<div class = 'prize-money-option'>";
            $html .= "<label for='prizeMoney-thirdPlace'>Prize Money Third Place</label>";
            $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions->pm_third_place . "' class = 'optionalSettings' id = 'prizeMoney-thirdPlace'></input>";
            $html .= "</div>";

            $html .= "<div class = 'prize-money-option'>";
            $html .= "<label for='prizeMoney-biSec'>Prize Money Best in Section</label>";
            $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions->pm_bisec . "' class = 'optionalSettings' id = 'prizeMoney-biSec'></input>";
            $html .= "</div>";

            $html .= "<div class = 'prize-money-option'>";
            $html .= "<label for='prizeMoney-boSec'>Prize Money Best in Section Opposite Age</label>";
            $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions->pm_bosec . "' class = 'optionalSettings' id = 'prizeMoney-boSec'></input>";
            $html .= "</div>";

            $html .= "<div class = 'prize-money-option'>";
            $html .= "<label for='prizeMoney-bis'>Prize Money Best in Show</label>";
            $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions->pm_bis . "' class = 'optionalSettings' id = 'prizeMoney-bis'></input>";
            $html .= "</div>";

            $html .= "<div class = 'prize-money-option'>";
            $html .= "<label for='prizeMoney-boa'>Prize Money Best in Show Opposite Age</label>";
            $html .= "<input type = 'number' min = '0' step= '0.1' value = '" . $showOptions->pm_boa . "' class = 'optionalSettings' id = 'prizeMoney-boa'></input>";
            $html .= "</div>";

            //unstandardised + junior + auction checkboxes
            $html .= "<div class='schedule-option'>";
            $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Unstandardised' " . (($showOptions->allow_unstandardised) ? 'checked' : '') . "><label for = 'allow-Unstandardised'>Allow Unstandardised</label>";
            $html .= "</div>";

            $html .= "<div class='schedule-option'>";
            $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Junior' " . (($showOptions->allow_junior) ? 'checked' : '') . "><label for = 'allow-Junior'>Allow Junior</label>";
            $html .= "</div>";

            $html .= "<div class='schedule-option'>";
            $html .= "<input type = 'checkbox' class = 'optionalSettings optionalClasses' id = 'allow-Auction' " . (($showOptions->allow_auction) ? 'checked' : '') . "><label for = 'allow-Auction'>Allow Auction</label>";
            $html .= "</div>";

            if($showOptions->allow_auction){
                $html .= "<div class = 'registration-fee-option'>";
                $html .= "<label for='auctionFeeInput'>Auction Fee</label>";
                $html .= "<input type = 'number' min = '0' step= '0.01' value = '" . $showOptions->auction_fee . "' class = 'optionalSettings' id = 'auctionFeeInput'></input>";
                $html .= "</div>";
            }
        }
        $html .= "</div>";

        return $html;
    }

    private static function getShowClassesHtml(int $locationID): string
    {
        return ShowClassesView::render($locationID);
    }
}
