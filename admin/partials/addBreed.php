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
/*
$name = $_POST['name'];
$colour = $_POST['colour'];
$section = $_POST['section'];
$iconURL = $_POST['iconURL'];
$cssClass = "";

//set path
if(!isset($iconURL)){
  $upload_dir= ABSPATH."wp-content/plugins/micerule-tables/admin/svg/breed-icons/".basename($_FILES["file"]["name"]);
  //Check if file is already uploaded
  if( !file_exists($upload_dir)){
    //file is not yet uploaded, so we upload it
    move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir);
  }
  //update path option
  $iconURL= content_url()."/plugins/micerule-tables/admin/svg/breed-icons/".basename($_FILES["file"]["name"]); 
}
$cssClass = basename($iconURL,".".pathinfo($iconURL)['extension']);

if(isset($name)){
  $breedTableName = $wpdb->prefix."micerule_breeds";
  $wpdb->insert(
    $breedTableName,
    array(
      'name' => $name,
      'colour' => $colour,
      'css_class' => $cssClass,
      'section' => $section,
      'icon_url' => $iconURL,
    )
  );
}*/

//move options to DB
/*
$options = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%'",ARRAY_A);
$paths = get_option("mrOption_paths");
foreach($options as $option){
  $id = $option['option_id'];
  $iconURL = $paths[$id];
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
      'icon_url' => $iconURL,
    )
  );
}*/


//move icon files and adjust icon urls
$breedPaths = $wpdb->get_results("SELECT id, icon_url FROM " . $wpdb->prefix . "micerule_breeds", ARRAY_A);
foreach($breedPaths as $index => $path){
  $targetPath = BREED_ICONS_DIR.basename($path['icon_url']);//ABSPATH."wp-content/plugins/micerule-tables/admin/svg/breed-icons/".basename($path["icon_url"]);
  rename(download_url(/*$path['icon_url']*/$srcURL), $targetPath);
    //echo(plugin_dir_url(__FILE__)."admin/svg/breed-icons/".basename($path['icon_url']));
  $iconURL = BREED_ICONS_DIR_URL.basename($path['icon_url']);
  $wpdb->update($wpdb->prefix."micerule_breeds", array('icon_url' => $iconURL), array('id' => $path['id']));
}

wp_die();
