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
        // Debug: Sauvegarder l'image reçue
        $debugDir = sys_get_temp_dir().'/face_debug/';
        if (!file_exists($debugDir)) {
            mkdir($debugDir, 0777, true);
        }

        $receivedImagePath = $debugDir.'received_'.time().'.jpg';
        file_put_contents($receivedImagePath, base64_decode(explode(',', $faceData)[1]));
        error_log("Image reçue sauvegardée: ".$receivedImagePath);

        // Récupérer les utilisateurs avec face_data
        $users = $this->entityManager->getRepository(Utilisateur::class)
            ->createQueryBuilder('u')
            ->where('u.faceData IS NOT NULL')
            ->getQuery()
            ->getResult();

        error_log("Utilisateurs avec face_data trouvés: ".count($users));

        $matches = [];
        foreach ($users as $user) {
            try {
                // Debug: Sauvegarder l'image stockée
                $storedImagePath = $debugDir.'stored_user_'.$user->getId().'.jpg';
                file_put_contents($storedImagePath, base64_decode(explode(',', $user->getFaceData())[1]));

                $similarity = $this->compareFaces($user->getFaceData(), $faceData);
                error_log("Comparaison avec user ".$user->getId()." - Similarité: ".$similarity);

                if ($similarity > 0.7) { // Seuil ajustable
                    $matches[] = $user;
                }
            } catch (\Exception $e) {
                error_log("Erreur comparaison pour user ".$user->getId().": ".$e->getMessage());
                continue;
            }
        }

        // Mode développement: retourne le premier utilisateur si aucun match
        if (empty($matches) && $_ENV['APP_ENV'] === 'dev') {
            error_log("Mode dev: retourne le premier utilisateur trouvé");
            return $users ? [reset($users)] : [];
        }

        return $matches;
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
        
        // Conservation ratio
        $srcWidth = imagesx($image);
        $srcHeight = imagesy($image);
        $ratio = min($width/$srcWidth, $height/$srcHeight);
        $newWidth = (int)($srcWidth * $ratio);
        $newHeight = (int)($srcHeight * $ratio);
        
        // Centrage
        $x = (int)(($width - $newWidth) / 2);
        $y = (int)(($height - $newHeight) / 2);
        
        imagecopyresampled($normalized, $image, $x, $y, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
        
        // Amélioration du contraste
        imagefilter($normalized, IMG_FILTER_GRAYSCALE);
        imagefilter($normalized, IMG_FILTER_CONTRAST, -20);
        imagefilter($normalized, IMG_FILTER_BRIGHTNESS, 10);
        
        imagedestroy($image);
        
        return $normalized;
    }

    private function advancedImageComparison($image1, $image2): float
    {
        // 1. Similarité des histogrammes (couleurs)
        $hist1 = $this->computeHistogram($image1);
        $hist2 = $this->computeHistogram($image2);
        $histSim = 1 - $this->histogramDistance($hist1, $hist2);
        
        // 2. Hachage perceptuel (structure)
        $hash1 = $this->perceptualHash($image1);
        $hash2 = $this->perceptualHash($image2);
        $hashSim = $this->hammingSimilarity($hash1, $hash2);
        
        // 3. Combinaison pondérée
        return ($hashSim * 0.6) + ($histSim * 0.4);
    }

    private function computeHistogram($image): array
    {
        $histogram = array_fill(0, 256, 0);
        $width = imagesx($image);
        $height = imagesy($image);
        
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $gray = imagecolorat($image, $x, $y) & 0xFF;
                $histogram[$gray]++;
            }
        }
        
        // Normalisation
        $total = $width * $height;
        return array_map(fn($v) => $v / $total, $histogram);
    }

    private function histogramDistance(array $h1, array $h2): float
    {
        $distance = 0.0;
        foreach ($h1 as $i => $v1) {
            $distance += abs($v1 - $h2[$i]);
        }
        return $distance / count($h1);
    }

    private function perceptualHash($image): string
    {
        // Réduction à 16x16
        $small = imagecreatetruecolor(16, 16);
        imagecopyresampled($small, $image, 0, 0, 0, 0, 16, 16, imagesx($image), imagesy($image));
        
        // Calcul de la moyenne
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
        
        // Génération du hash
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

    public function debugComparison(int $userId1, int $userId2): array
    {
        $user1 = $this->entityManager->find(Utilisateur::class, $userId1);
        $user2 = $this->entityManager->find(Utilisateur::class, $userId2);

        if (!$user1 || !$user2) {
            throw new \InvalidArgumentException('Utilisateurs non trouvés');
        }

        return [
            'similarity' => $this->compareFaces($user1->getFaceData(), $user2->getFaceData()),
            'user1_image' => base64_decode(explode(',', $user1->getFaceData())[1]),
            'user2_image' => base64_decode(explode(',', $user2->getFaceData())[1])
        ];
    }
}