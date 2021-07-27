<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendVisitEmail'
        ];
    }

    public function sendVisitEmail(ProductViewEvent $productViewEvent)
    {
        //$email = new Email();
        // $email = new TemplatedEmail();
        // $email->from(new Address("contact@mail.com", "Infos de la boutique"))
        //     ->to("admin@mail.com")
        //     ->text("Un visiteur est en train de voir la page du produit n°" . $productViewEvent->getProduct()->getId())
        //     //->html("<h1>Visite du produit {$productViewEvent->getProduct()->getId()}</h1>")
        //     ->htmlTemplate('emails/product_view.html.twig')
        //     ->context([
        //         'product' => $productViewEvent->getProduct()
        //     ])
        //     ->subject("Visite du produit n°" . $productViewEvent->getProduct()->getId());

        // $this->mailer->send($email);

        //dd($productViewEvent->getProduct()->getName());
        $this->logger->info("Email envoyé à l'admin pour la visite du produit :" . $productViewEvent->getProduct()->getId()); //Marche pas...
    }
}
