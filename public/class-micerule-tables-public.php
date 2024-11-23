<?php

/**
* The public-facing functionality of the plugin.
*/
require_once plugin_dir_path(__FILE__) . 'partials/Enums/Prizes.php';
require_once plugin_dir_path(__FILE__) . 'partials/Enums/Awards.php';
require_once plugin_dir_path(__FILE__) . 'partials/Enums/Tables.php';
require_once plugin_dir_path(__FILE__) . 'partials/Enums/Sections.php';

require_once plugin_dir_path(__FILE__) . 'core/database/Collection.php';
require_once plugin_dir_path(__FILE__) . 'core/database/Model.php';
require_once plugin_dir_path(__FILE__) . 'core/database/IQueryCombine.php';
require_once plugin_dir_path(__FILE__) . 'core/database/IQueryWhere.php';
require_once plugin_dir_path(__FILE__) . 'core/database/QueryJoin.php';
require_once plugin_dir_path(__FILE__) . 'core/database/QueryJoinSub.php';
require_once plugin_dir_path(__FILE__) . 'core/database/QueryWhere.php';
require_once plugin_dir_path(__FILE__) . 'core/database/QueryWhereNull.php';
require_once plugin_dir_path(__FILE__) . 'core/database/QueryWhereNested.php';
require_once plugin_dir_path(__FILE__) . 'core/database/QueryBuilder.php';
require_once plugin_dir_path(__FILE__) . 'core/database/ModelHydrator.php';
require_once plugin_dir_path(__FILE__) . 'core/database/LazyLoader.php';

require_once plugin_dir_path(__FILE__) . 'core/Router/Route.php';
require_once plugin_dir_path(__FILE__) . 'core/Router/Router.php';
require_once plugin_dir_path(__FILE__) . 'core/Router/Logger.php';

require_once plugin_dir_path(__FILE__) . 'partials/Helpers/JudgeFormatter.php';
require_once plugin_dir_path(__FILE__) . 'partials/Helpers/LocationHelper.php';
require_once plugin_dir_path(__FILE__) . 'partials/Helpers/PermissionHelper.php';
require_once plugin_dir_path(__FILE__) . 'partials/Helpers/JuniorHelper.php';
require_once plugin_dir_path(__FILE__) . 'partials/Helpers/FancierNameFormatter.php';
require_once plugin_dir_path(__FILE__) . 'partials/Helpers/EventHelper.php';

include_once plugin_dir_path(__FILE__)."partials/Event/EventProperties.php";
include_once plugin_dir_path(__FILE__)."partials/Event/EventUser.php";
include_once plugin_dir_path(__FILE__)."partials/Event/AdminTabs.php";

require_once plugin_dir_path(__FILE__) . 'partials/Leaderboard/LeaderboardController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Leaderboard/LeaderboardModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Leaderboard/LeaderboardView.php';

require_once plugin_dir_path(__FILE__) . 'partials/Repositories/IRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/EntryRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/ShowClassesRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/UserRegistrationsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/AwardsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/PrizeCardsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/JudgesRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/BreedsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/JudgesReportRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/ChallengeIndexRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/ClassIndexRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/RegistrationCountRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/RegistrationOrderRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/PlacementsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/JuniorRegistrationRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/JudgesSectionsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/GeneralCommentRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/ClassCommentsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/PlacementReportsRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/NextPenNumberRepository.php';
require_once plugin_dir_path(__FILE__) . 'partials/Repositories/ShowOptionsRepository.php';

require_once plugin_dir_path(__FILE__) . 'partials/DAOs/IPrintDAO.php';
require_once plugin_dir_path(__FILE__) . 'partials/DAOs/AwardsDAO.php';
require_once plugin_dir_path(__FILE__) . 'partials/DAOs/IPlacementDAO.php';
require_once plugin_dir_path(__FILE__) . 'partials/DAOs/PlacementDAO.php';

require_once plugin_dir_path(__FILE__) . 'partials/Models/EntryModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/PlacementModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/NextPenNumberModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/PrizeCards.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/PlacementReport.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/ClassComment.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/GeneralComment.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/EntryClassModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/UserRegistrationModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/ClassIndexModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/ShowChallengeModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/RegistrationOrderModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/AwardModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/JuniorRegistrationModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/JudgeModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/JudgeSectionModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/BreedModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/ShowOptions.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/ShowOptionsModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/Models/RowPlacementData.php';

require_once plugin_dir_path(__FILE__) . 'partials/Services/PlacementsService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/EntriesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/ChallengeRowService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/IRowService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/EntryRowService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/OptionalClassRowService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/JuniorRowService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/JudgesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/PrizeCardsService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/BreedsService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/JudgesReportService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/RegistrationTablesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/RegistrationService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/EntryBookService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/PlacementsRowService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/FancierEntriesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/LabelService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/EntrySummaryService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/JudgingSheetsService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/AbsenteesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/EditEntryBookService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/EventDeadlineService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/LocationSecretariesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/ShowOptionsService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/ShowClassesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/IndicesService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/ShowReportPostService.php';
require_once plugin_dir_path(__FILE__) . 'partials/Services/AdminTabsService.php';

require_once plugin_dir_path(__FILE__) . 'partials/Factories/PrizeCardFactory.php';
require_once plugin_dir_path(__FILE__) . 'partials/Factories/PrintDAOFactory.php';
require_once plugin_dir_path(__FILE__) . 'partials/Factories/PlacementDAOFactory.php';
require_once plugin_dir_path(__FILE__) . 'partials/Factories/PlacementModelFactory.php';

require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/EntryBookViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/FancierEntriesViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/LabelViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/EntrySummaryViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/JudgingSheetsViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/AbsenteesViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/PrizeCardsViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/JudgesReportViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/ShowClassesViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/ShowReportPostViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/RegistrationTablesViewModel.php';
require_once plugin_dir_path(__FILE__) . 'partials/ViewModels/AdminTabsViewModel.php';

require_once plugin_dir_path(__FILE__) . 'partials/Controllers/ShowClassesController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/ShowReportPostController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/RegistrationTablesController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/JudgesReportController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/PrizeCardsController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/EntryBookController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/EntrySummaryController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/ShowOptionsController.php';
require_once plugin_dir_path(__FILE__) . 'partials/Controllers/AdminTabsController.php';

require_once plugin_dir_path(__FILE__) . 'partials/Views/FancierEntriesView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/EntrySummaryView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/EntryBookView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/EntryBookPlacementView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/EntryBookRowView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/RegistrationTablesView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/ShowClassesView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/ShowReportPostView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/ChallengeRowView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/LabelView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/JudgingSheetsView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/AbsenteesView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/PrizeCardsView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/JudgesReportView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/ShowOptionsView.php';
require_once plugin_dir_path(__FILE__) . 'partials/Views/AdminTabsView.php';

require_once plugin_dir_path(__FILE__) . 'partials/DataLoaders/AbstractDataLoader.php';
require_once plugin_dir_path(__FILE__) . 'partials/DataLoaders/ChallengeIndexDataLoader.php';
require_once plugin_dir_path(__FILE__) . 'partials/DataLoaders/JudgeDataLoader.php';
require_once plugin_dir_path(__FILE__) . 'partials/DataLoaders/ShowClassDataLoader.php';

require_once plugin_dir_path(__FILE__) . 'partials/DataMappers/JudgeMapper.php';
require_once plugin_dir_path(__FILE__) . 'partials/DataMappers/PlacementsMapper.php';
require_once plugin_dir_path(__FILE__) . 'partials/DataMappers/RegistrationCountMapper.php';


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

		wp_enqueue_script('route',plugin_dir_url( __FILE__ ) . 'js/route.js',array('jquery'),$this->plugin_name, true);

		//---------------------------lbTables------------------
		wp_enqueue_script('lbTables', plugin_dir_url( __FILE__ ).'js/lbTables.js', array( 'jquery' ), $this->version, false );

		$title_nonce = wp_create_nonce('lbTables');
		wp_localize_script('lbTables','my_ajax_obj',array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => $title_nonce,

		));
		//--------------------------------------------

		//---------------------------addBreed------------------
		wp_enqueue_script('addClass', plugin_dir_url( __FILE__ ).'js/addClass.js', array( 'wp-api', 'jquery' ), $this->version, false );

		// wp_localize_script('addClass','my_ajax_obj',array('wp-api'));
		wp_localize_script('addClass','miceruleApi', array(
			'nonce' => wp_create_nonce('wp_rest'),
			'root' => esc_url_raw(rest_url()),
		));
		//--------------------------------------------

		//---------------------------registerClasses------------------
		wp_enqueue_script('registerClasses', plugin_dir_url( __FILE__ ).'js/registerClasses.js', array( 'jquery' ), $this->version, false );

		wp_localize_script('registerClasses','miceruleApi',array(
			'nonce'    => wp_create_nonce('wp_rest'),
		));
		//--------------------------------------------


		//---------------------------moveClass------------------
		wp_enqueue_script('moveClass', plugin_dir_url( __FILE__ ).'js/moveClass.js', array( 'wp-api', 'jquery' ), $this->version, false );

		wp_localize_script('moveClass','miceruleApi',array(
			'nonce' => wp_create_nonce('wp_rest'),
        	'root' => esc_url_raw(rest_url()),
		));
		//--------------------------------------------


		//--------------------editClass-----------------------------
    	wp_enqueue_script('deleteClass',plugin_dir_url( __FILE__ ) . 'js/deleteClass.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('deleteClass','miceruleApi',array(
			'nonce'    => wp_create_nonce('wp_rest'),
		));
    //---------------------------------------------


		//----------------------location settings-----------------
		wp_enqueue_script('locationSettings',plugin_dir_url( __FILE__ ) . 'js/locationSettings.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('locationSettings','miceruleApi',array(
			'nonce'    => wp_create_nonce('wp_rest'),
		));
		//-----------------------------------------------------------


		//--------------------editClass-----------------------------
    wp_enqueue_script('updateRegistrationTables',plugin_dir_url( __FILE__ ) . 'js/updateRegistrationTables.js',array('jquery'),$this->plugin_name, true);

	wp_localize_script('updateRegistrationTables','miceruleApi',array(
		'nonce'    => wp_create_nonce('wp_rest'),
	));
    //---------------------------------------------

		//--------------------editEntrySummary---------------------------------
		wp_enqueue_script('editEntrySummary',plugin_dir_url( __FILE__ ) . 'js/editEntrySummary.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('setAllAbsent','miceruleApi',array(
			'nonce'    => wp_create_nonce('wp-rest'),
		));
		//--------------------------------------------------------------


		//--------------------editEntryBook-----------------------------
		wp_enqueue_script('editEntryBook', plugin_dir_url(__FILE__) . 'js/editEntryBook.js', array('jquery'), $this->plugin_name, true);

		wp_localize_script('editEntryBook','miceruleApi',array(
			'nonce'    => wp_create_nonce('wp-rest'),
		));
    //---------------------------------------------


		//--------------------printPrizeCards---------------------------------
		wp_enqueue_script('printPrizeCards',plugin_dir_url( __FILE__ ) . 'js/printPrizeCards.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('printPrizeCards','miceruleApi', array(
			'nonce'    => wp_create_nonce('wp_rest'),
		));
		//--------------------------------------------------------------

		//--------------------editJudgesReports---------------------------------
		wp_enqueue_script('editJudgesReports',plugin_dir_url( __FILE__ ) . 'js/editJudgesReports.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('editJudgesReports','miceruleApi', array(
			'nonce'    => wp_create_nonce('wp_rest'),
		));
		//--------------------------------------------------------------

		//--------------------createShowPost---------------------------------
		wp_enqueue_script('createShowPost',plugin_dir_url( __FILE__ ) . 'js/createShowPost.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('createShowPost','miceruleApi', array(
			'nonce'    => wp_create_nonce('wp_rest'),
		));
		//--------------------------------------------------------------

		//--------------------setVariety---------------------------------
		wp_enqueue_script('setVariety',plugin_dir_url( __FILE__ ) . 'js/setVariety.js',array('jquery'),$this->plugin_name, true);

		wp_localize_script('setVariety','miceruleApi', array(
			'nonce'    => wp_create_nonce('wp_rest'),
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

	public function printAll(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-printAll.php';
	}

	public function moveToUnprinted(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-moveToUnprinted.php';
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

	public function getSelectOptions(){
		$locationID = LocationHelper::getIDFromEventPostID(url_to_postid( wp_get_referer()));
		EntryBookController::getSelectOptions(new ShowClassesRepository($locationID), new ClassIndexRepository($locationID));
	}

}

//add shortcodes
require_once plugin_dir_path(__FILE__).'partials/sectionTablesFrontend-Shortcode.php';
require_once plugin_dir_path(__FILE__).'partials/registrationDeadline-Shortcode.php';
require_once plugin_dir_path(__FILE__).'partials/classEditLink-Shortcode.php';
require_once plugin_dir_path(__FILE__).'partials/registrationFee-Shortcode.php';
