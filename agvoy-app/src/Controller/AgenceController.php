<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Controleur Agence
 * @Route("/")
 */
class AgenceController extends AbstractController
{
    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index(): Response
    {
        return $this->render('agence/index.html.twig');
    }    
}
