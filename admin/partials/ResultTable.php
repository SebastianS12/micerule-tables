<?php 

class ResultTable{
    public static function saveTableData($postID, $tableData){
        foreach($tableData as $award => $sectionData){
            foreach($sectionData as $sectionName => $rowData){
                self::saveTableRow($rowData, $award, $sectionName, $postID);
            }
        }
    }

    private static function saveTableRow($rowData, $award, $section, $postID){
        global $wpdb;

        $fancier = (isset($rowData['fancier_name'])) ? $rowData['fancier_name'] : "";
        $variety = (isset($rowData['variety_name'])) ? $rowData['variety_name'] : "";
        $age = (isset($rowData['age'])) ? $rowData['age'] : "";
        $points = (isset($rowData['points'])) ? $rowData['points'] : 0;

        $data = array(
            "event_post_id" => $postID,
            "award" => $award,
            "section" => $section,
            "fancier_name" => $fancier,
            "variety_name" => $variety,
            "age" => $age,
            "points" => $points,
        );

        $table_name = $wpdb->prefix."micerule_event_results";
        if(isset($rowData['data_id']))
            $wpdb->update($table_name, $data, array('id' => $rowData['data_id']));
        else
            $wpdb->insert($table_name, $data);
    }
}