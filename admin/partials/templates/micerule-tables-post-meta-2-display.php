<div class="judges">
    <h1>Judges</h1>
    <?php echo getJudgesHtml(); ?>
    <br>
</div>

<?php
function getJudgesHtml()
{
    global $post;
    global $wpdb;
    $users = (array) $wpdb->get_results("SELECT display_name FROM " . $wpdb->prefix . "users ORDER BY display_name;");
    $html = "";
    for ($judgeNo = 1; $judgeNo <= 3; $judgeNo++) {
        $judgeName = $wpdb->get_row("SELECT judge_name FROM " . $wpdb->prefix . "micerule_event_judges WHERE event_post_id = " . $post->ID . " AND judge_no = " . $judgeNo, ARRAY_A);
        $html .= "<strong>Judge " . $judgeNo . "</strong><br>
                  <select name= 'judge_data[" . $judgeNo . "][name]' autocomplete='off'>
                    <option value=''>" . (($judgeNo == 1) ? 'Please Select' : 'None') . "</option>";
        foreach ($users as $user) {
            $html .= "<option value='" . $user->display_name . "' " . ((isset($judgeName) && $judgeName['judge_name'] == $user->display_name) ? 'selected="selected"' : '') . ">";
            $html .= $user->display_name;
            $html .= "</option>";
        }
        $html .= "</select>";

        $judgeSections = $wpdb->get_results("SELECT section FROM " . $wpdb->prefix . "micerule_event_judges_sections WHERE event_post_id = " . $post->ID . " AND judge_no = " . $judgeNo, ARRAY_A);
        foreach (EventProperties::SECTIONNAMES as $sectionName) {
            $html .= "<input id='judge-section-select-" . $sectionName . "-" . $judgeNo . "' type='checkbox' name='judge_data[" . $judgeNo . "][sections][" . strtolower($sectionName) . "]' " . ((array_search(strtolower($sectionName), array_column($judgeSections, 'section')) !== false) ? 'checked="on"' : '') . ">";
            $html .= "<label for='judge-section-select-" . $sectionName . "-" . $judgeNo . "'>" . $sectionName . "</label>";
        }
        $html .= getJudgePartnershipHtml($post->ID, $judgeNo, $users);
        $html .= "<br>";
    }

    return $html;
}

function getJudgePartnershipHtml($eventPostID, $judgeNo, $users)
{
    global $wpdb;
    $judgePartnerName = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "micerule_event_judges_partnerships WHERE event_post_id = " . $eventPostID . " AND judge_no = " . $judgeNo, ARRAY_A);
    $html = "<div class='partnership-judge'>
                <input type='checkbox' class='pCheck' " . (isset($judgePartnerName) ? 'checked="on"' : '') . ">
                <label for='partnership" . $judgeNo . "'>Partnership</label>";

    $html .= "<select name= 'judge_data[" . $judgeNo . "][partnership]' style='display:" . (isset($judgePartnerName) ? 'inline' : 'none') . "' class='partnership-select' autocomplete='off'>
                <option value=''>Please Select</option>";
    foreach ($users as $user) {
        $html .= "<option value='" . $user->display_name . "' " . ((isset($judgePartnerName) && $judgePartnerName['partner_name'] == $user->display_name) ? 'selected="selected"' : '') . ">";
        $html .= $user->display_name;
        $html .= "</option>";
    }
    $html .= "</select>";
    $html .= "</div>";

    return $html;
}
?>