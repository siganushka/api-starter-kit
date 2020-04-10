<?php

namespace App\Security\Extractor;

use Symfony\Component\HttpFoundation\Request;

class AuthorizationHeaderExtractor implements TokenExtractorInterface
{
    private $name;
    private $prefix;

    public function __construct(string $name = 'Authorization', ?string $prefix = 'Bearer')
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The name must not be empty');
        }

        $this->name = $name;
        $this->prefix = $prefix;
    }

    public function extract(Request $request): ?string
    {
        if (!$request->headers->has($this->name)) {
            return null;
        }

        $token = $request->headers->get($this->name);

        if (empty($this->prefix)) {
            return $token;
        }

        $parts = explode(' ', $token);
        $parts = array_filter($parts);
        $parts = array_values($parts);

        if (!(2 === \count($parts) && 0 === strcasecmp($parts[0], $this->prefix))) {
            return null;
        }

        return $parts[1];
    }
}
