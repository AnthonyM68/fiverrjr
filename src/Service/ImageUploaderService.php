<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploaderService implements ImageUploaderInterface
{
    private $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function uploadImage(UploadedFile $file, $user): void
    {
        $currentPicture = $user->getPicture();
        if ($currentPicture) {
            $currentFilePath = $this->parameters->get('kernel.project_dir') . '/public/' . $currentPicture;
            if (file_exists($currentFilePath)) {
                unlink($currentFilePath);
            }
        }

        $newFilename = uniqid() . '.' . $file->guessExtension();
        $uploadDirectory = $this->parameters->get('pictures_directory');

        if (in_array('ROLE_DEVELOPER', $user->getRoles())) {
            $uploadDirectory = $this->parameters->get('developer_pictures_directory');
        } elseif (in_array('ROLE_ENTERPRISE', $user->getRoles())) {
            $uploadDirectory = $this->parameters->get('enterprise_pictures_directory');
        }

        try {
            $file->move($uploadDirectory, $newFilename);
            $relativePath = str_replace($this->parameters->get('pictures_directory'), '', $uploadDirectory);
            $url = './img/' . $relativePath . '/' . $newFilename;
            $user->setPicture($url);
        } catch (FileException $e) {
            throw new \LogicException('Une erreur s\'est produite lors du téléchargement de l\'image.');
        }
    }
}
