<?php

namespace App\Serializer\Normalizer;

use App\Exception\APIException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class APIExceptionNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $status = $object->getStatusCode();
        $message = $this->translator->trans(
            $object->getMessageId(),
            $object->getMessageParameters(),
            $object->getDomain());

        return [
            'type' => 'https://tools.ietf.org/html/rfc7807',
            'title' => Response::$statusTexts[$status],
            'status' => $status,
            'detail' => $message,
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof APIException;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
