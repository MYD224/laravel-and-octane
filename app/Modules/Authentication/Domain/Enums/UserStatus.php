<?php

namespace App\Modules\Authentication\Domain\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';


    public function value(): string
    {
        return $this->value;
    }
}