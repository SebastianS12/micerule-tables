<?php

class ShowOptionsRepository{
    // public function getShowOptions(int $locationID): ShowOptions
    // {
    //     return ShowOptions::createFromPostMeta(get_post_meta($locationID, 'micerule_show_options', true));
    // }

    // public function saveShowOptions(int $locationID, ShowOptions $showOptions): void
    // {
    //     update_post_meta($locationID, 'micerule_show_options', $showOptions);
    // }

    public function getShowOptions(int $locationID): ShowOptionsModel
    {
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::SHOW_OPTIONS)
                                ->where(Table::SHOW_OPTIONS->getAlias(), "location_id", "=", $locationID)
                                ->build();
        
        global $wpdb;
        $showOptionsQueryResults = $wpdb->get_row($query, ARRAY_A);

        if($showOptionsQueryResults === null) return ShowOptionsModel::getDefault($locationID);

        return ShowOptionsModel::createWithID(...$showOptionsQueryResults);
    }

    public function saveShowOptions(ShowOptionsModel $showOptionsModel): void
    {
        global $wpdb;
        if(isset($showOptionsModel->id)){
            $wpdb->update($wpdb->prefix.Table::SHOW_OPTIONS->value, get_object_vars($showOptionsModel), array('id' => $showOptionsModel->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::SHOW_OPTIONS->value, get_object_vars($showOptionsModel));
        }
    }
}