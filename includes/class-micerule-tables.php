<?php
class Micerule_Tables{

  protected $loader;

  protected $plugin_name;

  protected $version;

  public function __construct(){
    $this->plugin_name = 'micerule_tables';
    $this->version = '2.0';
    $this->define_constants();
    $this->load_dependencies();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  private function define_constants(){
    define("BREED_ICONS_DIR", ABSPATH."wp-content/plugins/micerule-tables/res/breed-icons/");
    define("BREED_ICONS_DIR_URL", plugin_dir_url("")."micerule-tables/res/breed-icons/");
  }

  private function load_dependencies(){
    require_once plugin_dir_path(dirname(__FILE__)).'includes/class-micerule-tables-loader.php';
    require_once plugin_dir_path(dirname(__FILE__)).'admin/micerule-tables-admin.php';
    require_once plugin_dir_path(dirname(__FILE__)).'public/class-micerule-tables-public.php';

    $this->loader = new Micerule_Tables_Loader();
  }

  private function define_admin_hooks(){
    $plugin_admin = new Micerule_Tables_Admin($this->get_plugin_name(), $this->get_version());

    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

    //add ajax actions
    $this->loader->add_action('wp_ajax_tableCreate',$plugin_admin,'tableCreate');

    $this->loader->add_action('wp_ajax_deleteIcon',$plugin_admin,'deleteIcon');

    $this->loader->add_action('wp_ajax_uploadFiles',$plugin_admin,'uploadFiles');

    $this->loader->add_action('wp_ajax_updateTable',$plugin_admin,'updateTable');

    $this->loader->add_action('wp_ajax_deleteTable',$plugin_admin,'deleteTable');

    $this->loader->add_action('wp_ajax_updateBreed',$plugin_admin,'updateBreed');

    $this->loader->add_action('wp_ajax_deleteBreed',$plugin_admin,'deleteBreed');

    $this->loader->add_action('wp_ajax_addBreed',$plugin_admin,'addBreed');

    $this->loader->add_action('wp_ajax_getUploads',$plugin_admin,'getUploads');

    $this->loader->add_action('admin_menu',$plugin_admin,'mr_tables_menu');

    $this->loader->add_action('admin_menu',$plugin_admin,'mr_tables_sub_menu');

    $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'micerule_add_meta_box' );
    //$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'micerule_add_deadline_meta_box' );
    $this->loader->add_action('add_meta_boxes', $plugin_admin, 'micerule_add_breeds_meta_box');

    $this->loader->add_action('save_post',$plugin_admin,'micerule_save_metabox_data');
  }

  private function define_public_hooks() {
    $plugin_public = new Micerule_Tables_Public( $this->get_plugin_name(), $this->get_version() );

    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    //add ajax actions
    $this->loader->add_action('wp_ajax_lbTables',$plugin_public,'lbTables');
    $this->loader->add_action('wp_ajax_nopriv_lbTables',$plugin_public,'lbTables');

    $this->loader->add_action('wp_ajax_addClass',$plugin_public,'addClass');

    $this->loader->add_action('wp_ajax_registerClasses',$plugin_public,'registerClasses');
    $this->loader->add_action('wp_ajax_getClassSelectOptions',$plugin_public,'getClassSelectOptions');
    $this->loader->add_action('wp_ajax_moveClass',$plugin_public,'moveClass');
    $this->loader->add_action('wp_ajax_deleteClass',$plugin_public,'deleteClass');
    $this->loader->add_action('wp_ajax_eventOptionalSettings', $plugin_public, 'eventOptionalSettings');
    $this->loader->add_action('wp_ajax_updateRegistrationTables', $plugin_public, 'updateRegistrationTables');
    $this->loader->add_action('wp_ajax_moveEntry', $plugin_public, 'moveEntry');
    $this->loader->add_action('wp_ajax_addEntry', $plugin_public, 'addEntry');
    $this->loader->add_action('wp_ajax_deleteEntry', $plugin_public, 'deleteEntry');
    $this->loader->add_action('wp_ajax_editPlacement', $plugin_public, 'editPlacement');
    $this->loader->add_action('wp_ajax_editBIS', $plugin_public, 'editBIS');
    $this->loader->add_action('wp_ajax_getSelectOptions', $plugin_public, 'getSelectOptions');
    $this->loader->add_action('wp_ajax_editAbsent', $plugin_public, 'editAbsent');
    $this->loader->add_action('wp_ajax_setAllAbsent', $plugin_public, 'setAllAbsent');
    $this->loader->add_action('wp_ajax_setCustomClassVariety', $plugin_public, 'setCustomClassVariety');
    $this->loader->add_action('wp_ajax_printAll', $plugin_public, 'printAll');
    $this->loader->add_action('wp_ajax_moveToUnprinted', $plugin_public, 'moveToUnprinted');
    $this->loader->add_action('wp_ajax_submitReport', $plugin_public, 'submitReport');
    $this->loader->add_action('wp_ajax_createShowPost', $plugin_public, 'createShowPost');
    $this->loader->add_action('wp_ajax_getAdminTabsHtml', $plugin_public, 'getAdminTabsHtml');
  }

  public function run() {
    $this->loader->run();
  }


  public function get_plugin_name() {
    return $this->plugin_name;
  }

  /**
  * The reference to the class that orchestrates the hooks with the plugin.
  *
  * @since     1.0.0
  * @return    Jtrt_Responsive_Tables_Loader    Orchestrates the hooks of the plugin.
  */
  public function get_loader() {
    return $this->loader;
  }

  /**
  * Retrieve the version number of the plugin.
  *
  * @since     1.0.0
  * @return    string    The version number of the plugin.
  */
  public function get_version() {
    return $this->version;
  }
}
