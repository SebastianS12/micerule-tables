<?php

/*
*	creates html for the leaderboard page,
*
*	creates divs for the lb-tables.js to append to
*/

function leaderBoard_results(){

	global $wpdb;

	//Get Season Tables
	$seasons= $wpdb->get_results("SELECT dateFrom,dateTo FROM ".$wpdb->prefix."micerule_result_tables WHERE seasonTable =1 ORDER BY dateTo DESC");

	$dateFromCurrent= $seasons[0]->dateTo +1;

	//store dates from season tables in array
	for($i=0;$i<count($seasons);$i++){
		$dateTo[$i] = date('Y',$seasons[$i]->dateTo);
		$dateFrom[$i] = date('Y',$seasons[$i]->dateFrom);
	}

	//Get Event postmeta + ids and event dates + ids
	$eventDates = (array) $wpdb->get_results('SELECT post_id, meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_time" ORDER BY post_id DESC'); //AND DATE(meta_value) <= '.date("Y-m-d H:i:s", $dateFromCurrent).' ORDER BY meta_value ASC');
 	$events = (array) $wpdb->get_results('SELECT post_id, meta_value FROM '.$wpdb->postmeta .' WHERE meta_key = "micerule_data_settings" ORDER BY post_id DESC');

	//Get the index for the most recent event after the youngest season by checking if event is finished and if the event date is after current season date
	$indexCounter = 0;
	for($i = 0; $i < count($events); $i++){
	if(unserialize($events[$i]->meta_value)["name"][0] != "" && strtotime($eventDates[$i]->meta_value) > $dateFromCurrent){
			$indexCounter = $i;
		}
	}

	$html ="<p>Show Season:<p>";
	$html .='<select id="seasonSelect">';
	if(strtotime($eventDates[$indexCounter]->meta_value) > $dateFromCurrent){
		$html .='<option value="'.$dateFromCurrent.'"   selected="selected">Current Season</option>';
	}
	for($i=0;$i<count($dateTo);$i++){//display season dates as option
		$html .='<option value="'.$seasons[$i]->dateFrom.'/'.$seasons[$i]->dateTo.'">'.$dateFrom[$i].' / '.$dateTo[$i].'</option>';
	}
	$html .= "</select>";
	$html .='<div class="lbTables">';
	$html .='</div>';

	return $html;

}

function leaderBoard_results_topTwenty(){

	$html  ='<div id="lbTopTwenty" class="lbTables" style="width:100%;">';
	$html .='</div>';
	return $html;

}

function leaderBoard_results_bis(){

	$html  ='<div id="lbBIS" class="lbTables" style="width:100%;">';
	$html .='</div>';
	return $html;

}

function leaderBoard_results_chart(){
	$html  ='<div id="chart" class="lbTables" style="width:100%;">';
	$html .='</div>';
	return $html;
}

function leaderBoard_results_varieties(){
	$html  ='<div id="lbVarieties" class="lbTables" style="width:100%;">';
	$html .='</div>';
	return $html;
}

function leaderBoard_results_section_leaders(){
	$html = '<div id="lbSectionLeaders" class="lbTables" style="width:100%;">';
	$html .= '</div';
	return $html;
}

add_shortcode('leaderBoard_results','leaderBoard_results');
add_shortcode('leaderBoard_results_topTwenty','leaderBoard_results_topTwenty');
add_shortcode('leaderBoard_results_bis','leaderBoard_results_bis');
add_shortcode('leaderBoard_results_chart','leaderBoard_results_chart');
add_shortcode('leaderBoard_results_varieties','leaderBoard_results_varieties');
add_shortcode('leaderBoard_results_section_leaders', 'leaderBoard_results_section_leaders');

?>
