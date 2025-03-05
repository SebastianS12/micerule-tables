<?php

Route::post(EntryBookController::class, "editPlacement", "editPlacement", ["eventPostID", "placementNumber", "indexID", "entryID", "prizeID"]);
Route::post(EntryBookController::class, "editAwards", "editAwards", ["eventPostID", "prizeID", "challengeIndexID", "oaChallengeIndexID"]);
Route::post(EntryBookController::class, "addEntry", "addEntry", ["eventPostID", "userName", "classIndexID"]);
Route::put(EntryBookController::class, "editAbsent", "editAbsent", ["entryID"]);
Route::put(EntryBookController::class, "editVarietyName", "editVarietyName", ["eventPostID", "entryID", "varietyName"]);
Route::get(EntryBookController::class, "getSelectOptions", "getSelectOptions", ["eventPostID"]);
Route::put(EntryBookController::class, "moveEntry", "moveEntry", ["eventPostID", "entryID", "newClassIndexID"]);
Route::delete(EntryBookController::class, "deleteEntry", "deleteEntry", ["eventPostID", "entryID"]);

Route::get(ShowClassesController::class, "getClassSelectOptionsHtml", "getClassSelectOptionsHtml", ["sectionName", "locationID"]);
Route::post(ShowClassesController::class, "addClass", "addClass", ["locationID", "className", "section"]);
Route::post(ShowClassesController::class, "swapClasses", "swapClasses", ["locationID", "firstClassID", "secondClassID"]);
Route::delete(ShowClassesController::class, "deleteClass", "deleteClass", ["classID", "locationID", "sectionName"]);

Route::put(EntrySummaryController::class, "setAllAbsent", "setAllAbsent", ["eventPostID", "absent", "userName"]);

Route::post(JudgesReportController::class, "generalComment", "submitGeneralComment", ["eventPostID", "commentID", "judgeID", "comment"]);
Route::post(JudgesReportController::class, "classReport", "submitClassReport", ["eventPostID", "commentID", "indexID", "comment", "placementReports"]);

Route::post(ShowOptionsController::class, "locationSettings", "saveShowOptions", ["id", "locationID", "allowOnlineRegistrations", "registrationFee", "firstPrize", "secondPrize", "thirdPrize", "allowUnstandardised", "allowJunior", "allowAuction", "pmBiSec", "pmBoSec", "pmBIS", "pmBOA", "auctionFee"]);

Route::put(PrizeCardsController::class, "moveToUnprinted", "moveToUnprinted", ["placementID", "prizeID"]);
Route::put(PrizeCardsController::class, "printAll", "printAll", ["prizeCardsData"]);

Route::post(RegistrationTablesController::class, "registrations", "register", ["eventPostID", "classRegistrations", "userName"]);
Route::get(RegistrationTablesController::class, "registrations", "updateRegistrationTables", ["eventPostID", "fancierName"]);

Route::post(ShowReportPostController::class, "showPost", "createPost", ["eventPostID"]);

Route::get(AdminTabsController::class, "adminTabs", "getViewHtml", ["eventPostID"]);
