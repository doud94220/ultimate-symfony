<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController extends AbstractController //Va fournir un max de services avec ses différentes méthodes
{
    //protected $formFactory;
    //protected $router;
    //protected $security;
    protected $cartService;
    protected $em;

    /** @var PurchasePersister */
    protected $persister;

    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister)
    {
        //$this->formFactory = $formFactory;
        //$this->router = $router;
        //$this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request) //Il ne faut pas se faire livrer la Request dans le constructeur
    {
        $form = $this->createForm(CartConfirmationType::class);
        //$form = $this->formFactory->create(CartConfirmationType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            //$flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');

            return $this->redirectToRoute('cart_show');
            //return new RedirectResponse($this->router->generate('cart_show'));
        }

        //$user = $this->getUser(); //remplacé par IsGranted
        //$user = $this->security->getUser(); //remplacé par ligne au-dessus

        // if (!$user) {
        //     throw new AccessDeniedException('Vous devez être connecté pour confirmer une commande');
        // }

        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');

            return $this->redirectToRoute('cart_show');
            //return new RedirectResponse($this->router->generate('cart_show'));
        }

        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);

        // PLUS UTILISE :
        //$this->cartService->empty();
        //$this->addFlash('success', 'Votre commande est passée !');

        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
        //return new RedirectResponse($this->router->generate('purchase_index'));
    }
}
