<?php

namespace App\Serializer\Normalizer;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class KnpPaginationNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $items = [];
        foreach ($object->getItems() as $item) {
            $items[] = $this->normalizer->normalize($item, $format, $context);
        }

        $data = [
            'current_page_number' => $object->getCurrentPageNumber(),
            'items_per_page' => $object->getItemNumberPerPage(),
            'total_count' => $object->getTotalItemCount(),
            'items' => $items,
        ];

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof PaginationInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
