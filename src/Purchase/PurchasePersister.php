<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase)
    {

        $purchase->setUser($this->security->getUser());
        // ON VA FAIRE LES 2 ACTIONS DES 2 DESSOUS DANS le Purchase.php
        //->setPurchasedAt(new \DateTime()) //Syntaxe pas comme le prof, j'ai trouvé ça sur le net (backslash avant DateTime)
        //->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());

            // ON VA LE FAIRE DANS LE setPurchase()
            //$purchase->addPurchaseItem($purchaseItem);

            $this->em->persist($purchaseItem);
        }

        $this->em->flush();
    }
}
