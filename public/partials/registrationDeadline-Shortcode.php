<?php

function registrationDeadline(){

  global $post;
  global $wpdb;
  // $registrationDeadline = get_post_meta($post->ID, 'micerule_data_event_deadline', true);
  $registrationDeadline = EventProperties::getEventDeadline($post->ID);

  $html = "";

  if ($registrationDeadline) {
    $weekday = date ('l', $registrationDeadline);
    $day = date('d', $registrationDeadline);
    $month = date('M', $registrationDeadline);
    $time = date('g.ia', $registrationDeadline);

    $html = '<span class="weekday">'.$weekday.'</span><br>
    <span class="day">'.$day.'<span>
    <span class="month">'.$month.'</span><br>
    <span class="time">'.$time.'</span>';
  }
  else $html = '<span class="tba">TBA</span>';

  return $html;
}

add_shortcode('registrationDeadline', 'registrationDeadline');
