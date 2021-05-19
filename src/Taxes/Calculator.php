<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Calculator
{
    protected $logger;
    protected $tva;

    public function __construct(LoggerInterface $logger, float $tva)
    {
        $this->logger = $logger;
        $this->tva = $tva;
    }

    public function calcul(float $prix): float
    {
        $this->logger->info("un calcul a lieu : $prix");
        return $prix_avec_tva = $prix + ($prix * ($this->tva / 100));
    }
}
