<?php

class ShowSectionRepository{
    private int $locationID;

    public function __construct(int $locationID)
    {
        $this->locationID = $locationID;
    }

    public function getShowSectionClassNames(string $section): array|null{
        global $wpdb;
        return $wpdb->get_col("SELECT DISTINCT(CLASSES.class_name) FROM ".$wpdb->prefix."micerule_show_classes CLASSES INNER JOIN ".$wpdb->prefix."micerule_show_classes_indices INDICES ON CLASSES.id = INDICES.class_id WHERE CLASSES.location_id = ".$this->locationID." AND section = '".$section."' ORDER BY class_index");
    }
}