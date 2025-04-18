<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_telephone', [$this, 'formatTelephone']),
            new TwigFilter('ago', [$this, 'timeAgo']),

        ];
        
    }

    public function formatTelephone($number)
    {
        // Supprime tous les caractères non numériques
        $cleaned = preg_replace('/[^0-9]/', '', $number);
        
        // Format standard français
        if (strlen($cleaned) === 10) {
            return chunk_split($cleaned, 2, ' ');
        }

        return $number; // Retourne le numéro original si le format ne correspond pas
    }
    public function timeAgo(\DateTimeInterface $date)
    {
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->y > 0) return "Il y a {$diff->y} an" . ($diff->y > 1 ? 's' : '');
        if ($diff->m > 0) return "Il y a {$diff->m} mois";
        if ($diff->d > 0) return "Il y a {$diff->d} jour" . ($diff->d > 1 ? 's' : '');
        if ($diff->h > 0) return "Il y a {$diff->h} heure" . ($diff->h > 1 ? 's' : '');
        return "À l'instant";
    }
}