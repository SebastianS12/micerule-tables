<?php
function showRegistrationFee(){
  global $post;
  $showOptionsController = new ShowOptionsController();
  $showOptionsModel = $showOptionsController->getShowOptions(LocationHelper::getIDFromEventPostID($post->ID), new ShowOptionsService(), new ShowOptionsRepository());//get_post_meta($post->ID, 'micerule_data_event_optional_settings', true);
  $numberFormatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
  $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);

  $html = "<div class='entry-fee-wrapper'>";
  $html .= "<div class = 'entry-fee-wrapper'>";
  $html .= "<h1 class='blockentry' style= 'font-family: Bree Serif; color: #926939; display: flex; vertical-align: middle; text-align: center; font-size: 46px; font-weight: bold'>";
  $html .= "£".$showOptionsModel->registration_fee;
  $html .= "</h1>";
  $html .= getAuctionFeeHtml($showOptionsModel, $numberFormatter);
  $html .= "<h1>PRIZE MONEY</h1>";
  $html .= "</div>";
  $html .= getStandardPrizeMoneyHtml($showOptionsModel, $numberFormatter);
  $html .= getSectionPrizeMoneyHtml($showOptionsModel, $numberFormatter);
  $html .= getGrandChallengePrizeMoneyHtml($showOptionsModel, $numberFormatter);
  $html .= "</div>";

  return $html;
}

function getStandardPrizeMoneyHtml(ShowOptionsModel $showOptions, NumberFormatter $numberFormatter): string
{
  $html = "";

  if($showOptions->pm_first_place > 0 || $showOptions->pm_second_place > 0 || $showOptions->pm_third_place > 0){
    $html .= "<h3 style='top: 2px; position: relative;'>Breed Classes: ";
    $html .= ($showOptions->pm_first_place >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->pm_first_place, "GBP").", " : ($showOptions->pm_first_place * 100)."p, ";
    $html .= ($showOptions->pm_second_place >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->pm_second_place, "GBP").", " : ($showOptions->pm_second_place * 100)."p, ";
    $html .= ($showOptions->pm_third_place >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->pm_third_place, "GBP") : ($showOptions->pm_third_place * 100)."p";
    $html .= "</h3>";
  }

  return $html;
}

function getSectionPrizeMoneyHtml(ShowOptionsModel $showOptions, NumberFormatter $numberFormatter): string
{
  $html = "";

  if($showOptions->pm_bisec > 0 || $showOptions->pm_bosec > 0){
    $html .= "<h3 style='top: 2px; position: relative;'>Sections: ";
    $html .= ($showOptions->pm_bisec >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->pm_bisec, "GBP").", " : ($showOptions->pm_bisec * 100)."p, ";
    $html .= ($showOptions->pm_bosec >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->pm_bosec, "GBP") : ($showOptions->pm_bosec * 100)."p";
  }

  return $html;
}

function getGrandChallengePrizeMoneyHtml(ShowOptionsModel $showOptions, NumberFormatter $numberFormatter): string
{
  $html = "";

  if($showOptions->pm_bis > 0 || $showOptions->pm_boa > 0){
    $html .= "<h3 style='top: 2px; position: relative;'>Grand Challenge: ";
    $html .= ($showOptions->pm_bis >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->pm_bis, "GBP").", " : ($showOptions->pm_bis * 100)."p, ";
    $html .= ($showOptions->pm_boa >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->pm_boa, "GBP") : ($showOptions->pm_boa * 100)."p";
  }

  return $html;
}

function getAuctionFeeHtml(ShowOptionsModel $showOptions, NumberFormatter $numberFormatter): string
{
  $html = "";

  if($showOptions->allow_auction && $showOptions->auction_fee > 0){
    $html .= "<div class='auction-fee'>Auction Class ";
    $html .= ($showOptions->auction_fee >= 1.0) ? $numberFormatter->formatCurrency((float)$showOptions->auction_fee, "GBP") : ($showOptions->auction_fee * 100)."p";
    $html .= "</div>";
  }

  return $html;
}

add_shortcode('registrationFee', 'showRegistrationFee');
