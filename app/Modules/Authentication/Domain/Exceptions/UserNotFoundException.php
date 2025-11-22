<?php

namespace App\Modules\User\Domain\Exceptions;

use DomainException;

class UserNotFoundException extends DomainException
{
    public function __construct($message = 'The requested user was not found.')
    {
        parent::__construct($message);
    }
}
