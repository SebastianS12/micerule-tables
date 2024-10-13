<?php
  global $post;
  global $wpdb;

  $url     = wp_get_referer();
  $event_id = url_to_postid( $url );

  $classRegistrations = $_POST['classRegistrations'];
  // $optionalClassRegistrations = $_POST['optionalClassRegistrations'];
  $userName = $_POST['userName'];

  $userRegistrationsRepository = new UserRegistrationsRepository($event_id);
  $registrationOrderRepository = new RegistrationOrderRepository($event_id);
  $registrationCountRepository = new RegistrationCountRepository($event_id, EventProperties::getEventLocationID($event_id));
  $classIndexRepository = new ClassIndexRepository(EventProperties::getEventLocationID($event_id));

  $registrationService = new RegistrationService($event_id, $userRegistrationsRepository, $registrationOrderRepository, $registrationCountRepository, $classIndexRepository);
  $registrations = $registrationService->registerEntries($classRegistrations, $userName);

  $entriesService = new EntriesService(new EntryRepository($event_id));
  $entriesService->createEntriesFromRegistrations(EventProperties::getEventLocationID($event_id), $event_id);


  // RegistrationTablesController::registerEntries($event_id, $classRegistrations, $optionalClassRegistrations, $userName);
  // RegistrationTablesController::createEntriesFromRegistrations($event_id, EventProperties::getEventLocationID($event_id));
  echo(RegistrationTablesView::getUserRegistrationOverviewHtml($userName, $registrations));

  wp_die();
