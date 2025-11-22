<?php

namespace App\Modules\Authentication\Application\Exceptions;

use App\Core\Application\Exceptions\ApplicationException;

class CannotUpdateUserException extends ApplicationException
{
    public function __construct($message = 'Error during update.')
    {
        parent::__construct($message);
    }
}
