<?php

namespace App\Core\Application\Exceptions;

use Exception;

abstract class ApplicationException extends Exception
{
    /**
     * A domain exception represents a business-rule violation or
     * an invalid domain state. It should NOT expose infrastructure details.
     *
     * @var string
     */
    protected string $errorCode = 'application_error';

    public function __construct(
        string $message = 'An application exception occurred.',
        ?string $errorCode = null
    ) {
        if ($errorCode) {
            $this->errorCode = $errorCode;
        }

        parent::__construct($message);
    }

    /**
     * Unique error code for this domain error.
     * Useful for API responses, business logs, translations, etc.
     */
    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
