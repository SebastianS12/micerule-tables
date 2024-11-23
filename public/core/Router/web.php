<?php

Route::post(EntryBookController::class, "editPlacement", "editPlacement", ["placementNumber", "indexID", "entryID", "prizeID"]);
Route::post(EntryBookController::class, "editAwards", "editAwards", ["prizeID", "challengeIndexID", "oaChallengeIndexID"]);
Route::post(EntryBookController::class, "addEntry", "addEntry", ["userName", "classIndexID"]);
Route::put(EntryBookController::class, "editAbsent", "editAbsent", ["entryID"]);
Route::put(EntryBookController::class, "editVarietyName", "editVarietyName", ["entryID", "varietyName"]);
Route::get(EntryBookController::class, "getSelectOptions", "getSelectOptions", []);
Route::put(EntryBookController::class, "moveEntry", "moveEntry", ["entryID", "newClassIndexID"]);
Route::delete(EntryBookController::class, "deleteEntry", "deleteEntry", ["entryID"]);

Route::get(ShowClassesController::class, "getClassSelectOptionsHtml", "getClassSelectOptionsHtml", ["sectionName", "locationID"]);
Route::post(ShowClassesController::class, "addClass", "addClass", ["locationID", "className", "section"]);
Route::post(ShowClassesController::class, "swapClasses", "swapClasses", ["locationID", "firstClassID", "secondClassID"]);
Route::delete(ShowClassesController::class, "deleteClass", "deleteClass", ["classID", "locationID", "sectionName"]);

Route::put(EntrySummaryController::class, "setAllAbsent", "setAllAbsent", ["absent", "userName"]);

Route::post(JudgesReportController::class, "generalComment", "submitGeneralComment", ["commentID", "judgeID", "comment"]);
Route::post(JudgesReportController::class, "classReport", "submitClassReport", ["commentID", "indexID", "comment", "placementReports"]);

Route::post(ShowOptionsController::class, "locationSettings", "saveShowOptions", ["id", "locationID", "allowOnlineRegistrations", "registrationFee", "allowUnstandardised", "allowJunior", "allowAuction", "firstPrize", "secondPrize", "thirdPrize", "pmBiSec", "pmBoSec", "pmBIS", "pmBOA", "auctionFee"]);

Route::put(PrizeCardsController::class, "moveToUnprinted", "moveToUnprinted", ["placementID", "prizeID"]);
Route::put(PrizeCardsController::class, "printAll", "printAll", ["prizeCardsData"]);

Route::post(RegistrationTablesController::class, "registrations", "register", ["classRegistrations", "userName"]);
Route::get(RegistrationTablesController::class, "registrations", "updateRegistrationTables", ["fancierName"]);

Route::post(ShowReportPostController::class, "showPost", "createPost", []);

Route::get(AdminTabsController::class, "adminTabs", "getViewHtml", []);
