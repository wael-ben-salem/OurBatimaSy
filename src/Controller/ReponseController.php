<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\Reponse1Type;
use App\Repository\ReponseRepository;
use App\Repository\CustomReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

#[Route('/reponse')]
final class ReponseController extends AbstractController{
    #[Route(name: 'app_reponse_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Use a custom query to fetch all responses with their associated reclamations
        $conn = $entityManager->getConnection();
        $sql = 'SELECT r.*, rec.description as reclamation_description, rec.id as reclamation_id 
                FROM reponse r 
                LEFT JOIN reclamation rec ON r.id_Reclamation = rec.id';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $reponses = $resultSet->fetchAllAssociative();

        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses,
        ]);
    }

    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Only administrators can create responses')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get reclamations for the dropdown
        $conn = $entityManager->getConnection();
        $sql = 'SELECT id, description FROM reclamation';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $reclamations = $resultSet->fetchAllAssociative();

        // Create reclamation choices for the form
        $reclamationChoices = [];
        foreach ($reclamations as $reclamation) {
            $shortDesc = strlen($reclamation['description']) > 30 
                ? substr($reclamation['description'], 0, 30) . '...' 
                : $reclamation['description'];
            $reclamationChoices['#' . $reclamation['id'] . ' - ' . $shortDesc] = $reclamation['id'];
        }

        // Create a form without binding to an entity
        $form = $this->createFormBuilder()
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a description']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Your description should be at least {{ limit }} characters',
                        'max' => 1000,
                        'maxMessage' => 'Your description cannot be longer than {{ limit }} characters'
                    ])
                ],
                'attr' => ['rows' => 5]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'Pending',
                    'In Progress' => 'In Progress',
                    'Resolved' => 'Resolved',
                    'Closed' => 'Closed'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a status'])
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'constraints' => [
                    new NotNull(['message' => 'Please select a date'])
                ]
            ])
            ->add('id_Reclamation', ChoiceType::class, [
                'choices' => $reclamationChoices,
                'label' => 'Réclamation associée',
                'placeholder' => 'Choisir une réclamation',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une réclamation'])
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Insert the response using a direct SQL query
            $sql = 'INSERT INTO reponse (description, statut, date, id_Reclamation) VALUES (:description, :statut, :date, :id_Reclamation)';
            $stmt = $conn->prepare($sql);
            $stmt->executeStatement([
                'description' => $data['description'],
                'statut' => $data['statut'],
                'date' => $data['date']->format('Y-m-d H:i:s'),
                'id_Reclamation' => $data['id_Reclamation']
            ]);

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        // Create an empty response array for the template
        $reponse = [
            'id' => null,
            'description' => '',
            'statut' => '',
            'date' => ''
        ];

        return $this->render('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        // Use a custom query to fetch a single response with its associated reclamation
        $conn = $entityManager->getConnection();
        $sql = 'SELECT r.*, rec.description as reclamation_description, rec.id as reclamation_id 
                FROM reponse r 
                LEFT JOIN reclamation rec ON r.id_Reclamation = rec.id
                WHERE r.id = :id';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);
        $reponse = $resultSet->fetchAssociative();

        if (!$reponse) {
            throw $this->createNotFoundException('Response not found');
        }

        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Only administrators can edit responses')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        // Use a custom query to fetch a single response
        $conn = $entityManager->getConnection();
        $sql = 'SELECT * FROM reponse WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['id' => $id]);
        $reponse = $resultSet->fetchAssociative();

        if (!$reponse) {
            throw $this->createNotFoundException('Response not found');
        }

        // Get reclamations for the dropdown
        $sql = 'SELECT id, description FROM reclamation';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $reclamations = $resultSet->fetchAllAssociative();

        // Create reclamation choices for the form
        $reclamationChoices = [];
        foreach ($reclamations as $reclamation) {
            $shortDesc = strlen($reclamation['description']) > 30 
                ? substr($reclamation['description'], 0, 30) . '...' 
                : $reclamation['description'];
            $reclamationChoices['#' . $reclamation['id'] . ' - ' . $shortDesc] = $reclamation['id'];
        }

        // Create a form without binding to an entity
        $form = $this->createFormBuilder()
            ->add('description', TextareaType::class, [
                'data' => $reponse['description'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a description']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Your description should be at least {{ limit }} characters',
                        'max' => 1000,
                        'maxMessage' => 'Your description cannot be longer than {{ limit }} characters'
                    ])
                ],
                'attr' => ['rows' => 5]
            ])
            ->add('statut', ChoiceType::class, [
                'data' => $reponse['statut'],
                'choices' => [
                    'Pending' => 'Pending',
                    'In Progress' => 'In Progress',
                    'Resolved' => 'Resolved',
                    'Closed' => 'Closed'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a status'])
                ]
            ])
            ->add('date', DateType::class, [
                'data' => new \DateTime($reponse['date']),
                'widget' => 'single_text',
                'constraints' => [
                    new NotNull(['message' => 'Please select a date'])
                ]
            ])
            ->add('id_Reclamation', ChoiceType::class, [
                'data' => $reponse['id_Reclamation'],
                'choices' => $reclamationChoices,
                'label' => 'Réclamation associée',
                'placeholder' => 'Choisir une réclamation',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une réclamation'])
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Update the response using a direct SQL query
            $sql = 'UPDATE reponse SET description = :description, statut = :statut, date = :date, id_Reclamation = :id_Reclamation WHERE id = :id';
            $stmt = $conn->prepare($sql);
            // Format the date as a string
            $formattedDate = $data['date'] instanceof \DateTime ? $data['date']->format('Y-m-d H:i:s') : $data['date'];

            $stmt->executeStatement([
                'id' => $id,
                'description' => $data['description'],
                'statut' => $data['statut'],
                'date' => $formattedDate,
                'id_Reclamation' => $data['id_Reclamation']
            ]);

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Only administrators can delete responses')]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id, $request->getPayload()->getString('_token'))) {
            // Delete the response using a direct SQL query
            $conn = $entityManager->getConnection();
            $sql = 'DELETE FROM reponse WHERE id = :id';
            $stmt = $conn->prepare($sql);
            $stmt->executeStatement(['id' => $id]);
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }
}
