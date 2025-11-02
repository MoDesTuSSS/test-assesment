<?php

namespace App\Serializer;

use App\Entity\News;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NewsNormalizer implements NormalizerInterface
{
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        /** @var News $object */
        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'body' => $object->getBody(),
            'image' => $object->getImage(),
            'createdAt' => $object->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof News;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            News::class => true,
        ];
    }
}

