<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    // protected $logger;

    // public function __construct(LoggerInterface $logger)
    // {
    //     $this->logger = $logger;
    // }

    // protected $calculator;

    // public function __construct(Calculator $calculator)
    // {
    //     $this->calculator = $calculator;
    // }

    /**
     * @Route("/hello/{prenom?World}", name="hello", methods={"GET"}, host="localhost", schemes={"http", "https"})
     */
    public function hello($prenom, LoggerInterface $logger, Calculator $calculator, Slugify $slugify, Environment $twig)
    {
        dump($twig);

        //$slugify = new Slugify();
        dump($slugify->slugify("Hello World"));

        //$this->logger->error("Message erreur doud");
        $logger->error("message erreur doud");

        //$tva = $this->calculator->calcul(100);
        $tva = $calculator->calcul(100);
        dump($tva);

        return new Response("Hello $prenom");
    }
}
