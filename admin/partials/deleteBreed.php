<?php

/*
* deletes given breed option
*
*/

global $wpdb;
global $post;

$id = $_POST['id'];
$table_name = $wpdb->prefix . "micerule_breeds";
$where = array("id" => $id);
$wpdb->delete($table_name, $where);

wp_die();
