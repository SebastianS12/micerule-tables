<?php

global $wpdb;
global $post;

//Get data for Breeds,Age
require_once plugin_dir_path(__FILE__) . 'micerule-tables-categories-arrays.php';

$breedsAllSections = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "micerule_breeds", ARRAY_A);

//Get postmeta for current inputs
$scCheck = get_post_meta($post->ID, 'micerule_data_scCheck', true);

?>
<h3 style="display:none;">micerule_tables id="<?php echo $post->ID; ?>"</h3>
<input type="checkbox" id="addShortcode" value="<?php echo $post->ID; ?>">
<label for="addShortcode"><strong>Display as event table</strong> (Check this as soon as results have been entered below to have them displayed on the <a href="/show-results">Show Results</a> page)</label>
<br><br>
<input type="hidden" name="scCheck" id="hValue" value="<?php echo (isset($scCheck)) ? $scCheck : '0';?>">
<button type="button" id="unstandardised-row-toggle" class="addRow">Add/Hide Unstandardised Row</button>
<button type="button" id="junior-row-toggle" class="addRow">Add/Hide Junior Row</button>
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
  echo getTableRowHtml("BIS", "Best in Show", $BISData, $breedsAllSections, "grand challenge", 4, false);
  ?>
  </tr>
  <tr>
  <?php
   $BOAData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$post->ID." AND award = 'BOA'", ARRAY_A);
   echo getTableRowHtml("BOA", "Best Opposite Age", $BOAData, $breedsAllSections, "grand challenge", 3, true); 
  ?>
  </tr>
  <?php 
  foreach(EventProperties::SECTIONNAMES as $section){
    $BISecData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$post->ID." AND award = 'BISec' AND section = '".strtolower($section)."'", ARRAY_A);
    $BOSecData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results WHERE event_post_id = ".$post->ID." AND award = 'BOSec' AND section = '".strtolower($section)."'", ARRAY_A);
    $sectionBreeds = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."micerule_breeds WHERE upper(section) = '".$section."'", ARRAY_A);
    echo "<tr>".getTableRowHtml("BISec", "Best ".$section, $BISecData, $sectionBreeds, strtolower($section), 2, false)."</tr>";
    echo "<tr>".getTableRowHtml("BOSec", "Best Opposite Age ".$section, $BOSecData, $sectionBreeds, strtolower($section), 1, true)."</tr>";
  }

  $juniorData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results_optional WHERE event_post_id = ".$post->ID." AND class_name = 'junior'", ARRAY_A);
  echo getOptionalTableRowHtml("junior", $breedsAllSections, $juniorData);
  $unstandardisedData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_results_optional WHERE event_post_id = ".$post->ID." AND class_name = 'unstandardised'", ARRAY_A);
  echo getOptionalTableRowHtml("unstandardised", $breedsAllSections, $unstandardisedData);
  ?>
</table>

<?php
function getTableRowHtml($award, $displayedAward, $rowData, $breeds, $section, $points, bool $ageDisabled){
  $fancierName = (isset($rowData['fancier_name'])) ? $rowData['fancier_name'] : "";
  $varietyName = (isset($rowData['variety_name'])) ? $rowData['variety_name'] : "";
  $defaultAge = ($ageDisabled) ? "U8" : "Ad";
  $age = (isset($rowData['age']) && $rowData['age'] != "") ? $rowData['age'] : $defaultAge;
  $html = "<td>".$displayedAward."</td>";
  $html .= "<td>".getFancierSelectHtml($fancierName, "micerule_table_data[".$section."][".$award."][fancier_name]")."</td>";
  $html .= "<td>".getVarietySelectHtml($breeds, $varietyName, "micerule_table_data[".$section."][".$award."][variety_name]")."</td>";
  $html .= (!$ageDisabled) ?
    "<td>".getAgeSelectHtml($age, "micerule_table_data[".$section."][".$award."][age]", $ageDisabled, $section)."</td>"
    :
    "<td>".getOAAgeHtml($age, "micerule_table_data[".$section."][".$award."][age]", $section)."</td>";
  // $html .= "<td>".getAgeSelectHtml($age, "micerule_table_data[".$section."][".$award."][age]", $ageDisabled, $section)."</td>";
  $html .= "<input type='hidden' name='micerule_table_data[".$section."][".$award."][data_id]' value='".((isset($rowData['id'])) ? $rowData['id'] : "")."'>";  
  $html .= "<td><input type='hidden' name='micerule_table_data[".$section."][".$award."][points]' value='".$points."'>".$points."</td>";

  return $html;
}

function getOptionalTableRowHtml($optionalClassName, $breeds, $rowData){
  $fancierName = (isset($rowData['fancier_name'])) ? $rowData['fancier_name'] : "";
  $varietyName = (isset($rowData['variety_name'])) ? $rowData['variety_name'] : "";
  $html = "<tr ".(!isset($rowData) ? "style='display:none'" : "")." id='".$optionalClassName."Row'>
            <td>Best ".strtoupper($optionalClassName)."</td>";
  $html .= "<td>".getFancierSelectHtml($fancierName, "micerule_table_data_optional[".$optionalClassName."][fancier_name]")."</td>";
  $html .= "<td>".getVarietySelectHtml($breeds, $varietyName, "micerule_table_data_optional[".$optionalClassName."][variety_name]")."</td>";
  $html .= "<td>AA</td>";
  $html .= "<td>0</td>";
  $html .= "</tr>";

  return $html;
}

function getFancierSelectHtml($fancierName, $selectName){
  global $wpdb;
  $users = (array) $wpdb->get_results("SELECT display_name FROM " . $wpdb->prefix . "users ORDER BY display_name;");

  $html = "<select class = 'fancier-select' name='".$selectName."' autocomplete='off'>
              <option value=''>Please Select</option>";
  foreach($users as $user){
    $html .= "<option value='".$user->display_name."' ".((isset($fancierName) && $fancierName == $user->display_name) ? 'selected="selected"' : '').">";
    $html .= $user->display_name;
    $html .= "</option>";
  }
  $html .= "</select>";

  return $html;
}

function getVarietySelectHtml($breeds, $varietyName, $selectName){
  $html = "<select class = 'variety-select' name='".$selectName."' style='width:200px' autocomplete='off'>
                <option value='No Record'>No Record</option>";
  foreach ($breeds as $breed) {
    $html .= "<option value='".$breed['name']."' ".((isset($varietyName) && $varietyName == $breed['name']) ? 'selected="selected"' : '').">";
    $html .= $breed['name'];
    $html .= "</option>";
  }
  $html .= "</select>";

  return $html;
}

function getAgeSelectHtml($selectedAge, $selectName, bool $ageDisabled, string $section){
  $selectDisabled = ($ageDisabled) ? "disabled" : "";
  $selectID = (!$ageDisabled) ? $section."-age-select" : $section."-age-select-oa";
  $html = "<select id='".$selectID."' class = 'age-select' name='".$selectName."' autocomplete='off' ".$selectDisabled.">";
  foreach (EventProperties::AGESECTIONS as $age) {
    $html .= "<option value='".$age."' ".((isset($selectedAge) && $selectedAge == $age) ? 'selected="selected"' : '').">";
    $html .= $age;
    $html .= "</option>";
  }
  $html .= "</select>";

  return $html;
}

function getOAAgeHtml($selectedAge, $selectName, string $section): string 
{
  $selectID = $section."-age-select-oa";
  $html = "<input type='text' id='".$selectID."' class = 'age-select age-select-disabled' name='".$selectName."' value='".$selectedAge."' readonly>";
  $html .= "</select>";

  return $html;
}

include("micerule-tables-post-meta-2-display.php");
