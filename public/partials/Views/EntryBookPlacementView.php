<?php

class EntryBookPlacementView{
    public static function render($data){
        $html = "<td class = 'placement-" . $data['age'] . "'>";
        $html .= "<div class='placement-checks' data-index-id = ".$data['index_id']." data-entry-id = ".$data['entry_id'].">";
        $html .= "<input type = 'checkbox' name = 'firstPlaceCheck' class = 'placementCheck' id = '1-" . $data['index_id'] . "-" . $data['entry_id'] . "-".$data['prize']."-check' " . $data['firstPlaceChecked'] . " " . $data['sectionBestDisabled'] . " style='display: ".$data['showFirstPlaceCheck'].";' data-prize='".$data['prize']."' data-placement = 1 data-placement-id = ".$data['placementID']."><label for = '1-" . $data['index_id'] . "-" . $data['entry_id'] . "-".$data['prize']."-check' style='display: ".$data['showFirstPlaceCheck'].";'>1</label>";
        $html .= "<input type = 'checkbox' name = 'secondPlaceCheck' class = 'placementCheck' id = '2-" . $data['index_id'] . "-" . $data['entry_id'] . "-".$data['prize']."-check' " . $data['secondPlaceChecked'] . " " . $data['sectionBestDisabled'] . " style='display: ".$data['showSecondPlaceCheck'].";' data-prize='".$data['prize']."' data-placement = 2><label for = '2-" . $data['index_id'] . "-" . $data['entry_id'] . "-".$data['prize']."-check' style='display: ".$data['showSecondPlaceCheck'].";'>2</label>";
        $html .= "<input type = 'checkbox' name = 'thirdPlaceCheck' class = 'placementCheck' id ='3-" . $data['index_id'] . "-" . $data['entry_id'] . "-".$data['prize']."-check' " . $data['thirdPlaceChecked'] . " " . $data['sectionBestDisabled'] . " style='display: ".$data['showThirdPlaceCheck'].";' data-prize='".$data['prize']."' data-placement = 3><label for = '3-" . $data['index_id'] . "-" . $data['entry_id'] . "-".$data['prize']."-check' style='display: ".$data['showThirdPlaceCheck'].";'>3</label>";
        $html .= "</div>";
        $html .= "</td>";

        return $html;
    }
}