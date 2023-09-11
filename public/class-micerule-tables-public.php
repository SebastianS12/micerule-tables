<?php

/**
* The public-facing functionality of the plugin.
*/
include_once plugin_dir_path(__FILE__)."partials/Location/EventClasses.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EventRegistrationData.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EventProperties.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EventUser.php";
include_once plugin_dir_path(__FILE__)."partials/ClassSelectOptions.php";
include_once plugin_dir_path(__FILE__)."partials/StandardClasses.php";
include_once plugin_dir_path(__FILE__)."partials/RegistrationTables.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EventOptionalSettings.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EntryBookData.php";
include_once plugin_dir_path(__FILE__)."partials/Event/AdminTabData.php";
include_once plugin_dir_path(__FILE__)."partials/Event/AdminTabDataFactory.php";
include_once plugin_dir_path(__FILE__)."partials/Event/IAdminTab.php";
include_once plugin_dir_path(__FILE__)."partials/Event/FancierEntries.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EntryBook.php";
include_once plugin_dir_path(__FILE__)."partials/Event/Label.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EntrySummary.php";
include_once plugin_dir_path(__FILE__)."partials/Event/JudgingSheets.php";
include_once plugin_dir_path(__FILE__)."partials/Event/Absentees.php";
include_once plugin_dir_path(__FILE__)."partials/Event/PriceCards.php";
include_once plugin_dir_path(__FILE__)."partials/Event/JudgesReport.php";
include_once plugin_dir_path(__FILE__)."partials/Event/ShowReportPost.php";
include_once plugin_dir_path(__FILE__)."partials/Event/AdminTabs.php";

require_once plugin_dir_path(__FILE__) . 'partials/Leaderboard/LeaderboardController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Leaderboard/LeaderboardModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Leaderboard/LeaderboardView.php';

require_once plugin_dir_path(__FILE__) . 'partials/RegistrationTables/RegistrationTablesController.php';
require_once plugin_dir_path(__FILE__) . 'partials/RegistrationTables/RegistrationTablesModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/RegistrationTables/RegistrationTablesView.php';

require_once plugin_dir_path(__FILE__) . 'partials/FancierEntries/FancierEntriesController.php';
require_once plugin_dir_path(__FILE__) . 'partials/FancierEntries/FancierEntriesView.php';

require_once plugin_dir_path(__FILE__) . 'partials/EntrySummary/EntrySummaryController.php';
require_once plugin_dir_path(__FILE__) . 'partials/EntrySummary/EntrySummaryModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/EntrySummary/EntrySummaryView.php';

require_once plugin_dir_path(__FILE__) . 'partials/EntryBook/EntryBookController.php';
require_once plugin_dir_path(__FILE__) . 'partials/EntryBook/EntryBookModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/EntryBook/EntryBookView.php';

require_once plugin_dir_path(__FILE__) . 'partials/Label/LabelModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Label/LabelView.php';

require_once plugin_dir_path(__FILE__) . 'partials/JudgingSheets/JudgingSheetsModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/JudgingSheets/JudgingSheetsView.php';

require_once plugin_dir_path(__FILE__) . 'partials/Absentees/AbsenteesModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Absentees/AbsenteesView.php';

require_once plugin_dir_path(__FILE__) . 'partials/PrizeCards/PrizeCardsController.php';
require_once plugin_dir_path(__FILE__) . 'partials/PrizeCards/PrizeCardsModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/PrizeCards/PrizeCardsView.php';

require_once plugin_dir_path(__FILE__) . 'partials/Models/ShowEntry.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/ClassPlacements.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/SinglePlacement.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/NextPenNumber.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/PrizeCards.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/UserClassRegistration.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/ChallengeAwards.php';
class Micerule_Tables_Public {

	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $plugin_name       The name of the plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

	}

	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {


		//---------------------------lbTables------------------
		wp_enqueue_script('lbTables', plugin_dir_url( __FILE__ ).'js/lbTables.js', array( 'jquery' ), $this->version, false );

		$title_nonce = wp_create_nonce('lbTables');
		wp_localize_script('lbTables','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,

		));
		//--------------------------------------------

		//---------------------------addBreed------------------
		wp_enqueue_script('addClass', plugin_dir_url( __FILE__ ).'js/addClass.js', array( 'jquery' ), $this->version, false );

		$title_nonce = wp_create_nonce('addClass');
		wp_localize_script('addClass','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,

		));
		//--------------------------------------------

		//---------------------------registerClasses------------------
		wp_enqueue_script('registerClasses', plugin_dir_url( __FILE__ ).'js/registerClasses.js', array( 'jquery' ), $this->version, false );

		$title_nonce = wp_create_nonce('registerClasses');
		wp_localize_script('registerClasses','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,

		));
		//--------------------------------------------

		//---------------------------getClassSelectOptions------------------

		$title_nonce = wp_create_nonce('getClassSelectOptions');
		wp_localize_script('getClassSelectOptions','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,

		));
		//--------------------------------------------

		//---------------------------moveClass------------------
		wp_enqueue_script('moveClass', plugin_dir_url( __FILE__ ).'js/moveClass.js', array( 'jquery' ), $this->version, false );

		$title_nonce = wp_create_nonce('moveClass');
		wp_localize_script('moveClass','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,

		));
		//--------------------------------------------


		//--------------------editClass-----------------------------
    wp_enqueue_script('deleteClass',plugin_dir_url( __FILE__ ) . 'js/deleteClass.js',array('jquery'),$this->plugin_name, true);

		$title_nonce = wp_create_nonce('deleteClass');
		wp_localize_script('deleteClass','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
    //---------------------------------------------


		//----------------------location settings-----------------
		wp_enqueue_script('locationSettings',plugin_dir_url( __FILE__ ) . 'js/locationSettings.js',array('jquery'),$this->plugin_name, true);

		$title_nonce = wp_create_nonce('allowOptionalClasses');
		wp_localize_script('allowOptionalClasses','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));

		$title_nonce = wp_create_nonce('enableOnlineRegistrations');
		wp_localize_script('enableOnlineRegistrations','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));

		$title_nonce = wp_create_nonce('eventOptionalSettings');
		wp_localize_script('eventOptionalSettings','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
		//-----------------------------------------------------------


		//--------------------editClass-----------------------------
    wp_enqueue_script('updateRegistrationTables',plugin_dir_url( __FILE__ ) . 'js/updateRegistrationTables.js',array('jquery'),$this->plugin_name, true);

		$title_nonce = wp_create_nonce('updateRegistrationTables');
		wp_localize_script('updateRegistrationTables','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
    //---------------------------------------------


		//--------------------editLabel---------------------------------
		wp_enqueue_script('editLabel',plugin_dir_url( __FILE__ ) . 'js/editLabel.js',array('jquery'),$this->plugin_name, true);
		//--------------------------------------------------------------


		//--------------------editEntrySummary---------------------------------
		wp_enqueue_script('editEntrySummary',plugin_dir_url( __FILE__ ) . 'js/editEntrySummary.js',array('jquery'),$this->plugin_name, true);

		$title_nonce = wp_create_nonce('setAllAbsent');
		wp_localize_script('setAllAbsent','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
		//--------------------------------------------------------------


		//--------------------editEntryBook-----------------------------
		wp_enqueue_script('editEntryBook', plugin_dir_url(__FILE__) . 'js/editEntryBook.js', array('jquery'), $this->plugin_name, true);

		$title_nonce = wp_create_nonce('moveEntry');
		wp_localize_script('moveEntry','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));

		$title_nonce = wp_create_nonce('addEntry');
		wp_localize_script('addEntry','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));

		$title_nonce = wp_create_nonce('deleteEntry');
		wp_localize_script('deleteEntry','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));

		$title_nonce = wp_create_nonce('editPlacement');
		wp_localize_script('editPlacement','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));

		$title_nonce = wp_create_nonce('editBIS');
		wp_localize_script('editBIS','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));

		$title_nonce = wp_create_nonce('editAbsent');
		wp_localize_script('editAbsent','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
    //---------------------------------------------


		//--------------------printPrizeCards---------------------------------
		wp_enqueue_script('printPrizeCards',plugin_dir_url( __FILE__ ) . 'js/printPrizeCards.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('setPrinted','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
		//--------------------------------------------------------------

		//--------------------editJudgesReports---------------------------------
		wp_enqueue_script('editJudgesReports',plugin_dir_url( __FILE__ ) . 'js/editJudgesReports.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('submitReport','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
		//--------------------------------------------------------------

		//--------------------createShowPost---------------------------------
		wp_enqueue_script('createShowPost',plugin_dir_url( __FILE__ ) . 'js/createShowPost.js',array('jquery'),$this->plugin_name, true);

		$title_nonce = wp_create_nonce('createShowPost');
		wp_localize_script('createShowPost','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
		//--------------------------------------------------------------

		//--------------------setVariety---------------------------------
		wp_enqueue_script('setVariety',plugin_dir_url( __FILE__ ) . 'js/setVariety.js',array('jquery'),$this->plugin_name, true);

		$title_nonce = wp_create_nonce('setCustomClassVariety');
		wp_localize_script('setCustomClassVariety','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
		//--------------------------------------------------------------

		//--------------------updateAdminTabs---------------------------------
		wp_enqueue_script('updateAdminTabs',plugin_dir_url( __FILE__ ) . 'js/updateAdminTabs.js',array('jquery'),$this->plugin_name, true);

		$title_nonce = wp_create_nonce('updateAdminTabs');
		wp_localize_script('updateAdminTabs','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,
		));
		//--------------------------------------------------------------

		wp_enqueue_script('selectTabs',plugin_dir_url( __FILE__ ) . 'js/selectTabs.js',array('jquery'),$this->plugin_name, true);
		//wp_enqueue_script('stickySidebar', plugin_dir_url( __FILE__ ).'js/stickySidebar.js', array( 'jquery' ), $this->version, false );
	}

	public function lbTables(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-lbTables.php';
	}

	public function addClass(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-addClass.php';
	}

	public function registerClasses(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-registerClasses.php';
	}

	public function getClassSelectOptions(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-getClassSelectOptions.php';
	}

	public function moveClass(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-moveClass.php';
	}

	public function deleteClass(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-deleteClass.php';
	}

	public function eventOptionalSettings(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-eventOptionalSettings.php';
	}

	public function updateRegistrationTables(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-updateRegistrationTables.php';
	}

	public function moveEntry(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-moveEntry.php';
	}

	public function addEntry(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-addEntry.php';
	}

	public function deleteEntry(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-deleteEntry.php';
	}

	public function editPlacement(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-editPlacement.php';
	}

	public function editBIS(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-editBIS.php';
	}

	public function editAbsent(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-editAbsent.php';
	}

	public function setAllAbsent(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-setAllAbsent.php';
	}

	public function setCustomClassVariety(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-setCustomClassVariety.php';
	}

	public function setPrinted(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-setPrinted.php';
	}

	public function submitReport(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-submitReport.php';
	}

	public function createShowPost(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-createShowPost.php';
	}

	public function getAdminTabsHtml(){
		$url     = wp_get_referer();
		$event_id = url_to_postid( $url );

		wp_send_json(AdminTabs::getAdminTabsHtml($event_id));
	}

}

//add shortcodes
require_once plugin_dir_path(__FILE__).'partials/sectionTablesFrontend-Shortcode.php';
require_once plugin_dir_path(__FILE__).'partials/registrationDeadline-Shortcode.php';
require_once plugin_dir_path(__FILE__).'partials/classEditLink-Shortcode.php';
require_once plugin_dir_path(__FILE__).'partials/registrationFee-Shortcode.php';
