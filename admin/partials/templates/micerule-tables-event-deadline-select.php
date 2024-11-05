<?php
global $post;

$eventDeadline = date("Y-m-d H:i", EventDeadlineService::getEventDeadline($post->ID));

?>

<h3>Deadline</h3>
<input type = 'text' id = 'deadlineDatePicker' name = 'micerule_table_data_deadline' value = '<?php echo(isset($eventDeadline) ? $eventDeadline : "") ?>'></input>
