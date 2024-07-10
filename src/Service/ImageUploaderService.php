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
        // Récupère l'image actuelle de l'utilisateur
        $currentPicture = $user->getPicture();
    
        // Si une image actuelle existe, supprimez-la du serveur
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
    
        // on change le répertoire de téléchargement en fonction du rôle de l'utilisateur
        if (in_array('ROLE_DEVELOPER', $user->getRoles())) {
            $uploadDirectory = $this->parameters->get('developer_pictures_directory');
        } elseif (in_array('ROLE_ENTERPRISE', $user->getRoles())) {
            $uploadDirectory = $this->parameters->get('enterprise_pictures_directory');
        }
        // on essaye de déplacer le fichier téléchargé vers le répertoire approprié
        try {
            $file->move($uploadDirectory, $newFilename);
            // on crée le chemin relatif à partir du répertoire de base des images
            $relativePath = str_replace($this->parameters->get('pictures_directory'), '', $uploadDirectory);
            $url = './img/' . $relativePath . '/' . $newFilename;
            // on met à jour l'URL de l'image dans l'objet utilisateur
            $user->setPicture($url);
        } catch (FileException $e) {
            throw new \LogicException('Une erreur s\'est produite lors du téléchargement de l\'image.');
        }
    }
    
}
