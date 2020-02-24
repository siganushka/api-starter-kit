<?php

namespace App\Serializer\Normalizer;

use FOS\RestBundle\Normalizer\CamelKeysNormalizer;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class SymfonyCamelKeysNormalizer extends CamelKeysNormalizer
{
    private $nameConverter;

    public function __construct(NameConverterInterface $nameConverter)
    {
        $this->nameConverter = $nameConverter;
    }

    protected function normalizeString($string)
    {
        return $this->nameConverter->denormalize($string);
    }
}
