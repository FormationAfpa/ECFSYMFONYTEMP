<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/subscription', name: 'admin_subscription_')]
#[IsGranted('ROLE_ADMIN')]
class SubscriptionController extends AbstractController
{
    private $subscriptionRepository;
    private $entityManager;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $activeSubscriptions = $this->subscriptionRepository->findActive();
        $expiredSubscriptions = $this->subscriptionRepository->findExpired();

        return $this->render('admin/subscription/index.html.twig', [
            'active_subscriptions' => $activeSubscriptions,
            'expired_subscriptions' => $expiredSubscriptions,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Subscription $subscription): Response
    {
        return $this->render('admin/subscription/show.html.twig', [
            'subscription' => $subscription,
        ]);
    }

    #[Route('/{id}/cancel', name: 'cancel', methods: ['POST'])]
    public function cancel(Request $request, Subscription $subscription): Response
    {
        if ($this->isCsrfTokenValid('cancel'.$subscription->getId(), $request->request->get('_token'))) {
            $subscription->setEndDate(new \DateTime());
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'abonnement a été annulé avec succès.');
        }

        return $this->redirectToRoute('admin_subscription_index');
    }
}
