<?php
global $post;

$eventDeadline = get_post_meta($post->ID, 'micerule_data_event_deadline', true);

?>

<h3>Deadline</h3>
<input type = 'text' id = 'deadlineDatePicker' name = 'micerule_table_data_deadline' value = '<?php echo(isset($eventDeadline) ? $eventDeadline : "") ?>'></input>
