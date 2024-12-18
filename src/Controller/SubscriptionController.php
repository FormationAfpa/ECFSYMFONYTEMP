<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/subscription', requirements: ['_locale' => 'en|fr'], defaults: ['_locale' => 'fr'])]
#[IsGranted('ROLE_USER')]
class SubscriptionController extends AbstractController
{
    #[Route('/', name: 'app_subscription')]
    public function index(): Response
    {
        $user = $this->getUser();
        $subscription = $user->getSubscription();

        return $this->render('subscription/index.html.twig', [
            'subscription' => $subscription,
        ]);
    }

    #[Route('/new/{type}', name: 'app_subscription_new', requirements: ['type' => 'mensuel|annuel'])]
    public function new(Request $request, EntityManagerInterface $entityManager, string $type = 'mensuel'): Response
    {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur a déjà un abonnement actif
        if ($user->hasActiveSubscription()) {
            $this->addFlash('warning', 'Vous avez déjà un abonnement actif.');
            return $this->redirectToRoute('app_subscription');
        }

        $subscription = new Subscription();
        $subscription->setType($type);
        $subscription->setStartDate(new \DateTime());
        $subscription->setUser($user);
        
        $form = $this->createForm(SubscriptionType::class, $subscription, [
            'type_choices' => [$type => ucfirst($type)],
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subscription);
            $entityManager->flush();

            $this->addFlash('success', 'Votre abonnement a été souscrit avec succès !');
            return $this->redirectToRoute('app_subscription');
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }
}
