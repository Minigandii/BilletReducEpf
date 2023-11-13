<?php

namespace App\Controller;

use App\Entity\Ouvreur;
use App\Entity\Utilisateur;
use App\Form\OuvreurFormType;
use App\Form\EditOuvreurFormType;
use App\Form\OuvreurEditFormType;
use App\Repository\OuvreurRepository;
use App\Repository\TheatreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class OuvreurController extends AbstractController
{
    #[Route('/ouvreur', name: 'app_ouvreur')]
    public function index(OuvreurRepository $ouvreurRepository): Response
    {

        $user = $this->getUser();

        if ($user instanceof Utilisateur) {
            $id = $user->getId();
            $ouvreur = $ouvreurRepository->getOuvreurById($id);
            $theatre = $ouvreur->getTheatre();
        } 
        else{

        }
        
        return $this->render('ouvreur/index.html.twig', [
            'ouvreur' => $ouvreur,
            'theatre' => $theatre,

        ]);
    }

    #[Route('/theatre/editOuvreur/{id}', name: 'app_theatre_edit_ouvreur')]
    public function theatreEditOuvreur(Request $request, EntityManagerInterface $entityManager,  Ouvreur $ouvreur): Response
    {

        $editOuvreurForm = $this->createForm(EditOuvreurFormType::class, $ouvreur);

        $editOuvreurForm->handleRequest($request);
        $theatre = $ouvreur->getTheatre();
        if ($editOuvreurForm->isSubmitted() && $editOuvreurForm->isValid()) {

            $entityManager->persist($ouvreur);
            $entityManager->flush();
            

            return $this->redirectToRoute('app_theatre');
        }

        return $this->render('theatre/editOuvreur.html.twig', [
            'editOuvreurForm' => $editOuvreurForm->createView(),
            'ouvreur' => $ouvreur,
            'theatre'=> $theatre
        ]);
    }

    #[Route('/ouvreur/editOuvreur/{id}', name: 'app_edit_ouvreur')]
    public function editOuvreur(Request $request, EntityManagerInterface $entityManager, Ouvreur $ouvreur): Response
    {

        $editOuvreurForm = $this->createForm(OuvreurEditFormType::class, $ouvreur);

        $editOuvreurForm->handleRequest($request);
        $theatre = $ouvreur->getTheatre();
        if ($editOuvreurForm->isSubmitted() && $editOuvreurForm->isValid()) {

            $entityManager->persist($ouvreur);
            $entityManager->flush();

            return $this->redirectToRoute('app_ouvreur');
        }

        return $this->render('ouvreur/editOuvreur.html.twig', [
            'editOuvreurForm' => $editOuvreurForm->createView(),
            'ouvreur' => $ouvreur,
            'theatre'=> $theatre
        ]);
    }

    #[Route('/theatre/addOuvreur', name: 'app_add_ouvreur')]
    public function addOuvreur(TheatreRepository $theatreRepository, OuvreurRepository $ouvreurRepository,Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher ): Response
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

            $user = $this->getUser();

            if ($user instanceof Utilisateur) {
                $id = $user->getId();
                $theatre = $theatreRepository->getTheatreById($id);
            } 
            else{
            }

            $ouvreur->setTheatre($theatre);
            $entityManager->persist($ouvreur);
            $entityManager->flush();
            
            

    

            return $this->redirectToRoute('app_theatre');
        }

        return $this->render('theatre/addOuvreur.html.twig', [
            'addOuvreurform' => $addOuvreurform->createView(),
          
           
            
           
        ]);
    }

    #[Route('/theatre/viewouvreur/{id}', name: 'app_view_ouvreur')]
    public function view(Ouvreur $ouvreur): Response
    {

        $theatre = $ouvreur->getTheatre();

        return $this->render('ouvreur/viewouvreur.html.twig', [
            'ouvreur' => $ouvreur,
            'theatre' => $theatre
        ]);
    }

    #[Route('/admin/viewouvreur/{id}', name: 'app_view_ouvreur_admin')]
    public function viewadmin(Ouvreur $ouvreur): Response
    {

        $theatre = $ouvreur->getTheatre();

        return $this->render('ouvreur/viewouvreuradmin.html.twig', [
            'ouvreur' => $ouvreur,
            'theatre' => $theatre
        ]);
    }

    #[Route('/theatre/deleteOuvreur/{id}', name: 'app_delete_ouvreur')]
    public function deleteOuvreur($id, EntityManagerInterface $entityManager): Response
    {
        $ouvreurRepository = $entityManager->getRepository(Ouvreur::class);
        $ouvreurToDelete = $ouvreurRepository->find($id);
        $theatre=$ouvreurToDelete->getTheatre();

        if (!$ouvreurToDelete) {
            // Gérer le cas où le théâtre n'est pas trouvé, par exemple, rediriger avec un message d'erreur.
        }

        $entityManager->remove($ouvreurToDelete);
        $entityManager->flush();

        // Rediriger vers une page, peut-être la liste des théâtres
        return $this->redirectToRoute('app_theatre');

        return $this->render('/theatre/comfirm_delete_ouvreur.html.twig', [
            'ouvreur' => $ouvreurToDelete,
            'theatre'=>$theatre
        ]);
        
    }

    #[Route('/admin/deleteOuvreur/{id}', name: 'app_delete_ouvreur_admin')]
    public function deleteOuvreurAdmin($id, EntityManagerInterface $entityManager): Response
    {
        $ouvreurRepository = $entityManager->getRepository(Ouvreur::class);
        $ouvreurToDelete = $ouvreurRepository->find($id);
        $theatre=$ouvreurToDelete->getTheatre();

        if (!$ouvreurToDelete) {
            // Gérer le cas où le théâtre n'est pas trouvé, par exemple, rediriger avec un message d'erreur.
        }

        $entityManager->remove($ouvreurToDelete);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin');

        return $this->render('/admin/comfirm_delete_ouvreur.html.twig', [
            'ouvreur' => $ouvreurToDelete,
            'theatre'=>$theatre
        ]);
    }

    #[Route("/theatre/confirm_delete_ouvreur/{id}", name: 'app_confirm_delete_ouvreur')]

    public function confirmDeleteOuvreur($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $ouvreurRepository = $entityManager->getRepository(Ouvreur::class);
        $ouvreurToDelete = $ouvreurRepository->find($id);
        $theatre=$ouvreurToDelete->getTheatre();

        if (!$ouvreurToDelete) {
            return $this->redirectToRoute('app_theatre');
        }

        return $this->render('/theatre/comfirm_delete_ouvreur.html.twig', [
            'ouvreur' => $ouvreurToDelete,
            'theatre'=>$theatre
        ]);
    }

    #[Route("/admin/confirm_delete_ouvreur/{id}", name: 'app_confirm_delete_ouvreur_admin')]

    public function confirmDeleteOuvreurAdmin($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $ouvreurRepository = $entityManager->getRepository(Ouvreur::class);
        $ouvreurToDelete = $ouvreurRepository->find($id);
        $theatre=$ouvreurToDelete->getTheatre();

        if (!$ouvreurToDelete) {
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('/admin/comfirm_delete_ouvreur.html.twig', [
            'ouvreur' => $ouvreurToDelete,
            'theatre'=>$theatre
        ]);
    }

    #[Route('/ouvreur/viewQr', name: 'app_ouvreur_view_qr')]
    public function viewQr(Ouvreur $ouvreur): Response
    {

        $theatre = $ouvreur->getTheatre();
        $qrpath = $theatre->getQrcode();
        
        return $this->render('ouvreur/qrcodeOuvreur.html.twig', [
            'qrpath' => $qrpath,
            'ouvreur' => $ouvreur,
        ]);
    }
}
