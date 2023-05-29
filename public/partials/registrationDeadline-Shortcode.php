<?php

function registrationDeadline(){

  global $post;
  $registrationDeadline = get_post_meta($post->ID, 'micerule_data_event_deadline', true);

  $html = "";

  if ($registrationDeadline) {
    $time = strtotime($registrationDeadline);

    $weekday = date ('l', $time);
    $day = date('d', $time);
    $month = date('M', $time);
    $time = date('h.ia', $time);

    $html = '<span class="weekday">'.$weekday.'</span><br>
    <span class="day">'.$day.'<span>
    <span class="month">'.$month.'</span><br>
    <span class="time">'.$time.'</span>';
  }
  else $html = '<span class="tba">TBA</span>';

  return $html;
}

add_shortcode('registrationDeadline', 'registrationDeadline');
