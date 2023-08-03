<?php
global $wpdb;
global $post;

//Get all Season Result Tables to display
$seasonResultsModel = new SeasonResultsModel();
$seasonTables = $seasonResultsModel->getSeasonTableData();
?>

<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">

<!--------------Dialog HTML-------------------->
<div id="dialogText" style="display: none" title="Delete Table?"></div>

<!--------------Create Table-------------------->
<div class="micerule-backend">
<div class="micerule-title">
<img src="/wp-content/themes/Divi-child/Assets/logos/nmc_logo.png" width="84" height="80"/><h1>Top 20 Results</h1>
</div>
<h4 class="header-section">Create&nbsp;New&nbsp;Season&nbsp;Table</h4>
<div class="date-range">
<p><input type = "text" id = "datepicker-1" class="datepicker" placeholder="Start date" style="width:110px"></p>
<p style="font-size:16px;">to:&nbsp;&nbsp;<input type = "text" id = "datepicker-2" class="datepicker"  placeholder="End date" style="width:110px"></p>
<button type="button" id="tableCreate">Create Table</button>
</div>

<!----------------------------------------------->

<!--------------Season Results-------------------->
<div class="sResults">
<h4 class="header-section">Result&nbsp;Tables</h4>
<?php foreach ($seasonTables as $seasonTable) {
      echo '<div class="label-option">';
      echo '<p class="label-top">Showing Season from '.date('d/m/y',$seasonTable->dateFrom). ' to ' .date('d/m/y',$seasonTable->dateTo).'</p>';
      echo '<div class="control-cluster"><input type="checkbox" id="Check'.$seasonTable->mrtable_id.'" class="checkUpdate" name="Check'.$seasonTable->mrtable_id.'" value="'.$seasonTable->mrtable_id.'"'.(($seasonTable->seasonTable ==1)?"checked":"").' >';
      echo '<label for="Check'.$seasonTable->mrtable_id.'">Show on Leader Board</label>';
      echo '</div>';

      echo '</div>';
      echo '<div class="button-cluster"><button type="button" value="'.$seasonTable->mrtable_id.'" class="toggleButton">Show/Hide Table</button>     ';
      echo '     <button type="button" id="Delete'.$seasonTable->mrtable_id.'" class="deleteButton" value="'.$seasonTable->mrtable_id.'">Delete Table</button>';
      echo '</div>';
      echo '<div id="table'.$seasonTable->mrtable_id.'" style="height: 0px; overflow:hidden;">';
      echo SeasonResultsView::getSeasonTableHtml($seasonTable->dateFrom, $seasonTable->dateTo);
      echo '</div>';
 } ?>
</div>
<!---------------------------------------------------->

<!--------------Current Table-------------------->
<div id ="currentTable" class="sResultsCurrent" >
<h4 class="header-section">Running&nbsp;Season's&nbsp;Results</h4>
  <?php echo SeasonResultsView::getSeasonTableHtml($seasonResultsModel->getCurrentSeasonDateFrom(), time()); ?>
  <br><br>
</div>
</div>
<!------------------------------------------------>
 <?php
