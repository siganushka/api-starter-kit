<?php

namespace App\Error;

trait ErrorAwareTrait
{
    private $error;

    public function hasError(): bool
    {
        return null !== $this->error;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }

    public function setError(Error $error)
    {
        $this->error = $error;
    }
}
