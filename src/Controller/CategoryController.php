<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function RenderMenuList()
    {
        //1. Aller chercher toutes les catégories dans la BDD (Repository)
        $categories = $this->categoryRepository->findAll();

        //2. Renvoyer le contenu HTML sous la forme d'une Response ($this->render)
        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/", name="category_home")
     */
    // public function home()
    // {
    //     return $this->render('category/index.html.twig');
    // }

    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(EntityManagerInterface $em, Request $request, SluggerInterface $slugger)
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        $toto = $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);

        return $toto;
    }

    // Pour mémoire :
    // @IsGranted("ROLE_ADMIN", message="Vous n'avez pas le droit d'accéder à cette ressource")
    // @Isgranted("CAN_EDIT", subject="id", message="Vou n'êtes pas le proprio de cette catégorie")

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Security $security)
    {
        ///// Pour mémoire :
        //$this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'accéder à cette ressource");
        //// Pour Backup
        // $user = $this->getUser();

        // if ($user === null) {
        //     return $this->redirectToRoute('security_login');
        // }

        // if ($this->isGranted("ROLE_ADMIN") === false) {
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource");
        // }

        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        // $this->denyAccessUnlessGranted("CAN_EDIT", $category, "Vous n'êtes pas le propriétaire de cette catégorie");

        // $user = $this->getUser();

        // if (!$user) {
        //     return $this->redirectToRoute('security_login');
        // }

        // if ($user !== $category->getOwner()) {
        //     throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
        // }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
