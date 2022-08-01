<?php

/*
* updates breed and path option 
*
*/

global $wpdb;
global $post;

$upload_dir= ABSPATH."wp-content/plugins/micerule-tables/admin/svg/".basename($_FILES["file"]["name"]);

$name= $_POST['name'];
$name_New = $_POST['name_New'];
$colour= $_POST['colour'];
//css_Class based on upload path
if(isset($_POST['path'])){
  $url = $_POST['path'];
  $css_Class = basename($url,".".pathinfo($url)['extension']);
}else{
  $url = $upload_dir;
  //css_Class based on upload url
  $css_Class = basename($url,".".pathinfo($url)['extension']);

}
$category = $_POST['category'];
$category_New = $_POST['category_New'];

$option_name= "mrTables_".$name."_".$category;
$id  = get_option($option_name)['id'];

//prepare data
$table = $wpdb->prefix."options";
$data = array("name"=>$name_New,
"colour"=>$colour,
"class"=>$css_Class,
"category"=>$category_New,
"id"=>$id);





update_option($option_name,$data);

//update option_id option
$ids = get_option("mrOption_id");
$ids[$id]= "mrTables_".$name_New."_".$category_New;
update_option("mrOption_id",$ids);


$dbData = array("option_name"=> "mrTables_".$name_New."_".$category_New);
$where = array("option_name"=>$option_name);

$wpdb->update($table,$dbData,$where);

//update svg-Path:

//get path option array
$paths = get_option("mrOption_paths");
//update svg-Path:
if(isset($_POST['path'])){
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



echo "";


wp_die();
