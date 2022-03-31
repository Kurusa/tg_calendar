<?php

use App\Commands\{AddAdmin,
    CreateEvent\Description,
    CreateEvent\Hour,
    CreateEvent\Minutes,
    CreateEvent\Title,
    DaySchedule,
    Edit\Delete,
    EventInfo,
    MainMenu,
    SendCalendar,
    Cancel};
use App\Services\Enums\UserStatus;

return [
    'callback_commands' => [
        'create_event'     => Hour::class,
        'back_to_calendar' => SendCalendar::class,
        'event_hour'       => Minutes::class,
        'event_minutes'    => Title::class,
        'display_schedule' => DaySchedule::class,
        'event_info'       => EventInfo::class,
        'back_to_schedule' => DaySchedule::class,
        'delete_event'     => Delete::class,
        'edit_title'       => \App\Commands\Edit\Title::class,
        'edit_description' => \App\Commands\Edit\Description::class,
        'next_month'       => SendCalendar::class,
        'prev_month'       => SendCalendar::class,
        'create_event_from_schedule' => Hour::class,
    ],
    
    'keyboard_commands' => [
        'cancel'    => Cancel::class,
        'add_admin' => AddAdmin::class,
    ],
    
    'status_commands' => [
        UserStatus::NEW              => MainMenu::class,
        UserStatus::ASK_TITLE        => Title::class,
        UserStatus::ASK_DESCRIPTION  => Description::class,
        UserStatus::EDIT_DESCRIPTION => \App\Commands\Edit\Description::class,
        UserStatus::EDIT_TITLE       => \App\Commands\Edit\Title::class,
        UserStatus::ASK_ADMIN        => AddAdmin::class,
    ],
    
    'slash_commands' => [
        '/start'    => MainMenu::class,
        '/calendar' => SendCalendar::class,
    ],
    
];
