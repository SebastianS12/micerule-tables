<?php
global $wpdb;
global $post;

//Get all user names
$users = (array) $wpdb->get_results("SELECT display_name FROM " .$wpdb->prefix."users ORDER BY display_name;");

$locationSecretaryNames = LocationSecretariesService::getLocationSecretaries(LocationHelper::getIDFromLocationPostID($post->ID));
?>
<h3>Location Secretaries</h3>
<select autocomplete = "off" name= "micerule_table_location_secretaries_names[]">
  <option value=''>Please Select</option>
  <?php

  foreach($users as $user){ ?>
    <option  value="<?php echo $user->display_name;?>" <?php if(isset($locationSecretaryNames[0])){ echo ($user->display_name == $locationSecretaryNames[0]) ? ' selected="selected"' : ''; }?>><?php echo $user->display_name;?></option>

      <?php
    }
    ?>
  </select>

  <select autocomplete = "off" name= "micerule_table_location_secretaries_names[]">
    <option value=''>Please Select</option>
    <?php
    foreach($users as $user){ ?>
      <option  value="<?php echo $user->display_name;?>" <?php if(isset($locationSecretaryNames[1])){ echo ($user->display_name == $locationSecretaryNames[1]) ? ' selected="selected"' : ''; }?>><?php echo $user->display_name;?></option>

        <?php
      }
      ?>
  </select>
