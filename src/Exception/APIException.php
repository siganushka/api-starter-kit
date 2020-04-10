<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class APIException extends \Exception
{
    private $statusCode;
    private $messageId;
    private $messageParameters;
    private $domain;

    public function __construct(int $statusCode, string $messageId, array $messageParameters = [], string $domain = null)
    {
        if (!isset(Response::$statusTexts[$statusCode])) {
            throw new \InvalidArgumentException(sprintf('Invalid HTTP Status Code: %d', $statusCode));
        }

        $this->statusCode = $statusCode;
        $this->messageId = $messageId;
        $this->messageParameters = $messageParameters;
        $this->domain = $domain;

        parent::__construct($messageId, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getMessageParameters(): array
    {
        return $this->messageParameters;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }
}
