<?php
  global $post;
  global $wpdb;

  $url     = wp_get_referer();
  $event_id = url_to_postid( $url );

  $classRegistrations = $_POST['classRegistrations'];
  $userName = $_POST['userName'];

  $userRegistrationsRepository = new UserRegistrationsRepository($event_id);
  $registrationOrderRepository = new RegistrationOrderRepository($event_id);
  $registrationCountRepository = new RegistrationCountRepository($event_id, LocationHelper::getIDFromEventPostID($event_id));
  $classIndexRepository = new ClassIndexRepository(LocationHelper::getIDFromEventPostID($event_id));

  $registrationService = new RegistrationService($event_id, $userRegistrationsRepository, $registrationOrderRepository, $registrationCountRepository, $classIndexRepository);
  $registrations = $registrationService->registerEntries($classRegistrations, $userName);

  $entriesService = new EntriesService(new EntryRepository($event_id));
  $entriesService->createEntriesFromRegistrations(LocationHelper::getIDFromEventPostID($event_id), $event_id);

  echo(RegistrationTablesView::getUserRegistrationOverviewHtml($userName, $registrations));

  wp_die();
