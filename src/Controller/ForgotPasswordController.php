<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForgotPasswordController extends AbstractController
{
    #[Route('/ForgetPassword', name: 'ForgetPassword')]
    public function index(EventRepository $eventRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('list');
        }

        return $this->render('security/forgotPassword.html.twig');
    }

}
