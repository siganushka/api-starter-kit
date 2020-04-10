<?php

namespace App\Security\Extractor;

use Symfony\Component\HttpFoundation\Request;

class ChainExtractor implements TokenExtractorInterface
{
    private $extractors = [];

    public function addExtractor(TokenExtractorInterface $extractor)
    {
        $this->extractors[] = $extractor;
    }

    public function extract(Request $request): ?string
    {
        foreach ($this->extractors as $extractor) {
            if (null !== $token = $extractor->extract($request)) {
                return $token;
            }
        }

        return null;
    }
}
