<?php

global $wpdb;

//Get data for Breeds,Age
require_once plugin_dir_path(__FILE__).'micerule-tables-categories-arrays.php';

//Get Uploads for Select
require_once plugin_dir_path(__FILE__).'../getUploads.php';

//Path for form action path
$addBreedPath= plugin_dir_url(__dir__).'addBreed.php';

$categories = array("Selfs","Marked","Satins","AOVs","Tans");

$defaultPath = get_home_url()."/wp-content/themes/Divi-child/Assets/spacer.gif";

//Get Breeds from Options Database
$breeds=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mrTables%'",ARRAY_A);

?>

<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">
<link href="/wp-content/plugins/micerule-tables/admin/css/style.css" rel="stylesheet">

<!--------------Dialog HTML-------------------->





<div class="micerule-breed-settings">

    <div class="micerule-header">

    <div class="manageIcons" style="display:block">
          <select name= "deleteUpload" id="deleteUpload">
            <option value="">Select upload to delete</option>
              <?php
                foreach($uploads as $value){
                  $id = basename($value,".".pathinfo($value)['extension']); ?>
                       <option value="<?php echo $value; ?>"><?php echo $id; ?></option>

                      <?php
                  }
                          ?>

          </select>
          <button id="deleteIcon">Delete</button>

          <div class='uploader-area' id='upload-area-multiple'>
          <div class='drop-area-holder'>

          <input type='file' class='fileUploadMultiple'  id='fileUploadMultiple' multiple>
          <div style='display:flex; flex-direction:column; justify-content:center; align-items:center;'><img src='/wp-content/themes/Divi-child/Assets/Icons/cloud-upload.svg' width='73' height='53' /><span id="uploadSpan" style='pointer-events:none;'>Drop files here</span><span id='or'>or</span>
          <label for='fileUploadMultiple' id='labelUploadMultiple'>Select File</label>

        </div>
      </div>
    </div>
    <button class='submitUploadsMultiple'>Submit Files</button>
          </div>


        <div class="micerule-title">
        <img src="/wp-content/themes/Divi-child/Assets/logos/nmc_logo.png" width="84" height="80"/><h1>Breed Settings</h1>
        </div>
        <div class="add-breed">
            <div id="dialogText" style="display: none" title="Delete Breed?"></div>
            <div style="display:flex">
            <button id="addBreedButton" style="margin-right:6px">Add Breed</button>
            </div>
          </div>
    </div>

    <div class="backend-split-pane">
        <div class="overview">

            <table id="overviewTable">
            <thead>
            <tr>
            <th>Icon</th>
            <th>Breed</th>
            <th style="display:none" id="colourHead">Colour</th>
            <th>Section</th>
            </tr>
            </thead>
            <tbody class= "overviewTableBody">
            <?php
            for($i=0;$i<count($breeds);$i++){
            echo '<tr>';
            echo '<td><div class="icon-bg" style="background-color:'.get_option($breeds[$i]["option_name"])["colour"].'"><img src="'. ((isset(get_option("mrOption_paths")[get_option($breeds[$i]['option_name'])['id']])) ?  get_option("mrOption_paths")[get_option($breeds[$i]['option_name'])['id']]: $defaultPath).'" width="50" height="50" ></div></td>';
            echo '<td class="name" style="width:100%">'.get_option($breeds[$i]['option_name'])['name'].'</td>';
            echo '<td>'.get_option($breeds[$i]['option_name'])['category'].'</td>';
            echo '<td style="display:none;background-color:'.get_option($breeds[$i]["option_name"])["colour"].'"></td>';
            }
            ?>
            </tbody>
            </table>
        </div>
        <div class="preview-pane">


          </div>
      </div>
</div>
