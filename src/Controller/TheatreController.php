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
use Symfony\Component\HttpClient\HttpClient;


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
            'theatre' => $user,
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
    public function addTheatre($stripeSK,Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $theatre = new Theatre();

        $httpClient = HttpClient::create();
        $responseData = $httpClient->request('POST', 'https://api.billetreduc.com/api/auth/login', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'userName' => 'epfprojects@billetreduc.fr', 
                'password' => 'LtlcHsFhWkOa7aZbDLOU',
            ]),
        ]);
        $tokenData = $responseData->toArray();
        $token = $tokenData['auth_token']; 

        $responseTheatres = $httpClient->request('GET', 'https://api.billetreduc.com/api/Export/theaters', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $theatresData = $responseTheatres->toArray();

        $theatreOptions = [];
        foreach ($theatresData as $th) {
            foreach ($th as $t){
                $theatreOptions[$t['id']] = $t['name'] . '*' . $t['address'] . '*' .$t['postalCode'] . '*' .$t['city'].'*'.$t['id']; 
            }
        }
        
        $addTheatreform = $this->createForm(TheatreFormType::class, $theatre, [
            'theatres' => ($theatreOptions),
        ]);


       // $addTheatreform = $this->createForm(TheatreFormType::class, $theatre);

        $addTheatreform->handleRequest($request);
        if ($addTheatreform->isSubmitted() && $addTheatreform->isValid()) {

            $theatre->setRoles(['ROLE_MODERATOR']);
            $theatre->setPassword(
                $userPasswordHasher->hashPassword(
                    $theatre,
                    $addTheatreform->get('password')->getData()
                )
            );


            $theatreInfo = $addTheatreform->get('BRId')->getData();
            $theatreChaine = explode('*', $theatreInfo); //chaine divisée en éléments

            $theatreBRId =$theatreChaine[4]; //dernier element : id

            $theatreName = $theatreChaine[0]; //premier element : nom 

            $theatreAddress = $theatreChaine[1].','.$theatreChaine[2].','.$theatreChaine[3];
            
            $theatre->setNom($theatreName);
            $theatre->setAdresse($theatreAddress);
            $theatre->setBRId($theatreBRId);
            $theatre->setStripeAccountId('start'); //oblige de mettre un truc pour creer le theatre, et on lui cree son vrai juste apres

            $entityManager->persist($theatre);
            $entityManager->flush();

           /* $email = $theatre->getEmail();

            Stripe::setApiKey($stripeSK);

            $account = Account::create([
                'type' => 'standard', 
                'country' => 'FR', 
                'email' => $email, 
            ]);

            $theatre->setStripeAccountId($account->id);
            $entityManager->flush();*/

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
            'refresh_url' => $this->generateUrl('app_theatre', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'return_url' => $this->generateUrl('app_theatre', [], UrlGeneratorInterface::ABSOLUTE_URL),            
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
