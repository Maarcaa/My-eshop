<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * Une fonction de controller appelle une action , pour rappel 
     * name ="nomducontroller_ nomdel'action" 
     * 
     * @Route ("/", name="default_home", methods={"GET"})
     */
  public function home(): Response
  {
    return $this->render('default/home.html.twig');
  }
}