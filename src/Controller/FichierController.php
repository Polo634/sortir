<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FichierController extends AbstractController
{
    /**
     * @Route("/profil/ajout-photoprofil", name="upload_photo")
     */

    public function index(): Response
    {
        return $this->render('profil/upload_photo.html.twig', [
            'controller_name' => 'FichierController',
        ]);
    }
}
