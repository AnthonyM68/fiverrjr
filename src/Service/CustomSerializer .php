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

/**
     * Sérialise les données avec une fonctionnalité supplémentaire.
     *
     * @param mixed $data Les données à sérialiser.
     * @param string $format Le format de sérialisation (ex: json, xml).
     * @param array $context Le contexte de sérialisation optionnel.
     * @return string Les données sérialisées avec une annotation personnalisée.
     */
    public function serialize(mixed $data, string $format, array $context = []): string
    {
        // Utilisation du sérialiseur sous-jacent pour sérialiser les données
        $serializedData = $this->serializer->serialize($data, $format);

        // Ajout d'une annotation personnalisée au résultat de la sérialisation
        return sprintf("Serialized data: %s", $serializedData);
    }

    /**
     * Désérialise les données avec une fonctionnalité personnalisée.
     *
     * @param mixed $data Les données à désérialiser.
     * @param string $type Le type vers lequel désérialiser les données.
     * @param string $format Le format de sérialisation (ex: json, xml).
     * @param array $context Le contexte de désérialisation optionnel.
     * @return mixed Les données désérialisées avec un message de confirmation.
     */
    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        // Exemple simplifié de désérialisation personnalisée avec un message statique
        return "Custom deserialization was successful";
    }
}