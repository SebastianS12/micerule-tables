<?php

class EventDeadlineService{
    public static function saveEventDeadline(int $eventPostID, string $eventDeadline): void
    {
        update_post_meta($eventPostID, "micerule_event_deadline", strtotime($eventDeadline));
    }
    
    public static function getEventDeadline(int $eventPostID): int
    {
      $deadline = get_post_meta($eventPostID, "micerule_event_deadline", true);
      if($deadline == '') return time();
      return ($deadline != '') ? intval($deadline) : time();
    }

    public static function convert(): void
    {
      global $wpdb;
      foreach($wpdb->get_results("SELECT * FROM sm1_micerule_event_deadline", ARRAY_A) as $row){
        update_post_meta($row['event_post_id'], "micerule_event_deadline", $row['event_deadline']);
      }
    }
}