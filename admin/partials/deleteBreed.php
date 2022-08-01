<?php

/*
* deletes given breed option
*
*/

global $wpdb;
global $post;

$name=$_POST['name'];
$category = $_POST['category'];

$option_name = "mrTables_".$name."_".$category;
$where = array("option_name"=>$option_name);
$table = $wpdb->prefix."options";

//get id of option to be deleted and update id_option
$id  = get_option($option_name)['id'];
$ids = get_option("mrOption_id");

//build new array without deleted option
$idUpdate = array();
foreach($ids as $key => $value){
  if($key != $id){
    $idUpdate[$key]=$value;
  }
}
//update option with array without deleted option
update_option("mrOption_id",$idUpdate);

//delete path option
$paths= get_option("mrOption_paths");
$pathUpdate= array();
foreach($paths as $key => $value){
  if($key != $id){
    $pathUpdate[$key]=$value;
  }
}
update_option("mrOption_paths",$pathUpdate);

//delete Breed Option
delete_option($option_name);

echo("success");

wp_die();
