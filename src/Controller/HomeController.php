<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
//use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository)
    {
        //count([])
        //find(id)
        //findBy([], [])
        //findOneBy([], []) ///dlamerde je trouve
        //finAll()
        //$products = $productRepository->findBy([], ['price' => 'DESC']);
        //dump($products);

        // $productRepository = $em->getRepository(Product::class);
        // $product = $productRepository->find(3);
        // $em->remove($product);
        // $em->flush();

        // $product = $productRepository->find(1);
        // $product->setPrice(2500);
        // $em->flush();

        // $product = new Product;
        // $product
        //     ->setName('Table en métal')
        //     ->setPrice(3000)
        //     ->setSlug('table-en-metal');

        // $em->persist($product);
        // $em->flush();

        //dd($product);

        $products = $productRepository->findBy([], [], 3);

        return $this->render(
            'home.html.twig',
            ['products' => $products]
        );
    }
}
