<?php

namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploadService
{
    private $targetDirectory;
    private $slugger;

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, string $subdirectory = '', int $maxWidth = 1200, int $maxHeight = 1200): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.webp';

        // Créez le sous-répertoire s'il n'existe pas
        $targetPath = $this->getTargetDirectory() . '/' . $subdirectory;
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        try {
            // Sauvegarde temporaire du fichier uploadé
            $tempFilePath = $file->getPathname();

            // Conversion en WebP avec redimensionnement
            $imagine = new Imagine();
            $image = $imagine->open($tempFilePath);

            // Redimensionner l'image si nécessaire
            $size = $image->getSize();

            if ($size->getWidth() > $maxWidth || $size->getHeight() > $maxHeight) {
                $ratio = min($maxWidth / $size->getWidth(), $maxHeight / $size->getHeight());
                $newWidth = (int) ($size->getWidth() * $ratio);
                $newHeight = (int) ($size->getHeight() * $ratio);

                $image->resize(new Box($newWidth, $newHeight));
            }

            // Enregistrement en WebP
            $image->save($targetPath . '/' . $fileName, [
                'webp_quality' => 80
            ]);

        } catch (FileException $e) {
            throw new \Exception('Une erreur est survenue lors de l\'upload du fichier: ' . $e->getMessage());
        }

        return $subdirectory . '/' . $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}

