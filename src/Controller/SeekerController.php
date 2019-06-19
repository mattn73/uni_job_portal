<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SeekerController extends AbstractController
{
    /**
     * @Route("/seeker", name="seeker")
     */
    public function index()
    {
        return $this->render('seeker/index.html.twig', [
            'controller_name' => 'SeekerController',
        ]);
    }
}
