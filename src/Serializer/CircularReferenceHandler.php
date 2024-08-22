<?php

namespace App\Serializer;

<<<<<<< HEAD
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
=======
class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}
>>>>>>> a5feb3db027be62ad942fe5c640558f052dbbba0
