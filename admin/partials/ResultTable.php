<?php 

class ResultTable{
    public static function saveTableData($postID, $tableData){
        foreach($tableData as $section => $awardData){
            foreach($awardData as $award => $rowData){
                self::saveTableRow($rowData, $award, $section, $postID);
            }
        }
    }

    public static function saveOptionalTableData($eventPostID, $optionalTableData){
        foreach($optionalTableData as $className => $rowData){
            self::saveOptionalTableRow($eventPostID, $className, $rowData);
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
        if(isset($rowData['data_id']) && $rowData['data_id'] != '')
            $wpdb->update($table_name, $data, array('id' => $rowData['data_id']));
        else
            $wpdb->insert($table_name, $data);
    }

    private static function saveOptionalTableRow($eventPostID, $className, $rowData){
        global $wpdb;

        $table_name = $wpdb->prefix."micerule_event_results_optional";
        if(isset($rowData['fancier_name']) && $rowData['fancier_name'] != ""){
           $replace_query = $wpdb->prepare("REPLACE INTO ".$table_name." VALUES (%d, %s, %s, %s)", $eventPostID, $className, $rowData['fancier_name'], $rowData['variety_name']); 
           $wpdb->query($replace_query);
        }else{
          $wpdb->delete($table_name, array("event_post_id" => $eventPostID, "class_name" => $className));
        }
    }
}