<?php 

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageUploaderInterface
{
    public function uploadImage(UploadedFile $file, $user): void;
}