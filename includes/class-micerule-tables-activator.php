<?php
class Micerule_Tables_Activator {

	/**
	* Short Description. (use period)
	*
	* Long Description.
	*
	* @since    1.0.0
	*/
	public static function activate() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$mr_tables_name = $wpdb->prefix . "micerule_result_tables";
		$sql_create_table = "CREATE TABLE " . $mr_tables_name . " (
			mrtable_id bigint(20) unsigned NOT NULL auto_increment,
			season_results longtext,
			dateFrom int,
			dateTo int,
			seasonTable int,
			PRIMARY KEY  (mrtable_id)
			) $charset_collate; ";
			dbDelta( $sql_create_table );

		//breed table
		$breed_table_name = $wpdb->prefix."micerule_breeds";
		$sql_create_breed_table = "CREATE TABLE ".$breed_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			name text,
			colour text,
			css_class text,
			section text,
			icon_url text,
			PRIMARY KEY  (id)
			) $charset_collate; ";
			dbDelta($sql_create_breed_table);
		}
	}
