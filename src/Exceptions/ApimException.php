<?php

namespace Applab\LaravelAzureApim\Exceptions;

use RuntimeException;

class ApimException extends RuntimeException
{
    protected int $httpStatus;
    protected array $errorBody;

    public function __construct(
        string $message = '',
        int $httpStatus = 0,
        array $errorBody = [],
        ?\Throwable $previous = null
    ) {
        $this->httpStatus = $httpStatus;
        $this->errorBody  = $errorBody;

        parent::__construct($message, $httpStatus, $previous);
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getErrorBody(): array
    {
        return $this->errorBody;
    }
}
