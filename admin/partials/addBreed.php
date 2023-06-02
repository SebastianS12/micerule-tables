<?php
/*
* adds breed to option db and uploads icon 
*
*/

if ( !defined('ABSPATH') ) {
  //If wordpress isn't loaded load it up.
  $path = $_SERVER['DOCUMENT_ROOT'];
  include_once $path . '/wp-load.php';
}

global $wpdb;
global $post;

$name = $_POST['name'];
$colour = $_POST['colour'];
$section = $_POST['section'];
$filePath = $_POST['path'];
$cssClass = "";

//set path
if(!isset($filePath)){
  $upload_dir= ABSPATH."wp-content/plugins/micerule-tables/admin/svg/".basename($_FILES["file"]["name"]);
  //Check if file is already uploaded
  if( !file_exists($upload_dir)){
    //file is not yet uploaded, so we upload it
    move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir);
  }
  //update path option
  $filePath= content_url()."/plugins/micerule-tables/admin/svg/".basename($_FILES["file"]["name"]); 
}
$cssClass = basename($filePath,".".pathinfo($filePath)['extension']);

if(isset($name)){
  $breedTableName = $wpdb->prefix."micerule_breeds";
  $wpdb->insert(
    $breedTableName,
    array(
      'name' => $name,
      'colour' => $colour,
      'css_class' => $cssClass,
      'section' => $section,
      'path' => $filePath,
    )
  );
}

//move options to DB
/*
$options = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%'",ARRAY_A);
$paths = get_option("mrOption_paths");
foreach($options as $option){
  $id = $option['option_id'];
  $path = $paths[$id];
  $option = get_option($option['option_name']);
  $name = $option['name'];
  $colour = $option['colour'];
  $cssClass = $option['class'];
  $section = $option['category'];
  
  $breedTableName = $wpdb->prefix."micerule_breeds";
  $wpdb->insert(
    $breedTableName,
    array(
      'name' => $name,
      'colour' => $colour,
      'css_class' => $cssClass,
      'section' => $section,
      'path' => $path,
    )
  );
}*/

wp_die();
