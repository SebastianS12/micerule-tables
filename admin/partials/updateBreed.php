<?php

/*
* updates breed and path option 
*
*/

global $wpdb;
global $post;

$id = $_POST['id'];
$name = $_POST['name'];
$colour = $_POST['colour'];
$iconURL = $_POST['iconURL'];
$css_Class = "";
$section = $_POST['section'];
//css_Class based on upload path
if (!isset($iconURL)) {
  $upload_dir= BREED_ICONS_DIR.basename($_FILES["file"]["name"]);
  //Check if file is already uploaded
  if( !file_exists($upload_dir)){
    //file is not yet uploaded, so we upload it
    move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir);
  }
  
  $iconURL= BREED_ICONS_DIR_URL.basename($_FILES["file"]["name"]); 
}

$table_name = $wpdb->prefix . "micerule_breeds";
$data = array(
  "name" => $name,
  "colour" => $colour,
  "css_class" => $css_Class,
  "section" => $section,
  "icon_url" => $iconURL,
);
$where = array(
  "id" => $id,
);
$wpdb->update($table_name, $data, $where);

wp_die();
