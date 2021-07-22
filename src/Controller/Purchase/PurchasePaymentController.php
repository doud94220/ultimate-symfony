<?php

namespace App\Controller\Purchase;

use Stripe\Stripe;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            return $this->redirectToRoute('cart_show');
        }

        // PLUS UTILISE :
        // \Stripe\Stripe::setApiKey('sk_test_51JFfZhF391UTnzkpqzzBMMFgqb6wQO687rNcNd6i1EtWmqTJMQlOlyCA5yXsrr1ml7WpFpwWHXfD9gsemfHbLvlk00W3RBjacB');

        // $intent = \Stripe\PaymentIntent::create([
        //     'amount' => $purchase->getTotal(),
        //     'currency' => 'eur'
        // ]);

        $intent = $stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }
}
