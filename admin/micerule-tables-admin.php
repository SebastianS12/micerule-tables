<?php
require_once plugin_dir_path(__FILE__) . 'partials/ResultTable.php';
require_once plugin_dir_path(__FILE__) . 'partials/EventJudges.php';
class Micerule_Tables_Admin{

  private $plugin_name;

  private $version;


  public function __construct($plugin_name, $version){
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  public function enqueue_styles($hook) {

    global $menu_page;

    if(is_admin()){
      if($hook == $menu_page||get_current_screen()->id==="season-results_page_mr_tables_breedSettings"){  //if Breed Settings page, enqueue admin and popover css
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name."popover", plugin_dir_url( __FILE__ ) . 'css/popoverStyle.css', array(), $this->version, 'all' );
      }

      if(get_current_screen()->post_type==="event"){  //if editing an Event enqueue show css
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/show.css', array(), $this->version, 'all' );

      }

    }
  }



  public function enqueue_scripts() {



    //-----------datePicker-----------------------
    wp_enqueue_script( 'jquery-ui-datepicker' );

    wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'jquery-ui' );

    wp_enqueue_script('datePicker',plugin_dir_url( __FILE__ ) . 'js/datePicker.js',array('jquery'),$this->plugin_name, true);
    //--------------------------------------------

    //------------Colour Picker---------------------------
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'colourPicker', plugin_dir_url(__FILE__ ).'js/colorPicker.js', array( 'wp-color-picker' ), $this->plugin_name, true );
    //----------------------------------------------------


    //------------Create Table----------------------------
    wp_enqueue_script('tableCreate',plugin_dir_url( __FILE__ ) . 'js/tableCreate.js',array('jquery'),$this->plugin_name, true);

    $title_nonce = wp_create_nonce('tableCreate');
    wp_localize_script('tableCreate','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,

    ));
    //--------------------------------------------

    //------------------updateTable----------------
    wp_enqueue_script('updateTable',plugin_dir_url( __FILE__ ) . 'js/updateTable.js',array('jquery'),$this->plugin_name, true);

    $title_nonce = wp_create_nonce('updateTable');
    wp_localize_script('updateTable','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,

    ));

    //---------------------------------------------

    //------------------deleteTable----------------
    wp_enqueue_script('deleteTable',plugin_dir_url( __FILE__ ) . 'js/deleteTable.js',array('jquery'),$this->plugin_name, true);

    $title_nonce = wp_create_nonce('deleteTable');
    wp_localize_script('deleteTable','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,

    ));
    //---------------------------------------------

    //---------------------breedPopover----------------------------
    wp_enqueue_script('breedPopover',plugin_dir_url( __FILE__ ) . 'js/breedPopover.js',array('jquery'),$this->plugin_name, true);
    //--------------------------------------------------------------+
    //---------------------updateBreed----------------------------
    wp_enqueue_script('updateBreed',plugin_dir_url( __FILE__ ) . 'js/updateBreed.js',array('jquery'),$this->plugin_name, true);

    $title_nonce = wp_create_nonce('updateBreed');
    wp_localize_script('updateBreed','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,
    ));

    //--------------------------------------------------------------

    //------------------deleteBreed----------------
    $title_nonce = wp_create_nonce('deleteBreed');
    wp_localize_script('deleteTable','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,

    ));
    //---------------------------------------------

    //-------------------addBreed------------------
    wp_enqueue_script('addBreed',plugin_dir_url( __FILE__ ) . 'js/addBreed.js',array('jquery'),$this->plugin_name, true);

    $title_nonce = wp_create_nonce('addBreed');
    wp_localize_script('addBreed','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,
    ));
    //---------------------------------------------

    //--------------------manageIcons-----------------------------
    wp_enqueue_script('manageIcons',plugin_dir_url( __FILE__ ) . 'js/manageIcons.js',array('jquery'),$this->plugin_name, true);

    $title_nonce = wp_create_nonce('breedAdd');
    wp_localize_script('deleteIcon','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,
    ));

    $title_nonce = wp_create_nonce('uploadFiles');
    wp_localize_script('uploadFiles','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,
    ));
    //---------------------------------------------

    wp_enqueue_script('toggleTable',plugin_dir_url( __FILE__ ) . 'js/toggleTable.js',array('jquery'),$this->plugin_name, true);

    wp_enqueue_script('addRow',plugin_dir_url( __FILE__ ) . 'js/addRow.js',array('jquery'),$this->plugin_name, true);

    wp_enqueue_script('addBreedRow',plugin_dir_url( __FILE__ ) . 'js/addBreedRow.js',array('jquery'),$this->plugin_name, true);

    wp_enqueue_script('changeAge',plugin_dir_url( __FILE__ ) . 'js/changeAge.js',array('jquery'),$this->plugin_name, true);

    wp_enqueue_script('showPSelect',plugin_dir_url( __FILE__ ) . 'js/showPSelect.js',array('jquery'),$this->plugin_name, true);

    wp_enqueue_script('addShortcode',plugin_dir_url( __FILE__ ) . 'js/addShortcode.js',array('jquery'),$this->plugin_name, true);

    wp_enqueue_script('popover',plugin_dir_url( __FILE__ ) . 'js/bootstrap-popover.js',array('jquery'),$this->plugin_name, true);

    //-----------------------checkUpdate-----------------------
    wp_enqueue_script('checkUpdate',plugin_dir_url( __FILE__ ) . 'js/checkUpdate.js',array('jquery'),$this->plugin_name, true);

    $title_nonce = wp_create_nonce('checkUpdate');
    wp_localize_script('checkUpdate','my_ajax_obj',array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => $title_nonce,

    ));
    //------------------------------------------------------------


  }



  //ajax call functions

  public function deleteIcon(){
    require_once plugin_dir_path(__FILE__) . 'partials/deleteIcon.php';
  }

  public function uploadFiles(){
    require_once plugin_dir_path(__FILE__) . 'partials/uploadFiles.php';
  }

  public function getUploads(){
    require_once plugin_dir_path(__FILE__) . 'partials/getUploads.php';
  }

  public function addBreed(){
    require_once plugin_dir_path(__FILE__) . 'partials/addBreed.php';
  }

  public function updateBreed(){
    require_once plugin_dir_path(__FILE__) . 'partials/updateBreed.php';
  }

  public function deleteBreed(){
    require_once plugin_dir_path(__FILE__) . 'partials/deleteBreed.php';
  }

  public function tableCreate(){
    require_once plugin_dir_path(__FILE__) . 'partials/micerule-tableCreate.php';
  }

  public function updateTable(){
    require_once plugin_dir_path(__FILE__) . 'partials/micerule-updateTable.php';
  }

  public function deleteTable(){
    require_once plugin_dir_path(__FILE__) . 'partials/micerule-deleteTable.php';
  }

  //-------------------------end ajax call functions--------------------------

  //Add Season Results Menu
  public function mr_tables_menu(){

    global $menu_page;

    $menu_page= add_menu_page(
      'Micerule Tables',
      'Season Results',
      'manage_options',
      'mr_tables',
      'mr_tables_menu_html',
      'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path fill="gray" d="M522 883q-74-162-74-371h-256v96q0 78 94.5 162t235.5 113zm1078-275v-96h-256q0 209-74 371 141-29 235.5-113t94.5-162zm128-128v128q0 71-41.5 143t-112 130-173 97.5-215.5 44.5q-42 54-95 95-38 34-52.5 72.5t-14.5 89.5q0 54 30.5 91t97.5 37q75 0 133.5 45.5t58.5 114.5v64q0 14-9 23t-23 9h-832q-14 0-23-9t-9-23v-64q0-69 58.5-114.5t133.5-45.5q67 0 97.5-37t30.5-91q0-51-14.5-89.5t-52.5-72.5q-53-41-95-95-113-5-215.5-44.5t-173-97.5-112-130-41.5-143v-128q0-40 28-68t68-28h288v-96q0-66 47-113t113-47h576q66 0 113 47t47 113v96h288q40 0 68 28t28 68z"/></svg>'),

    );


  }

  //Add sub-menu for Icons and table with Fills
  public function mr_tables_sub_menu(){
    add_submenu_page(
      'mr_tables',
      'Breed Settings',
      'Breed Settings',
      'manage_options',
      'mr_tables_breedSettings',
      'mr_tables_sub_menu_icons_html',);
    }

    //Add Meta Box in Events Manager Post
    public function micerule_add_meta_box(){
      add_meta_box('em-event-micerule-table',
      'Judges and Show Results',
      'micerule_meta_box_html_callback',
      EM_POST_TYPE_EVENT,
      'normal',
      'high');

      add_meta_box('em-event-micerule-table-deadline',
      'Deadline Select',
      'micerule_deadline_meta_box_html_callback',
      EM_POST_TYPE_EVENT,
      'normal',
      'high');
    }


    //Add Breeds Meta Box in Events Manager Post
    public function micerule_add_breeds_meta_box(){
      add_meta_box('em-event-micerule-table-breeds',
        'Breeds',
        'micerule_event_breed_meta_box_html_callback',
        EM_POST_TYPE_LOCATION,
        'normal',
        'high',);
    }

    function micerule_save_metabox_data($post_id){
      global $post;

      if(isset($post->ID)){
        //update_post_meta($post->ID, 'micerule_data_settings', $_POST['micerule_table_data']);
        /*
        if(isset($_POST['event_end_date'])) 
          update_post_meta( $post->ID, 'micerule_data_time', $_POST['event_end_date']);
        if(isset($_POST['scCheck']))
          update_post_meta( $post->ID, 'micerule_data_scCheck', $_POST['scCheck']);
        if(isset($_POST['micerule_table_data_deadline']))
          update_post_meta($post->ID, 'micerule_data_event_deadline', $_POST['micerule_table_data_deadline']);
        if(isset($_POST['micerule_breeds_table_data']))
          update_post_meta($post->ID, 'micerule_data_breeds', $_POST['micerule_breeds_table_data']);
        if(isset($_POST['micerule_table_location_secretaries']))
          update_post_meta($post->ID, 'micerule_data_location_secretaries', $_POST['micerule_table_location_secretaries']);
          */

          ResultTable::saveTableData($post->ID, $_POST['micerule_table_data']);
          EventJudgesHelper::saveEventJudges($post->ID, $_POST['judge_data']);
      }
      
    }
  }

  require_once plugin_dir_path(__FILE__) . 'partials/micerule-adminClass-extras.php';


  //Order metaboxes to display same regardless of user
  add_filter( 'get_user_option_meta-box-order_event', 'wpse25793_one_column_for_all' );
  function wpse25793_one_column_for_all( $order )
  {
    return array(
      'normal'   => join( ",", array(
        'em-event-where',
        'em-event-when',
        'acf-group_5c63295147efb',
        'em-event-micerule-table-deadline',
        'em-event-micerule-table',
        'micerule_tables',
        'submitdiv',
      ) ),
      'side'     => '',
      'advanced' => '',
    );
  }


  ?>
