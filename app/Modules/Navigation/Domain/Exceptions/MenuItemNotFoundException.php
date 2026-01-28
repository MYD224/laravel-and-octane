<?php

namespace App\Modules\Navigation\Domain\Exceptions;

use DomainException;

class MenuItemNotFoundException extends DomainException
{
    public function __construct($message = 'The requested menu item was not found.')
    {
        parent::__construct($message);
    }
}
