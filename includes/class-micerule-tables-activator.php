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

		//results table
		$event_results_table_name = $wpdb->prefix."micerule_event_results";
		$sql_create_event_results_table = "CREATE TABLE ".$event_results_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			event_post_id bigint(20) unsigned,
			award text,
			section text,
			fancier_name text,
			variety_name text,
			age text,
			points int(2),
			PRIMARY KEY  (id)
			) $charset_collate; ";
			dbDelta($sql_create_event_results_table);

		//optional results
		$event_results_optional_table_name = $wpdb->prefix."micerule_event_results_optional";
		$sql_create_event_results_optional_table = "CREATE TABLE ".$event_results_optional_table_name. " (
			event_post_id bigint(20) unsigned NOT NULL,
			class_name varchar(50) NOT NULL,
			fancier_name text,
			variety_name text,
			PRIMARY KEY  (event_post_id, class_name)
			) $charset_collate; ";
			dbDelta($sql_create_event_results_optional_table);

		//event judges table
		$event_judges_table_name = $wpdb->prefix."micerule_event_judges";
		$sql_create_event_judges_table = "CREATE TABLE ".$event_judges_table_name. " (
			event_post_id bigint(20) unsigned NOT NULL,
			judge_no int unsigned NOT NULL,
			judge_name text,
			PRIMARY KEY  (event_post_id, judge_no)
			) $charset_collate; ";
			dbDelta($sql_create_event_judges_table);

		//event judges sections table
		$event_judges_sections_table_name = $wpdb->prefix."micerule_event_judges_sections";
		$sql_create_event_judges_sections_table = "CREATE TABLE ".$event_judges_sections_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			event_post_id bigint(20) unsigned NOT NULL,
			judge_no int unsigned NOT NULL,
			section text,
			PRIMARY KEY  (id),
			CONSTRAINT fk_event_id_judge_no_sections
				FOREIGN KEY (event_post_id, judge_no) 
				REFERENCES ".$event_judges_table_name."(event_post_id, judge_no)
				ON DELETE CASCADE
			) $charset_collate; ";
			dbDelta($sql_create_event_judges_sections_table);

		//event judges partnerships table
		$event_judges_partnerships_table_name = $wpdb->prefix."micerule_event_judges_partnerships";
		$sql_create_event_judges_partnerships_table = "CREATE TABLE ".$event_judges_partnerships_table_name. " (
			event_post_id bigint(20) unsigned NOT NULL,
			judge_no int unsigned NOT NULL,
			partner_name text,
			PRIMARY KEY  (event_post_id, judge_no),
			CONSTRAINT fk_event_id_judge_no_partnerships
				FOREIGN KEY (event_post_id, judge_no) 
				REFERENCES ".$event_judges_table_name."(event_post_id, judge_no)
				ON DELETE CASCADE
			) $charset_collate; ";
			dbDelta($sql_create_event_judges_partnerships_table);
		}
	}

	//judge db: (judge_no event_id,) name
	//judge_sections db: (judge_no, event_id), section, reference judge db on delete cascade
	//judge_partnership db: (judge_no, event_id,) judge_partner_name reference judge db on delete cascade
