<?php

/*
*	creates html for the leaderboard page,
*
*	creates divs for the lb-tables.js to append to
*/

function leaderBoard_results(){
	$html = LeaderboardView::getSeasonSelectHtml();
	return $html;

}

function leaderBoard_results_topTwenty(){
	return '<div id="lbTopTwenty" class="lbTables" style="width:100%;"></div>';
}

function leaderBoard_results_bis(){
	return '<div id="lbBIS" class="lbTables" style="width:100%;"></div>';
}

function leaderBoard_results_chart(){
	return '<div id="chart" class="lbTables" style="width:100%;"></div>';
}

function leaderBoard_results_varieties(){
	return '<div id="lbVarieties" class="lbTables" style="width:100%;"></div>';
}

function leaderBoard_results_section_leaders(){
	return '<div id="lbSectionLeaders" class="lbTables" style="width:100%;"></div';
}

add_shortcode('leaderBoard_results','leaderBoard_results');
add_shortcode('leaderBoard_results_topTwenty','leaderBoard_results_topTwenty');
add_shortcode('leaderBoard_results_bis','leaderBoard_results_bis');
add_shortcode('leaderBoard_results_chart','leaderBoard_results_chart');
add_shortcode('leaderBoard_results_varieties','leaderBoard_results_varieties');
add_shortcode('leaderBoard_results_section_leaders', 'leaderBoard_results_section_leaders');

?>
