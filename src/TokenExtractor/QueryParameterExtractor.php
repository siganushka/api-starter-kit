<?php

namespace App\TokenExtractor;

use Symfony\Component\HttpFoundation\Request;

class QueryParameterExtractor implements TokenExtractorInterface
{
    private $name;

    public function __construct(string $name = 'bearer')
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The name must not be empty');
        }

        $this->name = $name;
    }

    public function extract(Request $request): ?string
    {
        return $request->query->get($this->name);
    }
}
