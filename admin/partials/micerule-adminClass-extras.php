<?php

//Google Charts+ ddslick dependencies+ popover, adds to html head
function hook_javascript() {
  ?>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <script type="text/javascript" src="https://cdn.rawgit.com/prashantchaudhary/ddslick/master/jquery.ddslick.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

  <script src="https://leafo.net/sticky-kit/src/jquery.sticky-kit.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <!-- jQuery Modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
  <?php

}
add_action('wp_head', 'hook_javascript');
add_action('admin_head', 'hook_javascript');


//pagination on top, show results
function event_pagination($output){
  $string = str_replace('<span class="em-pagination"', '|<span class="em-pagination"', $output);
  $paginationTop ="<span class = 'em-paginationTop'>".explode('|', $string)[1]."</span>".$output;

  return $paginationTop;
}
add_filter('em_events_output','event_pagination');

//html callback for Season Results Menu
function mr_tables_menu_html($post){
  require_once plugin_dir_path(__FILE__) . 'templates/micerule-tables-menu-1-display.php';
}

//html callbck for Seson Results Icon Submenu
function mr_tables_sub_menu_icons_html(){
  require_once plugin_dir_path(__FILE__) . 'templates/micerule-tables-sub-menu-1-display.php';
}

//html callbck for Seson Results Colour Table Submenu
function mr_tables_sub_menu_cTable_html(){
  require_once plugin_dir_path(__FILE__) . 'templates/micerule-tables-sub-menu-cTable-display.php';
}


//html callback for Meta Box (Events Manager)
function micerule_meta_box_html_callback( $post ) {
  require_once plugin_dir_path( __FILE__ ) . 'templates/micerule-tables-post-meta-1-display.php';
}

//html callback for deadline select meta box
function micerule_deadline_meta_box_html_callback(){
  require_once plugin_dir_path( __FILE__ ) . 'templates/micerule-tables-event-deadline-select.php';
}


//html callback for Meta Box Event Breeds
function micerule_event_breed_meta_box_html_callback(){
  require_once plugin_dir_path( __FILE__ ) . 'templates/micerule-tables-event-breeds-display.php';
}

//remove admin footer
function remove_footer_admin ()
{
  echo '<span ></span>';
}
add_filter('admin_footer_text', 'remove_footer_admin');

function my_footer_shh() {
  remove_filter( 'update_footer', 'core_update_footer' );
}

add_action( 'admin_menu', 'my_footer_shh' );

//add Shortcodes
require_once plugin_dir_path( __FILE__ ) . 'seasonResults-Shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'currentResult-Shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'micerule-eventTables-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'micerule-judges-Shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'leaderboard-Shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'svg-Shortcode.php';
require_once plugin_dir_path(__FILE__).'sectionTables-Shortcode.php';
