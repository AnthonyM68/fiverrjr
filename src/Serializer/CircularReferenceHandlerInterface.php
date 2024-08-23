<?php
namespace App\Serializer;

interface CircularReferenceHandlerInterface
{
    public function handle($object);
}
