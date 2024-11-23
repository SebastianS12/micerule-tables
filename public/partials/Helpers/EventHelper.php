<?php

class EventHelper {
    public static function getEventPostID(): int
    {
        // $url = wp_get_referer();
        // return url_to_postid($url);
        return get_the_ID();
    }
}