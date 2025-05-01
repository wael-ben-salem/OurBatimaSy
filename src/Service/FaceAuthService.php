<?php
// src/Service/FaceAuthService.php

namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FaceAuthService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findMatchingUsers(string $faceData): array
    {
        // 1. Validation de base
        if (empty($faceData)) {
            throw new \InvalidArgumentException('Aucune donnée faciale reçue');
        }

        // 2. Décodage de l'image
        $imageContent = $this->extractImageData($faceData);
        $image = @imagecreatefromstring($imageContent);
        
        if ($image === false) {
            throw new \RuntimeException('Image invalide');
        }

        // 3. Détection de visage basique
        if (!$this->containsValidFace($image)) {
            imagedestroy($image);
            return [];
        }

        // 4. Recherche des utilisateurs
        $users = $this->entityManager->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->where('u.faceData IS NOT NULL')
            ->getQuery()
            ->getResult();

        $matches = [];
        foreach ($users as $user) {
            try {
                $similarity = $this->compareFaces($user->getFaceData(), $faceData);
                if ($similarity > 0.74) { // Seuil plus strict
                    $matches[] = $user;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        imagedestroy($image);
        return $matches;
    }

    private function containsValidFace($image): bool
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Analyse des bords pour détecter une image uniforme
        $borderPixels = [];
        for ($x = 0; $x < $width; $x += 10) {
            $borderPixels[] = imagecolorat($image, $x, 0) & 0xFF;
            $borderPixels[] = imagecolorat($image, $x, $height-1) & 0xFF;
        }
        for ($y = 0; $y < $height; $y += 10) {
            $borderPixels[] = imagecolorat($image, 0, $y) & 0xFF;
            $borderPixels[] = imagecolorat($image, $width-1, $y) & 0xFF;
        }

        $variance = $this->calculateVariance($borderPixels);
        return $variance > 25;
    }

    private function extractImageData(string $faceData): string
    {
        $parts = explode(',', $faceData);
        if (count($parts) < 2) {
            throw new \InvalidArgumentException('Format d\'image invalide');
        }
        
        $decoded = base64_decode($parts[1]);
        if (empty($decoded)) {
            throw new \RuntimeException('Échec du décodage de l\'image');
        }
        
        return $decoded;
    }

    private function calculateVariance(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $sum = 0;
        foreach ($values as $value) {
            $sum += pow($value - $mean, 2);
        }
        return sqrt($sum / count($values));
    }

    private function compareFaces(?string $storedFace, string $capturedFace): float
    {
        if (!$storedFace) {
            return 0.0;
        }

        try {
            $storedImage = $this->prepareImageForComparison($storedFace);
            $capturedImage = $this->prepareImageForComparison($capturedFace);

            return $this->advancedImageComparison($storedImage, $capturedImage);
        } finally {
            if (isset($storedImage)) imagedestroy($storedImage);
            if (isset($capturedImage)) imagedestroy($capturedImage);
        }
    }

    private function prepareImageForComparison(string $imageData)
    {
        $imageString = base64_decode(explode(',', $imageData)[1] ?? $imageData);
        $image = @imagecreatefromstring($imageString);
        
        if ($image === false) {
            throw new \RuntimeException("Impossible de créer l'image à partir des données");
        }

        // Normalisation de la taille
        $width = 200;
        $height = 200;
        $normalized = imagecreatetruecolor($width, $height);
        
        imagecopyresampled($normalized, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
        
        // Amélioration du contraste
        imagefilter($normalized, IMG_FILTER_GRAYSCALE);
        imagefilter($normalized, IMG_FILTER_CONTRAST, -20);
        imagefilter($normalized, IMG_FILTER_BRIGHTNESS, 10);
        
        imagedestroy($image);
        
        return $normalized;
    }

    private function advancedImageComparison($image1, $image2): float
    {
        $hash1 = $this->perceptualHash($image1);
        $hash2 = $this->perceptualHash($image2);
        return $this->hammingSimilarity($hash1, $hash2);
    }

    private function perceptualHash($image): string
    {
        $small = imagecreatetruecolor(16, 16);
        imagecopyresampled($small, $image, 0, 0, 0, 0, 16, 16, imagesx($image), imagesy($image));
        
        $sum = 0;
        $pixels = [];
        for ($y = 0; $y < 16; $y++) {
            for ($x = 0; $x < 16; $x++) {
                $gray = imagecolorat($small, $x, $y) & 0xFF;
                $sum += $gray;
                $pixels[] = $gray;
            }
        }
        $average = $sum / 256;
        
        $hash = '';
        foreach ($pixels as $pixel) {
            $hash .= ($pixel > $average) ? '1' : '0';
        }
        
        imagedestroy($small);
        return $hash;
    }

    private function hammingSimilarity(string $hash1, string $hash2): float
    {
        $distance = 0;
        $len = min(strlen($hash1), strlen($hash2));
        for ($i = 0; $i < $len; $i++) {
            if ($hash1[$i] !== $hash2[$i]) {
                $distance++;
            }
        }
        return 1 - ($distance / $len);
    }
}