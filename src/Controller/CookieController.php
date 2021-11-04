<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\CookieFormType;
use App\Entity\Cookie;

class CookieController extends AbstractController
{
    #[Route('/cookie', name: 'cookie')]
    public function index(): Response
    {
        return $this->render('cookie/index.html.twig', [
            'controller_name' => 'CookieController',
        ]);
    }

    #[Route('/add_cookie', name: 'add_cookie')]
    public function addCookie(Request $request): Response
    {
        $cookie = new Cookie();
        $form = $this->createForm(CookieFormType::class, $cookie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cookie);
            $entityManager->flush();
        }

        return $this->render("cookie/cookie-form.html.twig", [
            "form_title" => "Ajouter un produit",
            "form_cookie" => $form->createView(),
        ]);
    }

    #[Route('/cookies', name: 'cookies')]
    public function cookies()
    {
        $cookies = $this->getDoctrine()->getRepository(Cookie::class)->findAll();

        return $this->render('cookie/cookies.html.twig', [
            "cookies" => $cookies,
        ]);
    }

    #[Route('/cookie/{id}', name: 'cookie')]
    public function cookie(int $id): Response
    {
        $cookie = $this->getDoctrine()->getRepository(Cookie::class)->find($id);

        return $this->render("cookie/cookie.html.twig", [
            "cookie" => $cookie,
        ]);
    }

    #[Route('/modify_cookie/{id}', name: 'modify_cookie')]
    public function modifyCookie(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cookie = $entityManager->getRepository(Cookie::class)->find($id);
        $form = $this->createForm(CookieFormType::class, $cookie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
        }

        return $this->render("cookie/cookie-form.html.twig", [
            "form_title" => "Modifier un cookie",
            "form_cookie" => $form->createView(),
        ]);
    }

    #[Route('/delete_cookie/{id}', name: 'delete_cookie')]
    public function deleteCookie(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $cookie = $entityManager->getRepository(Cookie::class)->find($id);
        $entityManager->remove($cookie);
        $entityManager->flush();

        return $this->redirectToRoute("cookies");
    }
}
