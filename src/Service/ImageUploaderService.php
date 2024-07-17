<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\ServiceItem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploaderService implements ImageUploaderInterface
{
    private $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function uploadImage(UploadedFile $file, $data): void
    {
        $currentPicture = $data->getPicture();
        if ($currentPicture) {
            $currentFilePath = $this->parameters->get('kernel.project_dir') . '/public/' . $currentPicture;
            if (file_exists($currentFilePath)) {
                unlink($currentFilePath);
            }
        }

        // on génère un nom de fichier unique pour la nouvelle image
        $newFilename = uniqid() . '.' . $file->guessExtension();

        // on définit le répertoire de téléchargement par défaut
        $uploadDirectory = $this->parameters->get('pictures_directory');

        if ($data instanceof User) {
            if (in_array('ROLE_DEVELOPER', $data->getRoles())) {
                $uploadDirectory = $this->parameters->get('developer_pictures_directory');
            } elseif (in_array('ROLE_CLIENT', $data->getRoles())) {
                $uploadDirectory = $this->parameters->get('client_pictures_directory');
            }
        } else if ($data instanceof ServiceItem) {
            $uploadDirectory = $this->parameters->get('service_pictures_directory');
        }
        try {
            $file->move($uploadDirectory, $newFilename);
            // on crée le chemin relatif à partir du répertoire de base des images
            $relativePath = str_replace($this->parameters->get('pictures_directory'), '', $uploadDirectory);
            $url = './img/' . $relativePath . '/' . $newFilename;
            $data->setPicture($url);
        } catch (FileException $e) {
            throw new \LogicException('Une erreur s\'est produite lors du téléchargement de l\'image.');
        }
    }
}
