<?php

// src/Controller/WelcomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig');
    }
    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        return $this->render('dash/base.html.twig');
    }

    #[Route('/welcome', name: 'app_welcome')]
    public function index(): Response
    {
        return $this->render('dash/dashboard.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    #[Route('/welcomeFront', name: 'app_welcomeFront')]
    public function indexFront(): Response
    {
        return $this->render('welcomeFront/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/service', name: 'service')]
    public function service(): Response
    {
        return $this->render('home/service.html.twig');
    }

    #[Route('/team', name: 'team')]
    public function team(): Response
    {
        return $this->render('home/team.html.twig');
    }

    #[Route('/portfolio', name: 'portfolio')]
    public function portfolio(): Response
    {
        return $this->render('home/portfolio.html.twig');
    }

    #[Route('/blog', name: 'blog')]
    public function blog(): Response
    {
        return $this->render('home/blog.html.twig');
    }

    #[Route('/single', name: 'single')]
    public function single(): Response
    {
        return $this->render('home/single.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}