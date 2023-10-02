<?php

namespace App\Controller;

use App\Entity\Administrateur;
use App\Entity\Theatre;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\AdminFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $theatres = $entityManager->getRepository(Theatre::class)->findAll();

        // Initialiser un tableau pour stocker les nombres d'entités "ouvreur" pour chaque théâtre
        $nombreOuvreursParTheatre = [];

        foreach ($theatres as $theatre) {
            // Utiliser la relation entre Theatre et Ouvreur pour compter les ouvreurs pour ce théâtre
            $nombreOuvreurs = count($theatre->getOuvreurs());
            
            // Stocker le nombre d'ouvreurs dans le tableau associatif avec le nom du théâtre en tant que clé
            $nombreOuvreursParTheatre[$theatre->getNom()] = $nombreOuvreurs;
        }

        return $this->render('admin/index.html.twig', [
            'theatres' => $theatres,
            'nombreOuvreursParTheatre' => $nombreOuvreursParTheatre,
            'controller_name' => 'AdminController'
        ]);
    }

    #[Route('/admin/addAdmin', name: 'app_add_admin')]
    public function addAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $administrateur = new Administrateur();

        $addAdminform = $this->createForm(AdminFormType::class, $administrateur);

        $addAdminform->handleRequest($request);
        if ($addAdminform->isSubmitted() && $addAdminform->isValid()) {
            $administrateur->setRoles(['ROLE_ADMIN']);
            $administrateur->setPassword(
                $userPasswordHasher->hashPassword(
                    $administrateur,
                    $addAdminform->get('password')->getData()
                )
            );
            $entityManager->persist($administrateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/addAdmin.html.twig', [
            'addAdminform' => $addAdminform->createView()
        ]);
    }

    #[Route('/admin/deleteAdmin/{id}', name: 'app_delete_admin')]
    public function deleteAdmin($id, EntityManagerInterface $entityManager): Response
    {
        $adminRepository = $entityManager->getRepository(Administrateur::class);
        $adminToDelete = $adminRepository->find($id);

        if (!$adminToDelete) {
            // Gérer le cas où l'administrateur n'est pas trouvé, par exemple, rediriger avec un message d'erreur.
        }

        $entityManager->remove($adminToDelete);
        $entityManager->flush();

        // Rediriger vers une page, peut-être la liste des administrateurs
        return $this->redirectToRoute('app_admin');
    }
}
