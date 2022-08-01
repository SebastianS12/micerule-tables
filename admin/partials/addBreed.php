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

$upload_dir= ABSPATH."wp-content/plugins/micerule-tables/admin/svg/".basename($_FILES["file"]["name"]);


//set path
if(isset($_POST['path'])){
  $url = $_POST['path'];
  $css_Class = basename($url,".".pathinfo($url)['extension']);
}else{
  $url =$upload_dir;
  //css_Class based on upload url
  $css_Class = basename($url,".".pathinfo($url)['extension']);

}


//prepare data, get data from ajax post
if(isset($_POST['name'])){
  $data = array("name"=>$_POST['name'], "colour"=>$_POST['colour'],"class"=>$css_Class,"category"=>$_POST['category']);
}

if(isset($_POST['name'])){
  //set option_name based on name and category and add option
  $option_name = 'mrTables_'.$_POST['name'].'_'.$_POST['category'];
  add_option($option_name,$data);


  //add option id of inserted option to option_value
  $options=$wpdb->get_results("SELECT option_id FROM ".$wpdb->prefix."options WHERE option_name ='" .$option_name."'",ARRAY_N);
  if(isset($options[0][0])){
    $id= $options[0][0];
  }
  $data['id']=$id;
  update_option($option_name,$data);

  //update option_id option
  $ids = get_option("mrOption_id");
  $ids[$id]= $option_name;
  update_option("mrOption_id",$ids);
}

//update svg-Path:

//get path option array
$paths = get_option("mrOption_paths");
//update svg-Path:
if(isset($_POST['path'])){  //Selected from already uploaded
  //update path option

  $paths[$id]= $_POST['path'];
  update_option("mrOption_paths",$paths);
}else{



  //Check if file is already uploaded
  if( !file_exists($upload_dir)){
    //file is not yet uploaded, so we upload it
    move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir);

  }

  //update path option
  $paths[$id]= content_url()."/plugins/micerule-tables/admin/svg/".basename($_FILES["file"]["name"]);
  update_option("mrOption_paths",$paths);

}//end else fileUpload



echo(var_dump($data));

wp_die();
