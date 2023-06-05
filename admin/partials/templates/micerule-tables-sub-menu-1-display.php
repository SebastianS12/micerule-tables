<?php
global $wpdb;
//Get Uploads for Select
require_once plugin_dir_path(__FILE__) . '../getUploads.php';
$defaultPath = get_home_url() . "/wp-content/themes/Divi-child/Assets/spacer.gif";
//Get Breeds from Options Database
$breeds = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "micerule_breeds", ARRAY_A);
?>

<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">
<link href="/wp-content/plugins/micerule-tables/admin/css/style.css" rel="stylesheet">

<div class="micerule-breed-settings">
  <div class="micerule-header">
    <div class="manageIcons" style="display:block">
      <select name="deleteUpload" id="deleteUpload">
        <option value="">Select upload to delete</option>
        <?php
        foreach ($uploads as $value) {
          $iconName = basename($value, "." . pathinfo($value)['extension']); ?>
          <option value="<?php echo $value; ?>"><?php echo $iconName; ?></option>
        <?php
        }
        ?>
      </select>
      <button id="deleteIcon">Delete</button>
      <div class='uploader-area' id='upload-area-multiple'>
        <div class='drop-area-holder'>
          <input type='file' class='fileUploadMultiple' id='fileUploadMultiple' multiple>
          <div style='display:flex; flex-direction:column; justify-content:center; align-items:center;'><img src='/wp-content/themes/Divi-child/Assets/Icons/cloud-upload.svg' width='73' height='53' /><span id="uploadSpan" style='pointer-events:none;'>Drop files here</span><span id='or'>or</span>
            <label for='fileUploadMultiple' id='labelUploadMultiple'>Select File</label>
          </div>
        </div>
      </div>
      <button class='submitUploadsMultiple'>Submit Files</button>
    </div>

    <div class="micerule-title">
      <img src="/wp-content/themes/Divi-child/Assets/logos/nmc_logo.png" width="84" height="80" />
      <h1>Breed Settings</h1>
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
        <tbody class="overviewTableBody">
          <?php
          foreach ($breeds as $breed) {
            $path = BREED_ICONS_DIR.basename($breed["icon_url"]);//ABSPATH."wp-content/plugins/micerule-tables/admin/svg/breed-icons/".basename($breed["path"]);
            echo '<tr>';
            echo '<td><div class="icon-bg" style="background-color:' . $breed['colour'] . '" data-id = "'.$breed['id'].'"><img src="' .((file_exists($path)) ?  $breed['icon_url'] : $defaultPath) . '" width="50" height="50" ></div></td>';
            echo '<td class="breed-name" style="width:100%">' . $breed['name'] . '</td>';
            echo '<td class = "breed-section">' . $breed['section'] . '</td>';
            echo '<td style="display:none;background-color:' . $breed['colour'] . '"></td>';
          }
          ?>
        </tbody>
      </table>
    </div>
    <div class="preview-pane">
    </div>
  </div>
</div>