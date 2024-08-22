<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\ServiceItem;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageService
{
    private $parameters;
    private $logger;

    public function __construct(
        ParameterBagInterface $parameters,
        LoggerInterface $logger
    ) {
        $this->parameters = $parameters;
        $this->logger = $logger;
    }
    public function handlerRoles($roles)
    {
        $role = match (true) {
            in_array('ROLE_ADMIN', $roles, true) => 'ROLE_ADMIN',
            in_array('ROLE_CLIENT', $roles, true) => 'ROLE_CLIENT',
            in_array('ROLE_DEVELOPER', $roles, true) => 'ROLE_DEVELOPER',
            default => 'ROLE_USER',
        };
        return $role;
    }
    // génére une url d'image et de son répertoire de stockage
    public function generateImageUrl(string $filename, string $role): string
    {
        $filePath = $this->getImagePath($filename, $role);
        if (!file_exists($filePath)) {
            throw new \Exception('Image not found' . $filePath);
        }

        return str_replace($this->parameters->get('kernel.project_dir') . '/public', '', $filePath);
    }

    // recherche une image a partir de son nom de fichier et d'un rôle pour iddentifier le répertoire
    public function getImagePath(string $filename, string $role): string
    {
        $uploadsDirectory = $this->getUploadDirectory($role);
        return $uploadsDirectory . '/' . $filename;
    }

    // recherche les paramètres repertoire du service.yaml
    private function getUploadDirectory(string $role): string
    {
        switch ($role) {
            case 'ROLE_ADMIN':
                return $this->parameters->get('admin_pictures_directory');
            case 'ROLE_DEVELOPER':
                return $this->parameters->get('developer_pictures_directory');
            case 'ROLE_CLIENT':
                return $this->parameters->get('client_pictures_directory');
            case 'SERVICE':
                return $this->parameters->get('service_pictures_directory');
            default:
                return $this->parameters->get('pictures_directory');
        }
    }

    // déplace une image et retourne son nom unique de fichier
    public function uploadImage(UploadedFile $file, string $role): string
    {
        // on vérfie l'extention du format si autorisé
        $allowedMimeTypes = ['image/jpeg', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception('Format de fichier non autorisé. Seules les images JPEG, webp, et GIF sont acceptées.');
        }
        // Limite à 2MB
        if ($file->getSize() > 2097152) {
            throw new \Exception('Le fichier est trop volumineux');
        }
        // on lui attribut un id unique
        $uploadsDirectory = $this->getUploadDirectory($role);
        $filename = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($uploadsDirectory, $filename);
        } catch (FileException $e) {
            throw new \Exception('Failed to upload image');
        }

        return $filename;
    }
    // efface une image
    public function deleteImage(string $filename, string $role): void
    {
        $filePath = $this->getImagePath($filename, $role);
        $filesystem = new Filesystem();

        if (!$filesystem->exists($filePath)) {
            throw new \Exception('Image not found');
        }

        try {
            $filesystem->remove($filePath);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete image');
        }
    }
    // affecte au champ picture de l'entité une url image générée
    public function setPictureUrl($entity)
    {
        $role = null;
        $roles = null;

        if (!$entity instanceof User) {
            $role = 'SERVICE';
        } else {
            $roles = $entity->getRoles();
            $role = $this->handlerRoles($roles);
        }
        $pictureFilename = $entity->getPicture();

        if ($pictureFilename) {
            try {
                $pictureUrl = $this->generateImageUrl($pictureFilename, $role);
                $this->logger->info('Generated picture URL' . $entity, ['pictureUrl' => $pictureUrl]);
                $entity->setPicture($pictureUrl);
            } catch (\Exception $e) {
                $this->logger->error('Failed to generate picture URL' . $entity, ['error' => $e->getMessage()]);
                throw $e;
            }
        }
    }
}
