<div class="judges">
<h1>Judges</h1>
<?php echo getJudgeHtml($users); ?>
<br>
</div>

<?php 
function getJudgeHtml($users){
    global $post;
    global $wpdb;
    $html = "";
    for($judgeNo = 1; $judgeNo <= 3; $judgeNo++){
        $judgeName = $wpdb->get_row("SELECT judge_name FROM ".$wpdb->prefix."micerule_event_judges WHERE event_post_id = ".$post->ID." AND judge_no = ".$judgeNo, ARRAY_A);
        $html .= "<p>".var_export($judgeName['judge_name'], true)."</p>";
        $html .= "<strong>Judge ".$judgeNo."</strong><br>
                  <select name= 'judge_data[".$judgeNo."][name]' autocomplete='off'>
                    <option value=''>".(($judgeNo == 1) ? 'Please Select' : 'None')."</option>";
        foreach($users as $user){
            $html .= "<option value='".$user->display_name."' ".((isset($judgeName) && $judgeName['judge_name'] == $user->display_name) ? 'selected="selected"' : '').">";
            $html .= $user->display_name;
            $html .= "</option>";
        }
        $html .= "</select>";

        $judgeSections = $wpdb->get_results("SELECT section FROM ".$wpdb->prefix."micerule_event_judges_sections WHERE event_post_id = ".$post->ID." AND judge_no = ".$judgeNo, ARRAY_A);
        foreach(EventProperties::SECTIONNAMES as $sectionName){
            $html .= "<input id='judge-section-select-".$sectionName."-".$judgeNo."' type='checkbox' name='judge_data[".$judgeNo."][sections][".strtolower($sectionName)."]' ".((array_search(strtolower($sectionName), array_column($judgeSections, 'section')) !== false) ? 'checked="on"' : '').">";
            $html .= "<label for='judge-section-select-".$sectionName."-".$judgeNo."'>".$sectionName."</label>";
        }

        $judgePartnerName = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_event_judges_partnerships WHERE event_post_id = ".$post->ID." AND judge_no = ".$judgeNo, ARRAY_A);
        $html .= "<div class='partnership-judge'>
                    <input type='checkbox' class='pCheck' ".(isset($judgePartnerName) ? 'checked="on"' : '').">
                    <label for='partnership".$judgeNo."'>Partnership</label>";
        
        $html .= "<select name= 'judge_data[".$judgeNo."][partnership]' style='display:".(isset($judgePartnerName) ? 'inline' : 'none')."' class='partnership-select' autocomplete='off'>
                    <option value=''>Please Select</option>";
                    foreach($users as $user){
                        $html .= "<option value='".$user->display_name."' ".((isset($judgePartnerName) && $judgePartnerName['partner_name'] == $user->display_name) ? 'selected="selected"' : '').">";
                        $html .= $user->display_name;
                        $html .= "</option>";
                    }
        $html .= "</select>";
        $html .= "</div>";
        //$html .= "<input type='hidden' name='micerule_judge_data[".$judgeNo."][data_id]' value='".((isset($judgeData['id'])) ? $judgeData['id'] : "")."'>";
        $html .= "<br>";
    }

    return $html;
}
?>
