<?php

namespace App\Error;

/**
 * @see https://tools.ietf.org/html/rfc7807
 */
class Error
{
    /**
     * A URI reference that identifies the problem type.
     *
     * @var string
     */
    private $type = 'https://tools.ietf.org/html/rfc2616#section-10';

    /**
     * A short, human-readable summary of the problem type.
     *
     * @var string
     */
    private $title = 'An error occurred';

    /**
     * The HTTP status code.
     *
     * @var int
     */
    private $status;

    /**
     * A human-readable explanation specific to this occurrence of the problem.
     *
     * @var string
     */
    private $detail;

    /**
     * The invalid params for post request.
     *
     * @var array
     */
    private $invalidParams = [];

    public function __construct(int $status, string $detail)
    {
        $this->setStatus($status);
        $this->setDetail($detail);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getInvalidParams(): array
    {
        return $this->invalidParams;
    }

    public function setInvalidParams(array $invalidParams): self
    {
        $this->invalidParams = $invalidParams;

        return $this;
    }
}
