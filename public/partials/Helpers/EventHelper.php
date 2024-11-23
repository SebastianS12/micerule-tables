<?php

class EventHelper {
    public static function getEventPostID() {
        $url = wp_get_referer();
        return url_to_postid($url);
    }
}