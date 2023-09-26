<?php

namespace App\Controller;

use App\Entity\Theatre;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use App\Entity\Utilisateur;
use App\Repository\OuvreurRepository;
use App\Form\TheatreFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TheatreController extends AbstractController
{
    #[Route('/theatre', name: 'app_theatre')]
    public function index(OuvreurRepository $ouvreurRepository): Response
    {
        $user = $this->getUser();

        if ($user instanceof Utilisateur) {
            $id = $user->getId();
        } 

        $ouvreurs = $ouvreurRepository->findByTheatreId($id);

        return $this->render('theatre/index.html.twig', [
            'ouvreurs' => $ouvreurs,
            'controller_name' => 'TheatreController'
        ]);
    }
    
    #[Route('/theatre/viewtheatre/{id}', name: 'app_view_theatre')]
    public function view(Theatre $theatre, $id, OuvreurRepository $ouvreurRepository): Response
    {

        $ouvreurs = $ouvreurRepository->findByTheatreId($id);

        return $this->render('theatre/viewtheatre.html.twig', [
            'theatre' => $theatre,
            'ouvreurs' => $ouvreurs
        ]);
    }

    #[Route('/admin/addTheatre', name: 'app_add_theatre')]
    public function addTheatre(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $theatre = new Theatre();

        $addTheatreform = $this->createForm(TheatreFormType::class, $theatre);

        $addTheatreform->handleRequest($request);
        if ($addTheatreform->isSubmitted() && $addTheatreform->isValid()) {

            Stripe::setApiKey($stripeSK);

            $account = Account::create([
                'type' => 'standard', 
                'country' => 'FR', 
                'email' => 'lolacarder@gmail.com', 
            ]);

            $theatre->setStripeAccountId($account->id);
            $theatre->setRoles(['ROLE_MODERATOR']);
            $theatre->setPassword(
                $userPasswordHasher->hashPassword(
                    $theatre,
                    $addTheatreform->get('password')->getData()
                )
            );
            $entityManager->persist($theatre);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/addTheatre.html.twig', [
            'addTheatreform' => $addTheatreform->createView()
        ]);
    }

    #[Route('create-stripe-account-link', name: 'app_create_account_stripe_link')]
    public function createAccountLink($stripeSK): Response
    {
        Stripe::setApiKey($stripeSK);

        $stripeAccountId = $this->getUser()->getStripeAccountId();

        $accountLink = AccountLink::create([
            'account' => $stripeAccountId,
            'refresh_url' => $this->generateUrl('app_view_theatre', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'return_url' => $this->generateUrl('app_view_theatre', [], UrlGeneratorInterface::ABSOLUTE_URL),            
            'type' => 'account_onboarding',
        ]);

        return $this->redirect($accountLink->url);
    }


    #[Route('/admin/deleteTheatre/{id}', name: 'app_delete_theatre')]
    public function deleteTheatre($id, EntityManagerInterface $entityManager): Response
    {
        $theatreRepository = $entityManager->getRepository(Theatre::class);
        $theatreToDelete = $theatreRepository->find($id);

        if (!$theatreToDelete) {
            // Gérer le cas où le théâtre n'est pas trouvé, par exemple, rediriger avec un message d'erreur.
        }

        $entityManager->remove($theatreToDelete);
        $entityManager->flush();

        // Rediriger vers une page, peut-être la liste des théâtres
        return $this->redirectToRoute('app_admin');
    }

}
