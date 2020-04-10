<?php

namespace App\Serializer\Normalizer;

use App\Exception\APIException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormErrorNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $nameConverter;

    public function __construct(APIExceptionNormalizer $normalizer, NameConverterInterface $nameConverter)
    {
        $this->normalizer = $normalizer;
        $this->nameConverter = $nameConverter;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $exception = new APIException(422, '_form_validation_failed');

        $data = $this->normalizer->normalize($exception, $format, $context);
        $data['errors'] = $this->convertFormErrorsToArray($object);

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

    private function convertFormErrorsToArray(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getIterator() as $key => $child) {
            $snakeCaseName = $this->nameConverter->normalize($key);
            foreach ($child->getErrors() as $error) {
                $errors[$snakeCaseName] = $error->getMessage();
                break;
            }

            if (\count($child->getIterator()) > 0 && !$child instanceof SubmitButton) {
                $childErrors = $this->convertFormErrorsToArray($child);
                if (!empty($childErrors)) {
                    $errors[$snakeCaseName] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
