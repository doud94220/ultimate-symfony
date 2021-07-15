<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        //dd($utils);

        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);
        //$form = $factory->createNamed('', LoginType::class, ['_username' => $utils->getLastUsername()]);

        //dump($utils->getLastAuthenticationError(), $utils->getLastUsername());

        return $this->render('security/login.html.twig', [
            'formView' => $form->createView(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }
}
