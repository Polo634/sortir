<?php

namespace App\Controller;

use App\Form\FiltreType;
use App\Models\Filtre;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{

    /**
     * @Route ("/home", name="main_home")
     */

    public function home(SortieRepository $sortieRepository,
                         CampusRepository $campusRepository,
                         Request $request): Response
    {

        $campus = $campusRepository->findAll();

        $filtre = new Filtre();
        $form = $this->createForm(FiltreType::class, $filtre);
        $form->handleRequest($request);
        $sorties = $sortieRepository->findSearch($filtre);
        return $this->render('sorties/list.html.twig', [
            "sorties" => $sorties,
            "campus" => $campus,
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route ("/annuler/{id}", name="annuler_sortie")
     */
    /*
    public function annuler($id, SortieRepository $sortieRepository){
        $sortie=$sortieRepository->find($id);

        return $this->render('sorties/annuler.html.twig',[
            "sortie"=> $sortie
        ]);
    }
*/
}