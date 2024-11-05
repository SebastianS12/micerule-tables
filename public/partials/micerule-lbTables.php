<?php

/*
* ajax action
*
* creates html for leaderboard page
*
*/

global $post;
global $wpdb;


//Get dateTo and dateFrom
$time= explode("/",$_POST['time']);

$html = LeaderboardView::getTopTwentyHtml($time[0], $time[1]);
$html .= LeaderboardView::getSeasonBISWinnerHtml($time[0], $time[1]);
$html .= LeaderboardView::getVarietyPopularityHtml($time[0], $time[1]);
$bisVarietyChartData = LeaderboardController::getBISVarietyChartData($time[0], $time[1]);

echo $html;
echo '||';
echo json_encode($bisVarietyChartData);
echo '||';
echo (LeaderboardView::getMobileChartLegendHtml($time[0], $time[1]));
echo '||';
echo(LeaderboardView::getSeasonSectionLeaderHtml($time[0], $time[1]));

wp_die();
