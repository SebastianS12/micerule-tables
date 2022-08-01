<?php

/**
* The public-facing functionality of the plugin.
*/


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

		wp_enqueue_script('stickySidebar', plugin_dir_url( __FILE__ ).'js/stickySidebar.js', array( 'jquery' ), $this->version, false );

	}

	public function lbTables(){
		require_once plugin_dir_path(__FILE__) . 'partials/micerule-lbTables.php';
	}

}
