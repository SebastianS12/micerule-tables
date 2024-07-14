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

		$event_deadline_table_name = $wpdb->prefix."micerule_event_deadline";
		$sql_create_event_deadline_table = "CREATE TABLE IF NOT EXISTS ".$event_deadline_table_name. " (
			event_post_id bigint(20) unsigned NOT NULL,
			event_deadline int,
			PRIMARY KEY  (event_post_id)
			) $charset_collate; ";
		dbDelta($sql_create_event_deadline_table);

		$show_classes_table_name = $wpdb->prefix."micerule_show_classes";
		$sql_create_show_classes_table = "CREATE TABLE IF NOT EXISTS ".$show_classes_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			location_id bigint(20) unsigned NOT NULL,
			class_name varchar(30) NOT NULL,
			section text,
			section_position int,
			PRIMARY KEY  (id)
			) $charset_collate; ";
		dbDelta($sql_create_show_classes_table);

		$show_classes_indices_table_name = $wpdb->prefix."micerule_show_classes_indices";
		$sql_create_show_classes_indices_table = "CREATE TABLE IF NOT EXISTS ".$show_classes_indices_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			class_id bigint(20) unsigned NOT NULL,
			age varchar(10) NOT NULL,
			class_index int NOT NULL,
			PRIMARY KEY  (id),
			CONSTRAINT fk_class_id_class_index
				FOREIGN KEY (class_id) 
				REFERENCES ".$show_classes_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_classes_indices_table);

		$show_challenges_indices_table_name = $wpdb->prefix."micerule_show_challenges_indices";
		$sql_create_show_challenges_indices_table = "CREATE TABLE IF NOT EXISTS ".$show_challenges_indices_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			location_id bigint(20) unsigned NOT NULL,
			section text NOT NULL,
			challenge_name varchar(30) NOT NULL,
			age varchar(10) NOT NULL,
			challenge_index int NOT NULL,
			PRIMARY KEY  (id)
			) $charset_collate; ";
		dbDelta($sql_create_show_challenges_indices_table);

		$show_user_registrations_table_name = $wpdb->prefix."micerule_show_user_registrations";
		$sql_create_show_user_registrations_table = "CREATE TABLE IF NOT EXISTS ".$show_user_registrations_table_name. " (
			class_registration_id bigint(20) unsigned NOT NULL auto_increment,
			event_post_id bigint(20) unsigned NOT NULL,
			user_name varchar(30) NOT NULL,
			class_id bigint(20) unsigned NOT NULL,
			age varchar(10) NOT NULL,
			PRIMARY KEY  (class_registration_id),
			CONSTRAINT fk_class_id_registrations
				FOREIGN KEY (class_id)
				REFERENCES ".$show_classes_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_user_registrations_table);

		$show_user_registrations_order_table_name = $wpdb->prefix."micerule_show_user_registrations_order";
		$sql_create_show_user_registrations_order_table = "CREATE TABLE IF NOT EXISTS ".$show_user_registrations_order_table_name. " (
			class_registration_id bigint(20) unsigned NOT NULL,
			registration_order int NOT NULL,
			PRIMARY KEY  (class_registration_id, registration_order),
			CONSTRAINT fk_registration_id_registrations_order
				FOREIGN KEY (class_registration_id)
				REFERENCES ".$show_user_registrations_table_name."(class_registration_id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_user_registrations_order_table);

		$show_user_junior_registrations_table_name = $wpdb->prefix."micerule_show_user_junior_registrations";
		$sql_create_show_user_junior_registrations_table = "CREATE TABLE IF NOT EXISTS ".$show_user_junior_registrations_table_name. " (
			class_registration_id bigint(20) unsigned NOT NULL,
			registration_order int NOT NULL,
			PRIMARY KEY  (class_registration_id, registration_order),
			CONSTRAINT fk_registration_id_registrations_order_junior
				FOREIGN KEY (class_registration_id, registration_order)
				REFERENCES ".$show_user_registrations_order_table_name."(class_registration_id, registration_order)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_user_junior_registrations_table);

		$show_entries_table_name = $wpdb->prefix."micerule_show_entries";
		$sql_create_show_entries_table = "CREATE TABLE IF NOT EXISTS ".$show_entries_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			class_registration_id bigint(20) unsigned NOT NULL,
			registration_order int NOT NULL,
			pen_number int NOT NULL,
			variety_name text,
			absent bool DEFAULT false,
			added bool DEFAULT false,
			moved bool DEFAULT false,
			PRIMARY KEY  (id),
			CONSTRAINT fk_registration_order_pen_number
				FOREIGN KEY (class_registration_id, registration_order)
				REFERENCES ".$show_user_registrations_order_table_name."(class_registration_id, registration_order)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_entries_table);

		$show_classes_next_pen_numbers_table_name = $wpdb->prefix."micerule_show_classes_next_pen_numbers";
		$sql_create_show_classes_next_pen_numbers_table = "CREATE TABLE IF NOT EXISTS ".$show_classes_next_pen_numbers_table_name. " (
			class_index_id bigint(20) unsigned NOT NULL,
			next_pen_number int NOT NULL,
			PRIMARY KEY  (class_index_id),
			CONSTRAINT fk_class_index_id_next_pen_numbers
				FOREIGN KEY (class_index_id)
				REFERENCES ".$show_classes_indices_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_classes_next_pen_numbers_table);

		$show_class_placements_table_name = $wpdb->prefix."micerule_show_class_placements";
		$sql_create_show_class_placements_table = "CREATE TABLE IF NOT EXISTS ".$show_class_placements_table_name. " (
			class_placement_id bigint(20) unsigned NOT NULL auto_increment,
			entry_id bigint(20) unsigned NOT NULL,
			class_index_id bigint(20) unsigned NOT NULL,
			placement int(2) NOT NULL,
			printed bool DEFAULT False,
			PRIMARY KEY  (class_placement_id),
			CONSTRAINT fk_entry_id_placement
				FOREIGN KEY (entry_id)
				REFERENCES ".$show_entries_table_name."(id)
				ON DELETE CASCADE,
			CONSTRAINT fk_class_index_placement
				FOREIGN KEY (class_index_id)
				REFERENCES ".$show_classes_indices_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_class_placements_table);

		$show_junior_placements_table_name = $wpdb->prefix."micerule_show_junior_placements";
		$sql_create_show_junior_placements_table = "CREATE TABLE IF NOT EXISTS ".$show_junior_placements_table_name. " (
			class_placement_id bigint(20) unsigned NOT NULL auto_increment,
			entry_id bigint(20) unsigned NOT NULL,
			class_index_id bigint(20) unsigned NOT NULL,
			placement int(2) NOT NULL,
			printed bool DEFAULT False,
			PRIMARY KEY  (class_placement_id),
			CONSTRAINT fk_entry_id_junior_eplacement
				FOREIGN KEY (entry_id)
				REFERENCES ".$show_entries_table_name."(id)
				ON DELETE CASCADE,
			CONSTRAINT fk_class_index_junior_placement
				FOREIGN KEY (class_index_id)
				REFERENCES ".$show_classes_indices_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_junior_placements_table);

		$show_section_placements_table_name = $wpdb->prefix."micerule_show_section_placements";
		$sql_create_show_section_placements_table = "CREATE TABLE IF NOT EXISTS ".$show_section_placements_table_name. " (
			section_placement_id bigint(20) unsigned NOT NULL auto_increment,
			entry_id bigint(20) unsigned NOT NULL,
			class_index_id bigint(20) unsigned NOT NULL,
			placement int(2) NOT NULL,
			printed bool DEFAULT False,
			award text DEFAULT NULL,
			PRIMARY KEY  (section_placement_id),
			CONSTRAINT fk_entry_id_section_placement
				FOREIGN KEY (entry_id)
				REFERENCES ".$show_entries_table_name."(id)
				ON DELETE CASCADE,
			CONSTRAINT fk_challenge_index_section_placement
				FOREIGN KEY (class_index_id)
				REFERENCES ".$show_challenges_indices_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_section_placements_table);

		$show_grand_challenge_placements_table_name = $wpdb->prefix."micerule_show_grand_challenge_placements";
		$sql_create_show_grand_challenge_placements_table = "CREATE TABLE IF NOT EXISTS ".$show_grand_challenge_placements_table_name. " (
			grand_challenge_placement_id bigint(20) unsigned NOT NULL auto_increment,
			entry_id bigint(20) unsigned NOT NULL,
			class_index_id bigint(20) unsigned NOT NULL,
			placement int(2) NOT NULL,
			printed bool DEFAULT False,
			award text DEFAULT NULL,
			PRIMARY KEY  (grand_challenge_placement_id),
			CONSTRAINT fk_entry_id_grand_challenge_placement
				FOREIGN KEY (entry_id)
				REFERENCES ".$show_entries_table_name."(id)
				ON DELETE CASCADE,
			CONSTRAINT fk_challenge_index_grand_challenge_placement
				FOREIGN KEY (class_index_id)
				REFERENCES ".$show_challenges_indices_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_grand_challenge_placements_table);

		//TODO: One table for placements? -> id reference to awards table 
		/*
		$show_awards_table_name = $wpdb->prefix."micerule_show_awards";
		$sql_create_show_awards_table = "CREATE TABLE IF NOT EXISTS ".$show_awards_table_name. " (
			award_id bigint(20) unsigned NOT NULL,
			award text NOT NULL,
			PRIMARY KEY  (award_id),
		dbDelta($sql_create_show_awards_table);*/

		$show_judges_general_comments_table_name = $wpdb->prefix."micerule_show_judges_general_comments";
		$sql_create_show_judges_general_comments_table = "CREATE TABLE IF NOT EXISTS ".$show_judges_general_comments_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			event_post_id bigint(20) unsigned NOT NULL,
			judge_no int unsigned NOT NULL,
			comment text,
			PRIMARY KEY  (id),
			CONSTRAINT fk_event_post_id_judge_no_general_comment
				FOREIGN KEY (event_post_id, judge_no)
				REFERENCES ".$event_judges_table_name."(event_post_id, judge_no)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_judges_general_comments_table);

		$show_judges_class_comments_table_name = $wpdb->prefix."micerule_show_judges_class_comments";
		$sql_create_show_judges_class_comments_table = "CREATE TABLE IF NOT EXISTS ".$show_judges_class_comments_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			class_index_id bigint(20) unsigned NOT NULL,
			event_post_id bigint(20) unsigned NOT NULL,
			judge_no int unsigned NOT NULL,
			comment text,
			PRIMARY KEY  (id),
			CONSTRAINT fk_event_post_id_judge_no_class_comment
				FOREIGN KEY (event_post_id, judge_no)
				REFERENCES ".$event_judges_table_name."(event_post_id, judge_no)
				ON DELETE CASCADE,
			CONSTRAINT fk_class_index_id_class_comment
				FOREIGN KEY (class_index_id)
				REFERENCES ".$show_classes_indices_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_judges_class_comments_table);

		$show_judges_class_reports_table_name = $wpdb->prefix."micerule_show_judges_class_reports";
		$sql_create_show_judges_class_reports_table = "CREATE TABLE IF NOT EXISTS ".$show_judges_class_reports_table_name. " (
			id bigint(20) unsigned NOT NULL auto_increment,
			class_index_id bigint(20) unsigned NOT NULL,
			event_post_id bigint(20) unsigned NOT NULL,
			judge_no int unsigned NOT NULL,
			comment text,
			gender text,
			placement int(2) NOT NULL,
			PRIMARY KEY  (id),
			CONSTRAINT fk_event_post_id_judge_no_class_gender
				FOREIGN KEY (event_post_id, judge_no)
				REFERENCES ".$event_judges_table_name."(event_post_id, judge_no)
				ON DELETE CASCADE,
			CONSTRAINT fk_class_index_id_class_gender
				FOREIGN KEY (class_index_id)
				REFERENCES ".$show_classes_indices_table_name."(id)
				ON DELETE CASCADE
			) $charset_collate; ";
		dbDelta($sql_create_show_judges_class_reports_table);

		//file_put_contents(__DIR__.'/my_loggg.txt', ob_get_contents());
	}
}

	//judge db: (judge_no event_id,) name
	//judge_sections db: (judge_no, event_id), section, reference judge db on delete cascade
	//judge_partnership db: (judge_no, event_id,) judge_partner_name reference judge db on delete cascade
