<?php
namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Repository\ServiceItemRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageController extends AbstractController
{
    private $parameters;
    private $logger;
    public function __construct(ParameterBagInterface $parameters,  LoggerInterface $logger)
    {
        $this->parameters = $parameters;
        $this->logger = $logger;
    }

    #[Route('/api/uploadImage', name: 'upload_img', methods: ['POST'])]
    public function uploadImage(UploadedFile $file, String $role): JsonResponse
    {
        // $originalFilename = $file->getClientOriginalName();
        // Récupère le répertoire de téléchargement à partir des paramètres et ( services.yaml )
        $uploadsDirectory = $this->parameters->get('pictures_directory');

        if ($role === "ROLE_DEVELOPER") {
            $uploadsDirectory = $this->parameters->get('developer_pictures_directory');
        } else if ($role === "ROLE_CLIENT") {
            $uploadsDirectory = $this->parameters->get('client_pictures_directory');
        } else if ($role === "SERVICE") {
            $uploadsDirectory = $this->parameters->get('service_pictures_directory');
        }

        // Génère un nom de fichier unique pour éviter les conflits
        $filename = uniqid() . '.' . $file->guessExtension();

        try {
            // Déplace le fichier téléchargé vers le répertoire de téléchargement
            $file->move($uploadsDirectory, $filename);
        } catch (FileException $e) {
            // Retourne une réponse d'erreur si le déplacement du fichier échoue
            return new JsonResponse(['error' => 'Failed to upload image'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Retourne le nom de fichier dans la réponse
        return new JsonResponse(['filename' => $filename], Response::HTTP_OK);
    }

    #[Route('/api/images/{filename}', name: 'get_image', methods: ['GET'])]
    public function getImage(string $filename, String $role): Response
    {
        // Récupère le répertoire de téléchargement à partir des paramètres
        $uploadsDirectory = $this->parameters->get('pictures_directory');

        if ($role === "ROLE_DEVELOPER") {
            $uploadsDirectory = $this->parameters->get('developer_pictures_directory');
        } else if ($role === "ROLE_CLIENT") {
            $uploadsDirectory = $this->parameters->get('client_pictures_directory');
        } else if ($role === "SERVICE") {
            $uploadsDirectory = $this->parameters->get('service_pictures_directory');
        }

        $filePath = $uploadsDirectory . '/' . $filename;

        // Vérifie si le fichier existe
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('getImage: Image not found');
        }

        // Récupère le type MIME du fichier pour le contenu de la réponse
        $mimeType = mime_content_type($filePath);

        // Retourne le contenu de l'image avec le type MIME approprié
        return new Response(file_get_contents($filePath), Response::HTTP_OK, ['Content-Type' => $mimeType]);
    }


    #[Route('/api/deleteImage/{filename}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(string $filename, String $role ): JsonResponse
    {
        // // Récupère le répertoire de téléchargement à partir des paramètres
        $uploadsDirectory = $this->parameters->get('pictures_directory');

        if ($role === "ROLE_DEVELOPER") {
            $uploadsDirectory = $this->parameters->get('developer_pictures_directory');
        } else if ($role === "ROLE_CLIENT") {
            $uploadsDirectory = $this->parameters->get('client_pictures_directory');
        } else if ($role === "SERVICE") {
            $uploadsDirectory = $this->parameters->get('service_pictures_directory');
        }
        
        $filePath = $uploadsDirectory . '/' . $filename;

        $filesystem = new Filesystem();
        // Vérifie si le fichier existe
        if (!$filesystem->exists($filePath)) {
            // Retourne une réponse d'erreur si le fichier n'existe pas
            return new JsonResponse(['error' => 'Image not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            // Supprime le fichier
            $filesystem->remove($filePath);
        } catch (\Exception $e) {
            // Retourne une réponse d'erreur si la suppression du fichier échoue
            return new JsonResponse(['error' => 'Failed to delete image'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // // Retourne un message de succès
        return new JsonResponse(['message' => 'Image deleted successfully'], Response::HTTP_OK);
    }

    #[Route('/api/imageUrl/{filename}/{usertype}', name: 'generate_image_url', methods: ['GET'])]
    public function generateImageUrl(string $filename, String $usertype): JsonResponse
    {
        $this->logger->info('filename: ' . $filename);
        // Récupère le répertoire de téléchargement à partir des paramètres et ( services.yaml )
        // par défaut si usertype n'est pas connu
        $uploadsDirectory = $this->parameters->get('pictures_directory');

        if ($usertype === "ROLE_DEVELOPER") {
            $uploadsDirectory = $this->parameters->get('developer_pictures_directory');
        } else if ($usertype === "ROLE_CLIENT") {
            $uploadsDirectory = $this->parameters->get('client_pictures_directory');
        } else if ($usertype === "SERVICE") {
            $uploadsDirectory = $this->parameters->get('service_pictures_directory');
        }

        $filePath = $uploadsDirectory . '/' . $filename;
        $this->logger->info('filePath: ' . $filePath);
        // Vérifie si le fichier existe
        if (!file_exists($filePath)) {
            // Retourne une réponse d'erreur si le fichier n'existe pas
            return new JsonResponse(['error' => $filePath], Response::HTTP_NOT_FOUND);
        }
        // Retourne le chemin relatif à partir du répertoire public
        $relativeUrl = str_replace($this->getParameter('kernel.project_dir') . '/public', '', $filePath);

        // Retourne l'URL dans la réponse
        return new JsonResponse(['url' => $relativeUrl], Response::HTTP_OK);
    }

    // #[Route('/product/image/{id}', name: 'product_image')]
    // public function productImage(int $id, ServiceItemRepository $serviceItemRepository): Response
    // {
    //     $product = $serviceItemRepository->find($id);

    //     if (!$product) {
    //         throw $this->createNotFoundException('Product not found');
    //     }

    //     $imagePath = $this->getParameter('service_pictures_directory') . '/' . $product->getPicture();

    //     if (!file_exists($imagePath)) {
    //         throw $this->createNotFoundException('Image not found');
    //     }

    //     $image = file_get_contents($imagePath);

    //     return new Response($image, 200, [
    //         'Content-Type' => mime_content_type($imagePath),
    //         'Content-Disposition' => 'inline; filename="'.$product->getPicture().'"'
    //     ]);
    // }
}
