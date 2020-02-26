<?php

namespace App\Serializer\Normalizer;

use App\Response\ErrorResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FormErrorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $nameConverter;

    public function __construct(ObjectNormalizer $normalizer, NameConverterInterface $nameConverter)
    {
        $this->normalizer = $normalizer;
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

        $response = new ErrorResponse(Response::HTTP_UNPROCESSABLE_ENTITY, 'Validation Failed');

        $data = $this->normalizer->normalize($response, $format, $context);
        $data['invalid_params'] = $invalidParams;

        return $data;
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
