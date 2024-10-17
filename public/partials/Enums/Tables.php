<?php

enum Table : string{
    case CLASSES = "micerule_show_classes";
    case CLASS_INDICES = "micerule_show_classes_indices";
    case CHALLENGE_INDICES = "micerule_show_challenges_indices";
    case REGISTRATIONS = "micerule_show_user_registrations";
    case REGISTRATIONS_ORDER = "micerule_show_user_registrations_order";
    case REGISTRATIONS_JUNIOR = "micerule_show_user_junior_registrations";
    case ENTRIES = "micerule_show_entries";
    case CLASS_PLACEMENTS = "micerule_show_class_placements";
    case CHALLENGE_PLACEMENTS = "micerule_show_challenge_placements";
    case AWARDS = "micerule_show_challenge_awards";

    public function getAlias(): string
    {
        return match($this) {
            self::CLASSES => 'Classes',
            self::CLASS_INDICES => 'ClassIndices',
            self::CHALLENGE_INDICES => 'ChallengeIndices',
            self::REGISTRATIONS => 'Registrations',
            self::REGISTRATIONS_ORDER => 'RegistrationsOrder',
            self::REGISTRATIONS_JUNIOR => 'JuniorRegistrations',
            self::ENTRIES => 'Entries',
            self::CLASS_PLACEMENTS => 'ClassPlacements',
            self::CHALLENGE_PLACEMENTS => 'ChallengePlacements',
            self::AWARDS => 'Awards'
        };
    }
}