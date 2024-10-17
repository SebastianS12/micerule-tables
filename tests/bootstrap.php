<?php

require_once dirname(__DIR__).'/public/core/database/Model.php';
require_once dirname(__DIR__).'/public/partials/Models/ShowEntry.php';

require_once dirname(__DIR__).'/public/partials/Enums/Prizes.php';
require_once dirname(__DIR__).'/public/partials/Enums/Awards.php';
require_once dirname(__DIR__).'/public/partials/Enums/Tables.php';

require_once dirname(__DIR__).'/public/core/database/Collection.php';
require_once dirname(__DIR__).'/public/core/database/Model.php';
require_once dirname(__DIR__).'/public/core/database/IQueryCombine.php';
require_once dirname(__DIR__).'/public/core/database/IQueryWhere.php';
require_once dirname(__DIR__).'/public/core/database/QueryJoin.php';
require_once dirname(__DIR__).'/public/core/database/QueryJoinSub.php';
require_once dirname(__DIR__).'/public/core/database/QueryWhere.php';
require_once dirname(__DIR__).'/public/core/database/QueryWhereNull.php';
require_once dirname(__DIR__).'/public/core/database/QueryWhereNested.php';
require_once dirname(__DIR__).'/public/core/database/QueryBuilder.php';
require_once dirname(__DIR__).'/public/core/database/ModelHydrator.php';

require_once dirname(__DIR__).'/public/partials/Leaderboard/LeaderboardController.php';
require_once dirname(__DIR__).'/public/partials/Leaderboard/LeaderboardModel.php';
require_once dirname(__DIR__).'/public/partials/Leaderboard/LeaderboardView.php';

require_once dirname(__DIR__).'/public/partials/Repositories/IRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/EntryRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/ShowClassesRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/UserRegistrationsRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/AwardsRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/PrizeCardsRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/JudgesRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/BreedsRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/ShowSectionRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/JudgesReportRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/ChallengeIndexRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/ClassIndexRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/RegistrationCountRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/RegistrationOrderRepository.php';
require_once dirname(__DIR__).'/public/partials/Repositories/PlacementsRepository.php';

require_once dirname(__DIR__).'/public/partials/RegistrationTables/RegistrationTablesController.php';
require_once dirname(__DIR__).'/public/partials/RegistrationTables/RegistrationTablesModel.php';
require_once dirname(__DIR__).'/public/partials/RegistrationTables/RegistrationTablesView.php';
require_once dirname(__DIR__).'/public/partials/RegistrationTables/RegistrationTablesViewModel.php';

require_once dirname(__DIR__).'/public/partials/FancierEntries/FancierEntriesController.php';
require_once dirname(__DIR__).'/public/partials/FancierEntries/FancierEntriesView.php';

require_once dirname(__DIR__).'/public/partials/EntrySummary/EntrySummaryController.php';
require_once dirname(__DIR__).'/public/partials/EntrySummary/EntrySummaryModel.php';
require_once dirname(__DIR__).'/public/partials/EntrySummary/EntrySummaryView.php';

require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookController.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookModel.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookView.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookPlacementController.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookPlacementView.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookRowController.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookRowView.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/RowPlacementData.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/ChallengeRowView.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/ChallengeRowController.php';
require_once dirname(__DIR__).'/public/partials/EntryBook/EntryBookViewModel.php';

require_once dirname(__DIR__).'/public/partials/Label/LabelModel.php';
require_once dirname(__DIR__).'/public/partials/Label/LabelView.php';

require_once dirname(__DIR__).'/public/partials/JudgingSheets/JudgingSheetsModel.php';
require_once dirname(__DIR__).'/public/partials/JudgingSheets/JudgingSheetsView.php';

require_once dirname(__DIR__).'/public/partials/Absentees/AbsenteesModel.php';
require_once dirname(__DIR__).'/public/partials/Absentees/AbsenteesView.php';

require_once dirname(__DIR__).'/public/partials/PrizeCards/PrizeCardsController.php';
require_once dirname(__DIR__).'/public/partials/PrizeCards/PrizeCardsModel.php';
require_once dirname(__DIR__).'/public/partials/PrizeCards/PrizeCardsView.php';

require_once dirname(__DIR__).'/public/partials/JudgesReport/JudgesReportController.php';
require_once dirname(__DIR__).'/public/partials/JudgesReport/JudgesReportModel.php';
require_once dirname(__DIR__).'/public/partials/JudgesReport/JudgesReportView.php';

require_once dirname(__DIR__).'/public/partials/DAOs/IPrintDAO.php';
require_once dirname(__DIR__).'/public/partials/DAOs/AwardsDAO.php';
require_once dirname(__DIR__).'/public/partials/DAOs/IPlacementDAO.php';
require_once dirname(__DIR__).'/public/partials/DAOs/PlacementDAO.php';

require_once dirname(__DIR__).'/public/partials/Models/ShowEntry.php';
require_once dirname(__DIR__).'/public/partials/Models/PlacementModel.php';
require_once dirname(__DIR__).'/public/partials/Models/SinglePlacement.php';
require_once dirname(__DIR__).'/public/partials/Models/NextPenNumber.php';
require_once dirname(__DIR__).'/public/partials/Models/PrizeCards.php';
require_once dirname(__DIR__).'/public/partials/Models/UserClassRegistration.php';
require_once dirname(__DIR__).'/public/partials/Models/ChallengeAwards.php';
require_once dirname(__DIR__).'/public/partials/Models/PlacementReport.php';
require_once dirname(__DIR__).'/public/partials/Models/ClassComment.php';
require_once dirname(__DIR__).'/public/partials/Models/GeneralComment.php';
require_once dirname(__DIR__).'/public/partials/Models/EntryClassModel.php';
require_once dirname(__DIR__).'/public/partials/Models/UserRegistrationModel.php';
require_once dirname(__DIR__).'/public/partials/Models/ShowClassModel.php';
require_once dirname(__DIR__).'/public/partials/Models/ShowChallengeModel.php';
require_once dirname(__DIR__).'/public/partials/Models/RegistrationOrderModel.php';

require_once dirname(__DIR__).'/public/partials/Services/PlacementsService.php';
require_once dirname(__DIR__).'/public/partials/Services/EntriesService.php';
require_once dirname(__DIR__).'/public/partials/Services/ChallengeRowService.php';
require_once dirname(__DIR__).'/public/partials/Services/IRowService.php';
require_once dirname(__DIR__).'/public/partials/Services/EntryRowService.php';
require_once dirname(__DIR__).'/public/partials/Services/OptionalClassRowService.php';
require_once dirname(__DIR__).'/public/partials/Services/JuniorRowService.php';
require_once dirname(__DIR__).'/public/partials/Services/JudgesService.php';
require_once dirname(__DIR__).'/public/partials/Services/PrizeCardsService.php';
require_once dirname(__DIR__).'/public/partials/Services/BreedsService.php';
require_once dirname(__DIR__).'/public/partials/Services/JudgesReportService.php';
require_once dirname(__DIR__).'/public/partials/Services/RegistrationTablesService.php';
require_once dirname(__DIR__).'/public/partials/Services/RegistrationService.php';
require_once dirname(__DIR__).'/public/partials/Services/EntryBookService.php';
require_once dirname(__DIR__).'/public/partials/Services/PlacementsRowService.php';

require_once dirname(__DIR__).'/public/partials/Factories/PrizeCardFactory.php';
require_once dirname(__DIR__).'/public/partials/Factories/PrintDAOFactory.php';