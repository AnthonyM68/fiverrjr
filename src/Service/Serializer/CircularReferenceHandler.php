<?php

namespace App\Service\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}
