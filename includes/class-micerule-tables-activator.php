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
		$sql_create_table = "CREATE TABLE IF NOT EXISTS " . $mr_tables_name . " (
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
		$sql_create_breed_table = "CREATE TABLE IF NOT EXISTS ".$breed_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			name text,
			colour text,
			css_class text,
			section text,
			icon_url text,
			PRIMARY KEY  (id)
			) $charset_collate; ";
			dbDelta($sql_create_breed_table);

		//insert No Record Entry
		$wpdb->replace($wpdb->prefix."micerule_breeds", array("name"=>"No Record", "colour"=>"#FFFFFF", "css_class"=>"default", "section"=>"", "icon_url"=>get_home_url()."/wp-content/themes/Divi-child/Assets/spacer.gif"));

		//results table
		$event_results_table_name = $wpdb->prefix."micerule_event_results";
		$sql_create_event_results_table = "CREATE TABLE IF NOT EXISTS ".$event_results_table_name. " (
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
		$sql_create_event_results_optional_table = "CREATE TABLE IF NOT EXISTS ".$event_results_optional_table_name. " (
			event_post_id bigint(20) unsigned NOT NULL,
			class_name varchar(50) NOT NULL,
			fancier_name text,
			variety_name text,
			PRIMARY KEY  (event_post_id, class_name)
			) $charset_collate; ";
			dbDelta($sql_create_event_results_optional_table);

		//event judges table
		$event_judges_table_name = $wpdb->prefix."micerule_event_judges";
		$sql_create_event_judges_table = "CREATE TABLE IF NOT EXISTS ".$event_judges_table_name. " (
			event_post_id bigint(20) unsigned NOT NULL,
			judge_no int unsigned NOT NULL,
			judge_name text,
			PRIMARY KEY  (event_post_id, judge_no)
			) $charset_collate; ";
		dbDelta($sql_create_event_judges_table);
		
		//event judges sections table
		$event_judges_sections_table_name = $wpdb->prefix."micerule_event_judges_sections";
		$sql_create_event_judges_sections_table = "CREATE TABLE IF NOT EXISTS ".$event_judges_sections_table_name. " (
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
		$sql_create_event_judges_partnerships_table = "CREATE TABLE IF NOT EXISTS ".$event_judges_partnerships_table_name. " (
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

		$show_options_table_name = $wpdb->prefix."micerule_show_options";
		$sql_create_show_options_table = "CREATE TABLE IF NOT EXISTS ".$show_options_table_name. " (
			location_id bigint(20) unsigned NOT NULL,
			allow_online_registrations bool NOT NULL,
			registration_fee float NOT NULL,
			pm_first_place float NOT NULL,
			pm_second_place float NOT NULL,
			pm_third_place float NOT NULL,
			allow_unstandardised bool NOT NULL,
			allow_junior bool NOT NULL,
			allow_auction bool NOT NULL,
			PRIMARY KEY  (location_id)
			) $charset_collate; ";
		dbDelta($sql_create_show_options_table);

		$location_secretaries_table_name = $wpdb->prefix."micerule_location_secretaries";
		$sql_create_location_secretaries_table = "CREATE TABLE IF NOT EXISTS ".$location_secretaries_table_name. " (
			location_id bigint(20) unsigned NOT NULL,
			secretary_position int unsigned NOT NULL,
			secretary_name text,
			PRIMARY KEY  (location_id, secretary_position)
			) $charset_collate; ";
		dbDelta($sql_create_location_secretaries_table);

		$show_classes_table_name = $wpdb->prefix."micerule_show_classes";
		$sql_create_show_classes_table = "CREATE TABLE IF NOT EXISTS ".$show_classes_table_name. " (
			location_id bigint(20) unsigned NOT NULL,
			class_name varchar(30) NOT NULL,
			section text,
			section_position int,
			PRIMARY KEY  (location_id, class_name)
			) $charset_collate; ";
		dbDelta($sql_create_show_classes_table);

		$show_classes_indices_table_name = $wpdb->prefix."micerule_show_classes_indices";
		$sql_create_show_classes_indices_table = "CREATE TABLE IF NOT EXISTS ".$show_classes_indices_table_name. " (
			location_id bigint(20) unsigned NOT NULL,
			class_name varchar(30) NOT NULL,
			age varchar(10) NOT NULL,
			class_index int NOT NULL,
			PRIMARY KEY  (location_id, class_name, age),
			CONSTRAINT fk_location_id_class_name_class_index
				FOREIGN KEY (location_id, class_name) 
				REFERENCES ".$show_classes_table_name."(location_id, class_name)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_classes_indices_table);

		$show_challenges_indices_table_name = $wpdb->prefix."micerule_show_challenges_indices";
		$sql_create_show_challenges_indices_table = "CREATE TABLE IF NOT EXISTS ".$show_challenges_indices_table_name. " (
			location_id bigint(20) unsigned NOT NULL,
			challenge_name varchar(30) NOT NULL,
			age varchar(10) NOT NULL,
			challenge_index int NOT NULL,
			PRIMARY KEY  (location_id, challenge_name, age)
			) $charset_collate; ";
		dbDelta($sql_create_show_challenges_indices_table);

		//file_put_contents(__DIR__.'/my_loggg.txt', ob_get_contents());
	}
}

	//judge db: (judge_no event_id,) name
	//judge_sections db: (judge_no, event_id), section, reference judge db on delete cascade
	//judge_partnership db: (judge_no, event_id,) judge_partner_name reference judge db on delete cascade
