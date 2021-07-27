<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use Doctrine\ORM\EntityManager;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        //dd($category->getProducts());

        if (!$category) {
            //throw new NotFoundHttpException("La catégorie demandée n'existe pas");
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
     */
    public function show($slug, ProductRepository $productRepository, Request $request, $prenom, EventDispatcherInterface $dispatcher)
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas...");
        }

        //Lancer un évènement qui permettra à l'administrateur de voir que qqn a regardé ce produit
        $productEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productEvent, 'product.view');

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        //////////////////// Pour souvenir //////////////////////
        // $client = [
        //     'nom' => '',
        //     'prenom' => 'Edouard',
        //     'voiture' => [
        //         'marque' => '',
        //         'couleur' => 'Noire'
        //     ]
        // ];

        // $collection = new Collection([
        //     'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide !"]),
        //     'prenom' => [
        //         new NotBlank(['message' => 'Le prénom ne doit pas être vide']),
        //         new Length(['min' => 3, 'minMessage' => 'Le prénom ne doit pas faire moins de 34 caractères'])
        //     ],
        //     'voiture' => new Collection([
        //         'marque' => new NotBlank(['message' => 'La marque de la voiture est obligatoire']),
        //         'couleur' => new NotBlank(['message' => 'La couleur de la voiture est obligatoire'])
        //     ])
        // ]);

        // $resultat = $validator->validate($client, $collection);


        //////////////////// Pour souvenir //////////////////////
        // $age = 15;

        // $resultat = $validator->validate($age, [
        //     new LessThanOrEqual([
        //         'value' => 90,
        //         'message' => "L'âge ne doit pas dépasser {{ compared_value }} mais vous avez renseigné {{ value }}"
        //     ]),
        //     new GreaterThan([
        //         'value' => 0,
        //         'message' => "L'âge doit être supérieur à 0"
        //     ])
        // ]);

        // $product = new Product;
        // $product->setName("Salut à tous")
        //     ->setPrice(200);

        // $resultat = $validator->validate($product, null, ["Default", "with-price"]);

        // if ($resultat->count() > 0) {
        //     dd("Il y a des erreurs ", $resultat);
        // }

        // dd($resultat);


        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);
        //$form->setData($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            //$product = $form->getData(); //On peut l'enlever
            $em->flush(); //pas besoin du persist => un peu bizarre

            // $url = $urlGenerator->generate('product_show', [
            //     'category_slug' => $product->getCategory()->getSlug(),
            //     'slug' => $product->getSlug()
            // ]);

            //$response = new RedirectResponse($url);

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        $data = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            //$product = $form->getData(); //Plus besoin de ça
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            // $product = new Product;
            // $product->setName($data['name'])
            //     ->setShortDescription($data['shortDescription'])
            //     ->setPrice($data['price'])
            //     ->setCategory($data['category']);

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
