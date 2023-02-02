<?php

namespace App\Controller;


use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/admin/campus", name="details_campus")
     */
    public function detailsCampus(): Response
    {
        return $this->render("admin/details_campus.html.twig");

    }
}