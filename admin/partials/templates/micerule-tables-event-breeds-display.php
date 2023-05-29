<?php
global $wpdb;
global $post;

//Get all user names
$users = (array) $wpdb->get_results("SELECT display_name FROM " .$wpdb->prefix."users ORDER BY display_name;");

$sectionData = get_post_meta($post->ID, 'micerule_data_event_classes',true);
$locationSecretary = get_post_meta($post->ID, 'micerule_data_location_secretaries',true);
?>
<h2><?php echo $post->ID; ?></h2>
<h2><?php echo var_dump($sectionData); ?></h2>

<h3>Location Secretaries</h3>
<select autocomplete = "off" name= "micerule_table_location_secretaries[name][]">
  <option value=''>Please Select</option>
  <?php

  foreach($users as $user){ ?>
    <option  value="<?php echo $user->display_name;?>" <?php if(isset($locationSecretary['name'][0])){ echo ($user->display_name == $locationSecretary['name'][0]) ? ' selected="selected"' : ''; }?>><?php echo $user->display_name;?></option>

      <?php
    }
    ?>
  </select>

  <select autocomplete = "off" name= "micerule_table_location_secretaries[name][]">
    <option value=''>Please Select</option>
    <?php
    foreach($users as $user){ ?>
      <option  value="<?php echo $user->display_name;?>" <?php if(isset($locationSecretary['name'][1])){ echo ($user->display_name == $locationSecretary['name'][1]) ? ' selected="selected"' : ''; }?>><?php echo $user->display_name;?></option>

        <?php
      }
      ?>
  </select>
