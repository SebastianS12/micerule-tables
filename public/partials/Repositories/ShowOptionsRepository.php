<?php

class ShowOptionsRepository{
    public function getShowOptions(int $locationID): ShowOptions
    {
        return ShowOptions::createFromPostMeta(get_post_meta($locationID, 'micerule_show_options', true));
    }

    public function saveShowOptions(int $locationID, ShowOptions $showOptions): void
    {
        update_post_meta($locationID, 'micerule_show_options', $showOptions);
    }
}