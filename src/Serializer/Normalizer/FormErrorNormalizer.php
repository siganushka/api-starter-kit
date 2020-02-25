<?php

namespace App\Serializer\Normalizer;

use App\Error\Error;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormErrorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $errorNormalizer;
    private $nameConverter;

    public function __construct(ErrorNormalizer $errorNormalizer, NameConverterInterface $nameConverter)
    {
        $this->errorNormalizer = $errorNormalizer;
        $this->nameConverter = $nameConverter;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $invalidParams = [];
        foreach ($object->getIterator() as $key => $child) {
            foreach ($child->getErrors() as $error) {
                $invalidParams[$this->nameConverter->normalize($key)] = $error->getMessage();
                break;
            }
        }

        $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        $detail = Response::$statusTexts[$status];

        $error = new Error($status, $detail);
        $error->setInvalidParams($invalidParams);

        return $this->errorNormalizer->normalize($error, $format, $context);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof FormInterface && $data->isSubmitted() && !$data->isValid();
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
