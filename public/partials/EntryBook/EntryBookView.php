<?php

class EntryBookView
{
    //TODO: Split up into more functions
    public static function getEntryBookHtml($eventPostID)
    {
        //TODO: Get Data from EntryBookModel?
        $eventDeadline = EventProperties::getEventDeadline($eventPostID);
        $showClassesModel = new ShowClassesModel();
        $html = "<div class = 'entryBook content' style = 'display : none'>";
        $html .= "<div>";
        $html .= (time() > strtotime($eventDeadline)) ? "<a class = 'button addEntry'>Add Entry</a>" : "";

        $adGrandChallengePlacements = new GrandChallengePlacements($eventPostID, "Ad");
        $u8GrandChallengePlacements = new GrandChallengePlacements($eventPostID, "U8");
        foreach (EventProperties::SECTIONNAMES as $sectionName) {
            $sectionName = strtolower($sectionName);
            $adSectionPlacementsModel = new SectionPlacements($eventPostID, "Ad", $sectionName);
            $u8SectionPlacementsModel = new SectionPlacements($eventPostID, "U8", $sectionName);
            
            $sectionStandardBreeds = Breed::getSectionBreedNames($sectionName);
            $html .= "<div class = '" . $sectionName . "-div'>";
            foreach ($showClassesModel->getShowSectionClassNames(EventProperties::getEventLocationID($eventPostID), $sectionName) as $className) {
                //$classData = $this->entryBookData->classes[$className];
                $adClassModel = new ShowClassModel($eventPostID, $className, "Ad");
                $u8ClassModel = new ShowClassModel($eventPostID, $className, "U8");
                $html .= "<div class = 'class-pairing'>";
                $adsTableHtml = "<table><tbody>";
                $u8TableHtml = "<table><tbody>";
                $adsTableHtml .= self::getBreedNameHeader($adClassModel);
                $u8TableHtml .= self::getBreedNameHeader($u8ClassModel);

                $adRowCount = 0;
                $u8RowCount = 0;
                $adClassPlacementsModel = new ClassPlacements($eventPostID, "Ad", $className);
                $u8ClassPlacementsModel = new ClassPlacements($eventPostID, "U8", $className);
                foreach ($adClassModel->penNumbers as $penNumber) {
                    $entry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
                    $adsTableHtml .= self::getEntryRow($entry, $eventDeadline, "Ad", $adClassPlacementsModel, $adSectionPlacementsModel, $adGrandChallengePlacements, $sectionStandardBreeds, $sectionName, EventProperties::getEventLocationID($eventPostID));
                    $adRowCount++;
                }
                foreach ($u8ClassModel->penNumbers as $penNumber) {
                    $entry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
                    $u8TableHtml .= self::getEntryRow($entry, $eventDeadline, "U8", $u8ClassPlacementsModel, $u8SectionPlacementsModel, $u8GrandChallengePlacements, $sectionStandardBreeds, $sectionName, EventProperties::getEventLocationID($eventPostID));
                    $u8RowCount++;
                }

                $adsTableHtml .= ($adRowCount < $u8RowCount) ? self::addEmptyRows($u8RowCount - $adRowCount, "Ad") : "";
                $u8TableHtml .= ($u8RowCount < $adRowCount) ? self::addEmptyRows($adRowCount - $u8RowCount, "U8") : "";

                $adsTableHtml .= "</tbody></table>";
                $u8TableHtml .= "</tbody></table>";
                $html .= $adsTableHtml;
                $html .= $u8TableHtml;
                $html .= "</div>";
            }

            $html .= "<div class = 'class-pairing'>";
            $prize = "Section Challenge";
            $challengeName = EventProperties::getChallengeName($sectionName);
            $challengeAwardsModel = new SectionChallengeAwards($eventPostID, $sectionName);
            $html .= self::getChallengeRow(new ShowChallengeModel($eventPostID, $challengeName, $sectionName, "Ad"), $challengeAwardsModel, $prize, $adSectionPlacementsModel, $u8SectionPlacementsModel);
            $html .= self::getChallengeRow(new ShowChallengeModel($eventPostID, $challengeName, $sectionName, "U8"), $challengeAwardsModel, $prize, $u8SectionPlacementsModel, $adSectionPlacementsModel);
            $html .= "</div>";

            $html .= "</div>";
        }

        $html .= "<div class = 'class-pairing'>";
        //TODO: Enum
        $prize = "Grand Challenge";
        $grandChallengeAwardsModel = new GrandChallengeAwards($eventPostID);
        $html .= self::getChallengeRow(new ShowChallengeModel($eventPostID, EventProperties::GRANDCHALLENGE, "", "Ad"), $grandChallengeAwardsModel, $prize, $adGrandChallengePlacements, $u8GrandChallengePlacements);
        $html .= self::getChallengeRow(new ShowChallengeModel($eventPostID, EventProperties::GRANDCHALLENGE, "", "U8"), $grandChallengeAwardsModel, $prize, $u8GrandChallengePlacements, $adGrandChallengePlacements);
        $html .= "</div>";

        $html .= self::getOptionalClassHtml($eventDeadline, $showClassesModel, $eventPostID);

        $html .= "<div id = 'editEntryModal' style = 'hidden'></div>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    private static function getBreedNameHeader($classModel)
    {
        $html = "<tr class='breed-name-header'>";
        $html .= "<td class='table-pos'>" . $classModel->index . "</td>";
        $html .= "<td class = 'absent-td'>Abs</td>";
        $html .= "<td class='breed-class'>" . $classModel->name . " " . $classModel->age . "</td>";
        $html .= "<td class='age'></td>";
        $html .= "<td class = 'placement-" . $classModel->age . "'><img src='/wp-content/plugins/micerule-tables/admin/svg/class-ranking.svg'></td>";
        $html .= "<td class = 'sectionBest-" . $classModel->age . "'><img src='/wp-content/plugins/micerule-tables/admin/svg/section-first.svg'></td>";
        $html .= "<td class = 'ageBest-" . $classModel->age . "'><img src='/wp-content/plugins/micerule-tables/admin/svg/challenge-first.svg'></td>";
        $html .= "</tr>";

        return $html;
    }

    private static function getEntryRow($entry, $eventDeadline, $age, $classPlacementsModel, $sectionPlacementsModel, $grandChallengePlacementsModel, $sectionStandardBreeds, $sectionName, $locationID)
    {
        $classMoved = ($entry->moved) ? "moved" : "";
        $classAbsent = ($entry->absent) ? "absent" : "";
        $classAdded = ($entry->added) ? "added" : "";

        $html = "<tr class='entry-pen-number'>";
        $html .= "<td class='pen-numbers " . $classMoved . " " . $classAbsent . " " . $classAdded . "'><span>" . $entry->penNumber . "</span></td>";
        $html .= self::getAbsentCell($entry, $classPlacementsModel);
        $html .= "<td class='user-names " . $classMoved . "'><span>" . $entry->userName . "</span></td>";
        $html .= self::getEditCell($eventDeadline, $entry, $classPlacementsModel, $sectionStandardBreeds, $sectionName, $locationID);

        //TODO: Enums for Prize
        $html .= self::getPlacementEditCell($entry, null, $classPlacementsModel, $sectionPlacementsModel, $age, "Class");
        $html .= self::getPlacementEditCell($entry, $classPlacementsModel, $sectionPlacementsModel, $grandChallengePlacementsModel, $age, "Section Challenge");
        $html .= self::getPlacementEditCell($entry, $sectionPlacementsModel, $grandChallengePlacementsModel, null, $age, "Grand Challenge");

        $html .= "</tr>";

        return $html;
    }

    private static function addEmptyRows($rowCount, $age)
    {
        $html = "";
        for ($i = 0; $i < $rowCount; $i++) {
            $html .= "<tr class='entry-pen-number'>";
            $html .= "<td class='pen-numbers'>";
            $html .= "<td class='absent-td'></td>";
            $html .= "<td class='user-names'></td>";
            $html .= "<td class='editEntry-td'></td>";
            $html .= "<td class='placement-" . $age . "'></td>";
            $html .= "<td class='sectionBest-" . $age . "'></td>";
            $html .= "<td class='ageBest-" . $age . "'></td>";
            $html .= "</tr>";
        }

        return $html;
    }

    private static function getAbsentCell($entry, $classPlacementsModel)
    {
        $absentChecked = ($entry->absent) ? "checked" : "";
        $classAbsent = ($entry->absent) ? "absent" : "";

        $html = "<td class = 'absent-td'>";
        $html .= (!$classPlacementsModel->entryInPlacements($entry->ID)) ? "<input type = 'checkbox' class = 'absentCheck' id = '" . $entry->penNumber . "&-&absent&-&check' " . $absentChecked . "></input><label for='" . $entry->penNumber . "&-&absent&-&check'><img src='/wp-content/plugins/micerule-tables/admin/svg/absent-not.svg'></label>" : "";
        $html .= "</td>";

        return $html;
    }

    private static function getEditCell($eventDeadline, $entry, $classPlacementsModel, $sectionStandardBreeds, $sectionName, $locationID)
    {
        $html  = "<td class = 'editEntry-td'>";
        $html .= (!$classPlacementsModel->entryInPlacements($entry->ID) && time() > strtotime($eventDeadline)) ? "<div class='button-wrapper'><button class = 'moveEntry' id = '" . $entry->penNumber . "&-&move'><img src='/wp-content/plugins/micerule-tables/admin/svg/move.svg'></button>
                  <button class = 'deleteEntry' id = '" . $entry->penNumber . "&-&delete'><img src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></div>" : "";
        $html .= (!in_array($entry->className, $sectionStandardBreeds) && $classPlacementsModel->entryInPlacements($entry->ID)) ? "<select class = 'classSelect-entryBook' id = '".$entry->ID."&-&varietySelect' autocomplete='off'><option value = ''>Select a Variety</option>".ClassSelectOptions::getClassSelectOptionsHtml($sectionName, $locationID, $entry->varietyName)."</select>" : "";
        $html .= "</td>";

        return $html;
    }

    private static function getPlacementEditCell($entry, $lowerPlacementsModel, $currentPlacementsModel, $higherPlacementsModel, $age, $prize)
    {
        $sectionBestDisabled = (isset($higherPlacementsModel) && $higherPlacementsModel->entryInPlacements($entry->ID)) ? "disabled" : "";
        $firstPlaceChecked = ($currentPlacementsModel->entryHasPlacement(1, $entry->ID)) ? "checked" : "";
        $secondPlaceChecked = ($currentPlacementsModel->entryHasPlacement(2, $entry->ID)) ? "checked" : "";
        $thirdPlaceChecked = ($currentPlacementsModel->entryHasPlacement(3, $entry->ID)) ? "checked" : "";
        $html = "<td class = 'placement-" . $age . "'>";
        $html .= "<div class='placement-checks'>";

        $showFirstPlaceCheck = !isset($lowerPlacementsModel) || $lowerPlacementsModel->entryHasPlacement(1, $entry->ID);
        if ($showFirstPlaceCheck) {
            $html .= ($currentPlacementsModel->showPlacementCheck(1, $entry->ID)) ? "<input type = 'checkbox' name = 'firstPlaceCheck' class = 'placementCheck' id = '" . $prize . "&-&1&-&" . $entry->ID . "&-&check' " . $firstPlaceChecked . " " . $sectionBestDisabled . "><label for = '" . $prize . "&-&1&-&" . $entry->ID . "&-&check'>1</label>" : "";
        }

        $firstPlaceIsInSameClass = isset($lowerPlacementsModel) && ($lowerPlacementsModel->isPlacementChecked(1) && $currentPlacementsModel->higherPlacementEntryIsInSameClass(1, $entry->ID));
        $showSecondPlaceCheck = !isset($lowerPlacementsModel) || ($showFirstPlaceCheck || ($lowerPlacementsModel->entryHasPlacement(2, $entry->ID) && $firstPlaceIsInSameClass));
        if ($showSecondPlaceCheck) {
            $html .= ($currentPlacementsModel->showPlacementCheck(2, $entry->ID)) ? "<input type = 'checkbox' name = 'secondPlaceCheck' class = 'placementCheck' id = '" . $prize . "&-&2&-&" . $entry->ID . "&-&check' " . $secondPlaceChecked . " " . $sectionBestDisabled . "><label for = '" . $prize . "&-&2&-&" . $entry->ID . "&-&check'>2</label>" : "";
        }

        $secondPlaceIsInSameClass = isset($lowerPlacementsModel) && ($lowerPlacementsModel->isPlacementChecked(2) && $currentPlacementsModel->higherPlacementEntryIsInSameClass(2, $entry->ID));
        $showThirdPlaceCheck = !isset($lowerPlacementsModel) || ($showFirstPlaceCheck || $showSecondPlaceCheck || ($lowerPlacementsModel->entryHasPlacement(3, $entry->ID) && $secondPlaceIsInSameClass));
        if ($showThirdPlaceCheck) {
            $html .= ($currentPlacementsModel->showPlacementCheck(3, $entry->ID)) ? "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id = '" . $prize . "&-&3&-&" . $entry->ID . "&-&check' " . $thirdPlaceChecked . " " . $sectionBestDisabled . "><label for = '" . $prize . "&-&3&-&" . $entry->ID . "&-&check'>3</label>" : "";
        }

        $html .= "</div>";
        $html .= "</td>";

        return $html;
    }

    private static function getChallengeRow($showChallengeModel, $challengeAwardsModel, $prize, $agePlacements, $oppositeAgePlacements)
    {
        $BISChecked = ($challengeAwardsModel->bisChecked($showChallengeModel->age)) ? "checked" : "";
        $BISDisabled = ($challengeAwardsModel->boaChecked($showChallengeModel->age)) ? "disabled" : "";
        $html = "<table><tbody>";
        $html .= "<tr class='challenge-row'><td class='table-pos'>" . $showChallengeModel->index . "</td><td class='breed-class'>" . $showChallengeModel->name . " " . $showChallengeModel->age . "</td><td class='age'></td><td class='placement-" . $showChallengeModel->age . "'></td><td class='sectionBest-" . $showChallengeModel->age . "'><div class='placement-checks'>";
        $html .= ($agePlacements->isPlacementChecked(1) && $oppositeAgePlacements->isPlacementChecked(1)) ? "<input type = 'checkbox' class = 'BISCheck' id = '" . $prize . "&-&" . $showChallengeModel->age . "&-&" . $showChallengeModel->challengeSection . "&-&BIS&-&check' " . $BISChecked . " " . $BISDisabled . "></input><label for = '" . $prize . "&-&" . $showChallengeModel->age . "&-&" . $showChallengeModel->challengeSection . "&-&BIS&-&check'><span class='is-best'>BEST</span><span class='is-boa'>BOA</span></label>" : "";
        $html .= "</div></td><td class='ageBest-" . $showChallengeModel->age . "'></td></tr>";
        $html .= self::getChallengePlacementOverviewHtml($agePlacements);
        $html .= "</tbody></table>";

        return $html;
    }

    private static function getChallengePlacementOverviewHtml($placementModel)
    {
        $html = "";
        foreach ($placementModel->placements as $placement => $placementEntryID) {
            if ($placementEntryID != null) {
                $placementEntry = ShowEntry::createWithEntryID($placementEntryID);
                $html .= "<tr>";
                $html .= "<td>" . $placementEntry->penNumber . "</td>";
                $html .= "<td>" . $placementEntry->userName . "</td>";
                $html .= "<td>" . $placementEntry->varietyName . "</td>";
                $html .= "<td>" . $placement . "</td>";
                $html .= "<td></td>";
                $html .= "<td></td>";
            }
        }

        return $html;
    }

    private static function getOptionalClassHtml($eventDeadline, $showClassesModel, $eventPostID)
    {
        $html = "";

        $sectionName = 'optional';
        foreach ($showClassesModel->getShowSectionClassnames(EventProperties::getEventLocationID($eventPostID), $sectionName) as $className) {
            $optionalClassModel = new ShowClassModel($eventPostID, $className, "AA");
            //TODO: ENUM

            $html .= "<table class='optional'><tbody>";
            $html .= self::getOptionalClassHeaderRowHtml($optionalClassModel);
            //TODO: ENUM
            if($className != "Junior")
                $html .= self::getOptionalClassEntryRows($optionalClassModel, $eventPostID, $eventDeadline);
            else
                $html .= self::getJuniorEntryRows($optionalClassModel, $eventPostID);

            $html .= "</table></tbody>";
        }

        return $html;
    }

    private static function getOptionalClassHeaderRowHtml($optionalClassModel)
    {
        $html = "<tr class='breed-name-header'>";
        $html .= "<td class='table-pos'>" . $optionalClassModel->index . "</td>";
        $html .= "<td class = 'absent-td'>Abs</td>";
        $html .= "<td class='breed-class'>" . ucfirst($optionalClassModel->name) . " AA</td>";
        $html .= "<td class='age'></td>";
        $html .= "<td class = 'placement-ads'><img src='/wp-content/plugins/micerule-tables/admin/svg/class-ranking.svg'></td>";
        $html .= "</tr>";

        return $html;
    }

    private static function getOptionalClassEntryRows($optionalClassModel, $eventPostID, $eventDeadline)
    {
        $classPlacementsModel = new ClassPlacements($eventPostID, "AA", $optionalClassModel->name);
        $html = "";
        foreach ($optionalClassModel->penNumbers as $penNumber) {
            $entry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
            $classMoved = ($entry->moved) ? "moved" : "";
            $classAdded = ($entry->added) ? "added" : "";

            $html .= "<tr class='entry-pen-number'>";
            $html .= "<td class='pen-numbers " . $classMoved . " " . $classAdded . "'><span>" . $entry->penNumber . "</span></td>";
            $html .= self::getAbsentCell($entry, $classPlacementsModel);
            $html .= "<td class='user-names " . $classMoved . "'>" . $entry->userName . "</td>";
            $html .= "<td class = 'editEntry-td'>";
            $html .= (!$classPlacementsModel->entryInPlacements($entry->ID) && time() > strtotime($eventDeadline)) ? "<div class='button-wrapper'><button class = 'moveEntry' id = '" . $entry->penNumber . "&-&move'><img src='/wp-content/plugins/micerule-tables/admin/svg/move.svg'></button>
                  <button class = 'deleteEntry' id = '" . $entry->penNumber . "&-&delete'><img src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></div>" : "";
            $html .= ($optionalClassModel->name == "Unstandardised" && $classPlacementsModel->entryInPlacements($entry->ID)) ? "<input type = 'text' class = 'unstandardised-input' id = '" . $entry->ID . "&-&varietySelect' val = '" . $entry->varietyName . "' placeholder = '" . $entry->varietyName . "'></input>" : "";
            $html .= "</td>";
            $html .= self::getPlacementEditCell($entry, NULL, $classPlacementsModel, NULL, "AA", "Class");
            $html .= "</tr>";
        }

        return $html;
    }

    private static function getJuniorEntryRows($juniorClassModel, $eventPostID)
    {
        $classPlacementsModel = new JuniorPlacements($eventPostID, "AA");
        $html = "";
        foreach ($juniorClassModel->penNumbers as $penNumber) {
            $entry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
            $classMoved = ($entry->moved) ? "moved" : "";
            $classAdded = ($entry->added) ? "added" : "";

            $html .= "<tr class='entry-pen-number'>";
            $html .= "<td class='pen-numbers " . $classMoved . " " . $classAdded . "'><span>" . $entry->penNumber . "</span></td>";
            $html .= self::getAbsentCell($entry, $classPlacementsModel);
            $html .= "<td class='user-names " . $classMoved . "'>" . $entry->userName . "</td>";
            $html .= "<td class = 'editEntry-td'>";
            $html .= "</td>";
            $html .= self::getPlacementEditCell($entry, NULL, $classPlacementsModel, NULL, "AA", "Junior");
            $html .= "</tr>";
        }

        return $html;
    }
}
