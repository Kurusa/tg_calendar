<?php

namespace App\Services\Enums;

final class UserStatus {

    const NEW              = 'new';
    const ASK_TITLE        = 'asking title of event';
    const ASK_DESCRIPTION  = 'asking description of event';
    const EDIT_DESCRIPTION = 'editing description of event';
    const EDIT_TITLE       = 'editing title of event';

}
