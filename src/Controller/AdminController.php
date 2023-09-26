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
        $theatres = $doctrine->getRepository(Theatre::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'theatres' => $theatres,
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
