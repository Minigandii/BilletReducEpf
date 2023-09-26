<?php

namespace App\Controller;

use App\Entity\Ouvreur;
use App\Form\OuvreurFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OuvreurController extends AbstractController
{
    #[Route('/ouvreur', name: 'app_ouvreur')]
    public function index(): Response
    {
        return $this->render('ouvreur/index.html.twig', [
            'controller_name' => 'OuvreurController',
        ]);
    }

    #[Route('/theatre/addOuvreur', name: 'app_add_ouvreur')]
    public function addOuvreur(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $ouvreur = new Ouvreur();

        $addOuvreurform = $this->createForm(OuvreurFormType::class, $ouvreur);

        $addOuvreurform->handleRequest($request);
        if ($addOuvreurform->isSubmitted() && $addOuvreurform->isValid()) {
            $ouvreur->setRoles([]);
            $ouvreur->setPassword(
                $userPasswordHasher->hashPassword(
                    $ouvreur,
                    $addOuvreurform->get('password')->getData()
                )
            );
            $entityManager->persist($ouvreur);
            $entityManager->flush();

            return $this->redirectToRoute('app_theatre');
        }

        return $this->render('theatre/addOuvreur.html.twig', [
            'addOuvreurform' => $addOuvreurform->createView()
        ]);
    }

    #[Route('/theatre/viewouvreur/{id}', name: 'app_view_ouvreur')]
    public function view(Ouvreur $ouvreur): Response
    {
        return $this->render('ouvreur/viewouvreur.html.twig', [
            'ouvreur' => $ouvreur
        ]);
    }

    #[Route('/theatre/deleteOuvreur/{id}', name: 'app_delete_ouvreur')]
    public function deleteTheatre($id, EntityManagerInterface $entityManager): Response
    {
        $ouvreurRepository = $entityManager->getRepository(Ouvreur::class);
        $ouvreurToDelete = $ouvreurRepository->find($id);

        if (!$ouvreurToDelete) {
            // Gérer le cas où le théâtre n'est pas trouvé, par exemple, rediriger avec un message d'erreur.
        }

        $entityManager->remove($ouvreurToDelete);
        $entityManager->flush();

        // Rediriger vers une page, peut-être la liste des théâtres
        return $this->redirectToRoute('app_theatre');
    }
}
