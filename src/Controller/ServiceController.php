<?php
// src/Controller/ServiceController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/services', name: 'services')]
    public function services(): Response
    {
        $services = [
            ['name' => 'Construction de Bâtiments', 'description' => 'Nous réalisons des constructions neuves sur mesure, en respectant les normes de qualité et les délais convenus.'],
            ['name' => 'Rénovation de Maisons', 'description' => 'Transformation complète ou partielle de votre habitat avec des matériaux haut de gamme et un savoir-faire exceptionnel.'],
            ['name' => 'Conception Architecturale', 'description' => 'Création de plans personnalisés adaptés à vos besoins et aux spécificités de votre terrain.'],
            ['name' => 'Design Intérieur', 'description' => 'Optimisation de vos espaces de vie avec des solutions esthétiques et fonctionnelles.'],
            ['name' => 'Réparation et Maintenance', 'description' => 'Service après-vente et entretien régulier pour préserver la qualité de votre construction.'],
            ['name' => 'Peinture et Finitions', 'description' => 'Application de peintures écologiques et finitions soignées pour un rendu parfait.']
        ];
        
        $faqs = [
            ['question' => 'Quelle est votre zone d\'intervention?', 'answer' => 'Nous intervenons dans toute la région avec une équipe mobile disponible.'],
            ['question' => 'Quels sont vos délais de réalisation?', 'answer' => 'Nos délais varient selon le projet, avec un engagement contractuel.'],
            ['question' => 'Proposez-vous des garanties?', 'answer' => 'Tous nos travaux bénéficient d\'une garantie décennale.'],
            ['question' => 'Pouvez-vous fournir des références?', 'answer' => 'Nous mettons à disposition un portfolio de nos réalisations.'],
            ['question' => 'Travaillez-vous avec des matériaux écologiques?', 'answer' => 'Oui, nous privilégions les matériaux durables et respectueux de l\'environnement.'],
            ['question' => 'Comment obtenir un devis?', 'answer' => 'Contactez-nous pour une étude gratuite de votre projet.'],
            ['question' => 'Acceptez-vous les petits travaux?', 'answer' => 'Oui, nous adaptons notre intervention à l\'ampleur de votre projet.'],
            ['question' => 'Quelle est votre expérience?', 'answer' => 'Notre équipe cumule plus de 25 ans d\'expérience dans le secteur.'],
            ['question' => 'Proposez-vous des financements?', 'answer' => 'Nous travaillons avec des partenaires bancaires pour faciliter votre projet.'],
            ['question' => 'Comment se déroule le suivi de chantier?', 'answer' => 'Un chef de projet dédié vous tient informé à chaque étape.']
        ];

        return $this->render('partials/service.html.twig', [
            'services' => $services,
            'faqs' => $faqs
        ]);
    }
}