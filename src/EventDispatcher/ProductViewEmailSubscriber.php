<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendVisitEmail'
        ];
    }

    public function sendVisitEmail(ProductViewEvent $productViewEvent)
    {
        //dd($productViewEvent->getProduct()->getName());
        $this->logger->info("Email envoyé à l'admin pour la visite du produit :" . $productViewEvent->getProduct()->getId()); //Marche pas...
    }
}
