<?php

global $wpdb;
global $post;

//Get all user names
$users = (array) $wpdb->get_results("SELECT display_name FROM " . $wpdb->prefix . "users ORDER BY display_name;");

//Get data for Breeds,Age
require_once plugin_dir_path(__FILE__) . 'micerule-tables-categories-arrays.php';

$breeds = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "micerule_breeds", ARRAY_A);

$breedsOSelfs = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'mrTables%Selfs'", ARRAY_A);

$breedsOTans = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'mrTables%Tans'", ARRAY_A);

$breedsOMarked = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'mrTables%Marked'", ARRAY_A);

$breedsOSatins = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'mrTables%Satins'", ARRAY_A);

$breedsOAOVs = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'mrTables%AOVs'", ARRAY_A);

//wp_nonce_field('micerule_save_metabox_data','micerule_save_nonce_check');

//Get postmeta for current inputs
$meta = get_post_meta($post->ID, 'micerule_data_settings', true);
$scCheck = get_post_meta($post->ID, 'micerule_data_scCheck', true);

?>
<h3 style="display:none;">micerule_tables id="<?php echo $post->ID; ?>"</h3>
<input type="checkbox" id="addShortcode" value="<?php echo $post->ID; ?>">
<label for="addShortcode"><strong>Display as event table</strong> (Check this as soon as results have been entered below to have them displayed on the <a href="/show-results">Show Results</a> page)</label>
<br><br>
<input type="hidden" name="scCheck" id="hValue" value="<?php echo (isset($scCheck)) ? $scCheck : '0';?>">
<button type="button" id="addRowU" class="addRow">Add/Hide Unstandardised Row</button>
<button type="button" id="addRowJ" class="addRow">Add/Hide Junior Row</button>
<br><br>
<table style="width:100%">
  <tr style="text-align:left">
    <th>Award</th>
    <th>Fancier</th>
    <th>Variety</th>
    <th>Age</th>
    <th>Points</th>
  </tr>
  <tr>
  <?php 
  $BISData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$post->ID." AND award = 'BIS'", ARRAY_A);
  echo getTableRowHtml("BIS", "Best in Show", $users, $BISData, $breeds, "Grand Challenge", 4);
  ?>
  </tr>
  <tr>
  <?php
   $BOAData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$post->ID." AND award = 'BOA'", ARRAY_A);
   echo getTableRowHtml("BOA", "Best Opposite Age", $users, $BOAData, $breeds, "Grand Challenge", 3); 
  ?>
  </tr>
  <?php 
  foreach(EventProperties::SECTIONNAMES as $section){
    $BISecData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$post->ID." AND award = 'BISec' AND section = '".strtolower($section)."'", ARRAY_A);
    $BOSecData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$post->ID." AND award = 'BOSec' AND section = '".strtolower($section)."'", ARRAY_A);
    echo "<tr>".getTableRowHtml("BISec", "Best ".$section, $users, $BISecData, $breeds, strtolower($section), 2)."</tr>";
    echo "<tr>".getTableRowHtml("BOSec", "Best Opposite Age ".$section, $users, $BOSecData, $breeds, strtolower($section), 1)."</tr>";
  }
  ?>
</table>


<?php

function getTableRowHtml($award, $displayedAward, $users, $rowData, $breeds, $section, $points){
  $html = "<td>".$displayedAward."</td>
           <td>
            <select name='micerule_table_data[".$section."][".$award."][fancier_name]' autocomplete='off'>
              <option value=''>Please Select</option>";
  foreach($users as $user){
    $html .= "<option value='".$user->display_name."' ".((isset($rowData['fancier_name']) && $rowData['fancier_name'] == $user->display_name) ? 'selected="selected"' : '').">";
    $html .= $user->display_name;
    $html .= "</option>";
  }
  $html .= "</select></td>";

  $html .= "<td>
              <select name='micerule_table_data[".$section."][".$award."][variety_name]' style='width:200px' autocomplete='off'>
                <option value=''>No Record</option>";
  foreach ($breeds as $breed) {
    $html .= "<option value='".$breed['name']."' ".((isset($rowData['variety_name']) && $rowData['variety_name'] == $breed['name']) ? 'selected="selected"' : '').">";
    $html .= $breed['name'];
    $html .= "</option>";
  }
  $html .= "</select></td>";

  $html .= "<td>
              <select id='ageBIS1' name='micerule_table_data[".$section."][".$award."][age]' autocomplete='off'>";
  foreach (EventProperties::AGESECTIONS as $age) {
    $html .= "<option value='".$age."' ".((isset($rowData['age']) && $rowData['age'] == $age) ? 'selected="selected"' : '').">";
    $html .= $age;
    $html .= "</option>";
  }
  $html .= "</select></td>";

  $html .= "<input type='hidden' name='micerule_table_data[".$section."][".$award."][data_id]' value='".((isset($rowData['id'])) ? $rowData['id'] : "")."'>";  
  $html .= "<td><input type='hidden' name='micerule_table_data[".$section."][".$award."][points]' value='".$points."'>".$points."</td>";

  return $html;
}

include("micerule-tables-post-meta-2-display.php");
