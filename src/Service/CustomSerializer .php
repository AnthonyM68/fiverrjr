<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Serializer\SerializerInterface;

// intercepte les appels au sérialiseur par défaut et ajoute des fonctionnalités supplémentaires si nécessaire.

#[AsDecorator('serializer')]
class CustomSerializer implements SerializerInterface
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        $serializedData = $this->serializer->serialize($data, $format);

        return sprintf("Serialized data: %s", $serializedData);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return "Custom deserialization was successful";
    }
}