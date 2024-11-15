<?php

class EntryBookView
{
    //TODO: Split up into more functions
    public static function getEntryBookHtml($eventPostID)
    {
        $locationID = LocationHelper::getIDFromEventPostID($eventPostID);
        $entryBookService = new EntryBookService();
        $viewModel = $entryBookService->prepareViewModel($eventPostID, $locationID);
  
        $html = "<div class = 'entryBook content' style = 'display : none'>";
        $html .= "<div>";
        $html .= ($viewModel->pastDeadline) ? "<a class = 'button addEntry'>Add Entry</a>" : "";


        // echo(var_dump($viewModel));
        foreach(EventProperties::SECTIONNAMES as $sectionName){
            $sectionName = strtolower($sectionName);
            $html .= "<div class = '" . $sectionName . "-div'>";

            foreach($viewModel->classData[$sectionName] as $className => $classData){
                $html .= "<div class = 'class-pairing'>";
                $adsTableHtml = "<table><tbody>";
                $u8TableHtml = "<table><tbody>";
                $adClassData = $classData['Ad'];
                $u8ClassData = $classData['U8'];
                $adsTableHtml .= self::getBreedNameHeader($adClassData['classIndex'], $className, "Ad");
                $u8TableHtml .= self::getBreedNameHeader($u8ClassData['classIndex'], $className, "U8");

                $adRowCount = 0;
                $u8RowCount = 0;
                foreach ($adClassData['entries'] as $entryRowData) {
                    $adsTableHtml .= EntryBookRowView::render($entryRowData);//$entryBookRowController->render($entry, $rowplacementData);
                    $adRowCount++;
                }
                foreach ($u8ClassData['entries'] as $entryRowData) {
                    $u8TableHtml .= EntryBookRowView::render($entryRowData);//$entryBookRowController->render($entry, $rowplacementData);
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
            $html .= ChallengeRowView::render($viewModel->challengeData[$sectionName]);
            $html .= "</div>";

            $html .= "</div>";
        }

        $html .= "<div class = 'class-pairing'>";
        $html .= ChallengeRowView::render($viewModel->grandChallengeData);
        $html .= "</div>";

        $html .= self::getOptionalClassHtml($viewModel->optionalClassData);

        $html .= "<div id = 'editEntryModal' style = 'hidden'></div>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    private static function getBreedNameHeader(int $index, string $className, string $age)
    {
        $html = "<tr class='breed-name-header'>";
        $html .= "<td class='table-pos'>" . $index. "</td>";
        $html .= "<td class = 'absent-td'>Abs</td>";
        $html .= "<td class='breed-class'>" . $className . " " . $age . "</td>";
        $html .= "<td class='age'></td>";
        $html .= "<td class = 'placement-" . $age . "'><img src='/wp-content/plugins/micerule-tables/admin/svg/class-ranking.svg'></td>";
        $html .= "<td class = 'sectionBest-" . $age . "'><img src='/wp-content/plugins/micerule-tables/admin/svg/section-first.svg'></td>";
        $html .= "<td class = 'ageBest-" . $age . "'><img src='/wp-content/plugins/micerule-tables/admin/svg/challenge-first.svg'></td>";
        $html .= "</tr>";

        return $html;
    }

    private static function getOptionalBreedNameHeader(int $index, string $className, string $age)
    {
        $html = "<tr class='breed-name-header'>";
        $html .= "<td class='table-pos'>" . $index. "</td>";
        $html .= "<td class = 'absent-td'>Abs</td>";
        $html .= "<td class='breed-class'>" . $className . " " . $age . "</td>";
        $html .= "<td class='age'></td>";
        $html .= "<td class = 'placement-" . $age . "'><img src='/wp-content/plugins/micerule-tables/admin/svg/class-ranking.svg'></td>";
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

    private static function getOptionalClassHtml(array $optionalClassData)
    {
        $html = "";
        foreach($optionalClassData as $className => $classData){
            $html .= "<table class='optional'><tbody>";
            $html .= self::getOptionalBreedNameHeader($classData['classIndex'], $className, "AA");
            foreach ($classData['entries'] as $entryRowData) {
                $html .= EntryBookRowView::render($entryRowData, true);
            }

            $html .= "</table></tbody>";
        }

        return $html;
    }
}
