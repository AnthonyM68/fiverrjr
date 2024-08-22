<?php

namespace App\Serializer;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CircularReferenceHandler implements CircularReferenceHandlerInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle($object)
    {
        return $object->getId();
    }
}
