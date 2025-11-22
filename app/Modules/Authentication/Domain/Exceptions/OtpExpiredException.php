<?php

namespace App\Modules\Authentication\Domain\Exceptions;

use App\Core\Exceptions\DomainException;
use DomainException as GlobalDomainException;

final class OtpExpiredException extends GlobalDomainException
{
    protected $message = "The OTP code has expired or does not exist.";
}
