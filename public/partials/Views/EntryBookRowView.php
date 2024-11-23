<?php

class EntryBookRowView{
    public static function render($data, bool $optionalClass = false){
        $html = "<tr class='entry-pen-number'>";
        $html .= "<td class='pen-numbers " . $data['classMoved'] . " " . $data['classAbsent'] . " " . $data['classAdded'] . "'><span>" . $data['penNumber'] . "</span></td>";
        $html .= self::getAbsentCell($data);
        $html .= "<td class='user-names " . $data['classMoved'] . "'><span>" . $data['userName'] . "</span></td>";
        $html .= self::getEditCell($data);

        //TODO: Enums for Prize
        $html .= EntryBookPlacementView::render($data['classPlacementData']);
        if(!$optionalClass){
            $html .= EntryBookPlacementView::render($data['sectionPlacementData']);
            $html .= EntryBookPlacementView::render($data['grandChallengePlacementData']);  
        }
        $html .= "</tr>";

        return $html;
    }

    private static function getAbsentCell($data)
    {
        $html = "<td class = 'absent-td' data-entry-id = ".$data['entryID'].">";
        $html .= "<input type = 'checkbox' class = 'absentCheck' id = '" . $data['penNumber'] . "&-&absent&-&check' " . $data['absentChecked'] . " visibility = '".$data['absentVisibility']."'></input><label for='" . $data['penNumber'] . "&-&absent&-&check'><img src='/wp-content/plugins/micerule-tables/admin/svg/absent-not.svg'></label>";
        $html .= "</td>";

        return $html;
    }

    private static function getEditCell($data)
    {
        $html  = "<td class = 'editEntry-td' data-entry-id = ".$data['entryID'].">";
        if($data['editVisibility']){
            $html .= "<div class='button-wrapper'><button class = 'moveEntry' id = '" . $data['penNumber'] . "&-&move'><img src='/wp-content/plugins/micerule-tables/admin/svg/move.svg'></button>
                  <button class = 'deleteEntry' id = '" . $data['penNumber'] . "&-&delete'><img src='/wp-content/plugins/micerule-tables/admin/svg/trash.svg'></button></div>";
        }
        $html .=  "<select class = 'classSelect-entryBook' id = '".$data['entryID']."&-&varietySelect' autocomplete='off' style='display:".$data['showVarietySelect']."'><option value = ''>Select a Variety</option>".$data['varietyOptions']."</select>";
        $html .= "</td>";

        return $html;
    }
}