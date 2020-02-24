<?php

namespace App\Serializer\Normalizer;

use App\Error\Error;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ErrorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $translator;

    public function __construct(ObjectNormalizer $normalizer, TranslatorInterface $translator)
    {
        $this->normalizer = $normalizer;
        $this->translator = $translator;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        // trans detail for current language
        $data['detail'] = $this->translator->trans($data['detail']);

        // remove invalid_params if empty
        if (empty($data['invalid_params'])) {
            unset($data['invalid_params']);
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Error;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
