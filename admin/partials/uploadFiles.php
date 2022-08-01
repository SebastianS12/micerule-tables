<?php

/*
* bulk icon upload  
*
*/

global $post;

foreach($_FILES as $value){ //go through selected files

  $upload_dir= ABSPATH."wp-content/plugins/micerule-tables/admin/svg/".basename($value["name"]);
  //Check if file is already uploaded
      if( !file_exists($upload_dir)){
      	//file is not yet uploaded, so we upload it
         move_uploaded_file($value['tmp_name'], $upload_dir);

          }

}

echo var_dump($upload_dir);

wp_die();
