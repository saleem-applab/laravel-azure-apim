<?php

namespace Applab\LaravelAzureApim\Exceptions;

use RuntimeException;

class ApimAuthException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
