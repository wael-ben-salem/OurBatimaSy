<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\CompleteProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompleteProfileController extends AbstractController
{
    #[Route('/complete-profile', name: 'app_complete_profile')]
    public function index(
        Request $request,
        Security $security,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var Utilisateur $user */
        $user = $security->getUser();

        // Récupération des données Google si disponibles
        $googleUser = $request->getSession()->get('google_user');
        if ($googleUser) {
            $user->setNom($googleUser['nom'] ?? '');
            $user->setPrenom($googleUser['prenom'] ?? '');
            $user->setEmail($googleUser['email'] ?? '');
        }

        if ($user->getAdresse() && $user->getTelephone()) {
            return $this->redirectToRoute('app_welcomeFront');
        }

        $form = $this->createForm(CompleteProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Nettoyage du numéro de téléphone
            $phone = preg_replace('/[^0-9+]/', '', $form->get('telephone')->getData());
            $user->setTelephone($phone);

            // Encodage du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRole('Client');
            $user->setStatut('en_attente');

            $em->persist($user);
            $em->flush();

            // Redirection vers la page de confirmation avec la carte
            return $this->redirectToRoute('app_confirm_location', ['id' => $user->getId()]);
        }

        return $this->render('registration/complete_profile.html.twig', [
            'form' => $form->createView(),
            'googleUser' => $googleUser
        ]);
    }

    #[Route('/confirm-location/{id}', name: 'app_confirm_location')]
    public function confirmLocation(Utilisateur $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $user->setAdresse($request->request->get('adresse'));
            $user->setLatitude($request->request->get('latitude'));
            $user->setLongitude($request->request->get('longitude'));
            
            $em->flush();
            
            return $this->redirectToRoute('app_welcomeFront');
        }

        return $this->render('registration/confirm_location.html.twig', [
            'user' => $user
        ]);
    }
    #[Route('/reverse-geocode', name: 'app_reverse_geocode')]
public function reverseGeocode(Request $request): Response
{
    $lat = $request->query->get('lat');
    $lng = $request->query->get('lng');
    
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng";
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    return $this->json([
        'address' => $data['display_name'] ?? ''
    ]);
}

#[Route('/search-address', name: 'app_search_address')]
public function searchAddress(Request $request): Response
{
    $query = $request->query->get('q');
    
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($query);
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    return $this->json($data);
}
}