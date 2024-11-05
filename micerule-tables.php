<?php

/**
* The plugin bootstrap file
*
* This file is read by WordPress to generate the plugin information in the plugin
* admin area. This file also includes all of the dependencies used by the plugin,
* registers the activation and deactivation functions, and defines a function
* that starts the plugin.
*
*
* @wordpress-plugin
* Plugin Name:       Micerule Tables
* Version:           1.0
* Author:            Sebastian Regitz
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
function activate_micerule_tables(){
	require_once plugin_dir_path(__FILE__).'includes/class-micerule-tables-activator.php';
	Micerule_Tables_Activator::activate();
}
register_activation_hook(__FILE__,'activate_micerule_tables');

require plugin_dir_path( __FILE__ ) . 'includes/class-micerule-tables.php';


function run_micerule_tables() {

	$plugin = new Micerule_Tables();
	$plugin->run();

}
run_micerule_tables();
