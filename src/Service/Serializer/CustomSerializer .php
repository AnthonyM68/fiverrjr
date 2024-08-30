<?php

namespace App\Service\Serializer;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Serializer\SerializerInterface;


// intercepte les appels au sérialiseur par défaut et ajoute des fonctionnalités supplémentaires si nécessaire.
#[AsDecorator('serializer')]
class CustomSerializer implements SerializerInterface
{
    public function __construct(private readonly SerializerInterface $serializer) {}

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        // Sérialisation des données avec des fonctionnalités supplémentaires (si nécessaire)
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        // Désérialisation des données avec des fonctionnalités supplémentaires (si nécessaire)
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}
