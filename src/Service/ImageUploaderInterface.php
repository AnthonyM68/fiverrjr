<?php 

namespace App\Service;

use App\Entity\ServiceItem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageUploaderInterface
{
    public function uploadImage(UploadedFile $file, $data): void;
}