<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Extensions\MyLydiaFacade;
use Pythagus\Lydia\Http\PaymentRequest;


use Pythagus\Lydia\Lydia;

class PaymentController extends AbstractController
{

    #[Route('/payment', name: 'payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/admin/addTheatre', name: 'app_add_theatre')]
    public function addTheatre(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $theatre = new Theatre();

        $addTheatreform = $this->createForm(TheatreFormType::class, $theatre);

        $addTheatreform->handleRequest($request);
        if ($addTheatreform->isSubmitted() && $addTheatreform->isValid()) {
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


    #[Route('/lydia-payment', name: 'lydia-payment')]
    public function lydiaPayment(Request $request): Response
    {

        $tipAmount = (float)$request->request->get('tip_amount', 0.0);

        // Create PaymentRequest instance

        Lydia::setInstance(new MyLydiaFacade());
        $lydiaPaymentRequest = new PaymentRequest();
        $lydiaPaymentRequest->setFinishCallback($this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL));

        // Prepare data for payment
        $paymentData = [
            'amount' => (int)($tipAmount), // Replace with the actual payment amount
            'recipient' => 'zaborovstella@gmail.com', // Replace with the recipient email or ID
            // Add any other necessary parameters here
        ];

        // Execute payment request
        $lydiaPaymentData = $lydiaPaymentRequest->execute($paymentData);

        // Save your data

        // Redirect to Lydia's payment page
        return $this->redirect($lydiaPaymentData['url'], 303);

    }

    #[Route('/checkout', name: 'checkout')]
    public function checkout($stripeSK, Request $request): Response
    {
        Stripe::setApiKey($stripeSK);

        $tipAmount = (float)$request->request->get('tip_amount', 0.0);
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'eur',
                        'product_data' => [
                            'name' => 'Pourboire',
                        ],
                        'unit_amount'  => (int)($tipAmount * 100),
                    ],
                    'quantity'   => 1,
                ]
            ],
            'mode'                 => 'payment',
            'payment_intent_data' => ['application_fee_amount' => 0],
            'success_url'          => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ],
        ['stripe_account' => 'acct_1NtGNBPje2U6C11l']
    );

        return $this->redirect($session->url, 303);
    }


    


    #[Route('/success', name: 'success_url')]
    public function successUrl(): Response
    {
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
      

        $responseTheatre = $httpClient->request('GET', 'https://api.billetreduc.com/api/export/GetNextSessionFromTheater/122', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        

        $theatersData = $responseTheatres->toArray();
        $theaterData = $responseTheatre->toArray();

 
 
    
        return $this->render('payment/success.html.twig', ['theatersData' => $theatersData,'theaterData' => $theaterData]);
    }


    #[Route('/cancel', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
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
      

        $responseTheatre = $httpClient->request('GET', 'https://api.billetreduc.com/api/export/GetNextSessionFromTheater/122', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        

        $theatersData = $responseTheatres->toArray();
        $theaterData = $responseTheatre->toArray();

 
 
    
        return $this->render('payment/cancel.html.twig', ['theatersData' => $theatersData,'theaterData' => $theaterData]);
    }
}