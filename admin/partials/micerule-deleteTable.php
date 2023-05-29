<?php

/*
* deletes table from db, based on id 
*
*/

global $wpdb;
global $post;

//Get Table ID to delete
$id = $_POST['id'];

//prepare data for sql query
$table = $wpdb->prefix.'micerule_result_tables';
$where = ['mrtable_id' => $id];

//delete table
$wpdb->delete($table, $where);

echo($id);

wp_die();
