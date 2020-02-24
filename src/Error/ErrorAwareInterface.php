<?php

namespace App\Error;

interface ErrorAwareInterface
{
    public function hasError(): bool;

    public function getError(): ?Error;

    public function setError(Error $error);
}
