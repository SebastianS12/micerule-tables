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
        $variety = (isset($rowData['variety_name'])) ? $rowData['variety_name'] : "No Record";
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

    public static function convertPostmeta(){
        global $wpdb;
        $postMetaResults = $wpdb->get_results("SELECT post_id, meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'micerule_data_settings' AND meta_value IS NOT NULL", ARRAY_A);
        foreach($postMetaResults as $eventResult){
            $eventPostID = $eventResult['post_id'];
            $metaValue = get_post_meta($eventPostID, 'micerule_data_settings', true);
                $eventAwards = $metaValue['awards'];
                if(isset($eventAwards[0])){
                    self::saveEventResult(0, $eventPostID, "BIS", "grand challenge", 4, $metaValue);
                }
                if(isset($eventAwards[1])){
                    self::saveEventResult(1, $eventPostID, "BOA", "grand challenge", 3, $metaValue);
                }
                if(isset($eventAwards[2])){
                    self::saveEventResult(2, $eventPostID, "BISec", "selfs", 2, $metaValue);
                }
                if(isset($eventAwards[3])){
                    self::saveEventResult(3, $eventPostID, "BOSec", "selfs", 1, $metaValue);
                }
                if(isset($eventAwards[4])){
                    self::saveEventResult(4, $eventPostID, "BISec", "marked", 2, $metaValue);
                }
                if(isset($eventAwards[5])){
                    self::saveEventResult(5, $eventPostID, "BOSec", "marked", 1, $metaValue);
                }
                if(isset($eventAwards[6])){
                    self::saveEventResult(6, $eventPostID, "BISec", "tans", 2, $metaValue);
                }
                if(isset($eventAwards[7])){
                    self::saveEventResult(7, $eventPostID, "BOSec", "tans", 1, $metaValue);
                }
                if(isset($eventAwards[8])){
                    self::saveEventResult(8, $eventPostID, "BISec", "satins", 2, $metaValue);
                }
                if(isset($eventAwards[9])){
                    self::saveEventResult(9, $eventPostID, "BOSec", "satins", 1, $metaValue);
                }
                if(isset($eventAwards[10])){
                    self::saveEventResult(10, $eventPostID, "BISec", "aovs", 2, $metaValue);
                }
                if(isset($eventAwards[11])){
                    self::saveEventResult(11, $eventPostID, "BOSec", "aovs", 1, $metaValue);
                }
                if(isset($eventAwards[12])){
                    $class = strtolower(explode(" ",$eventAwards[12])[1]);
                    if($class = 'juvenile')
                        $class = 'junior';
                    self::saveOptionalEventResult($class, 12, $eventPostID, $metaValue);
                }
                if(isset($eventAwards[13])){
                    $class = strtolower(explode(" ",$eventAwards[13])[1]);
                    if($class = 'juvenile')
                        $class = 'junior';
                    self::saveOptionalEventResult($class, 13, $eventPostID, $metaValue);
                }
        }
    }

    private static function saveEventResult($awardIndex,  $eventPostID, $award, $section, $points, $eventResult){
        global $wpdb;
        $fancierName = $eventResult['name'][$awardIndex];
        $varietyName = get_option(get_option("mrOption_id")[$eventResult['breeds'][$awardIndex]])['name'];
        if($varietyName == "" || $varietyName == null){
            $varietyName = "No Record";
        }
        $age = $eventResult['age'][$awardIndex];

        $table_name = $wpdb->prefix."micerule_event_results";
        $data = array(
            "event_post_id" => $eventPostID,
            "award" => $award,
            "section" => $section,
            "fancier_name" => $fancierName,
            "variety_name" => $varietyName,
            "age" => $age,
            "points" => $points,
        );
        $wpdb->insert($table_name, $data);
    }

    private static function saveOptionalEventResult($class, $awardIndex, $eventPostID, $eventResult){
        global $wpdb;
        $fancierName = $eventResult['name'][$awardIndex];
        $varietyName = $eventResult['breeds'][$awardIndex];
        if($varietyName == ""){
            $varietyName = "No Record";
        }

        $table_name = $wpdb->prefix."micerule_event_results_optional";
        $data = array(
            "event_post_id" => $eventPostID,
            "class_name" => $class,
            "fancier_name" => $fancierName,
            "variety_name" => trim($varietyName),
        );
        $wpdb->insert($table_name, $data);
    }
}