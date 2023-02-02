<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VillesController extends AbstractController
{
    /**
     * @Route("/admin/ville", name="details_villes")
     */
    public function detailsVilles(): Response
    {
        return $this->render("admin/details_villes.html.twig");
    }
}