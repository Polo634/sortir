<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\AnnuleSortieType;
use App\Form\FiltreType;
use App\Models\Filtre;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/home", name="main_")
 */
class MainController extends AbstractController
{

    /**
     * @Route ("/", name="home")
     */

    public function home(SortieRepository $sortieRepository,
                         CampusRepository $campusRepository,
                         Request $request): Response
    {

        $campus = $campusRepository->findAll();

        $filtre = new Filtre();
        $form = $this->createForm(FiltreType::class, $filtre);
        $form->handleRequest($request);
        $sorties = $sortieRepository->findSearch($filtre, $this->getUser());
        return $this->render('sorties/list.html.twig', [
            "sorties" => $sorties,
            "campus" => $campus,
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function list(SortieRepository $sortieRepository, int $id):Response
    {
        $sortie = $sortieRepository->find($id);
        $participants = $sortieRepository->find($id);


        if (!$sortie) {
            throw $this->createNotFoundException('sortie non trouvée');
        }

        return $this->render('sorties/sortie-detail.html.twig', [
            'sortie' => $sortie,
            'participants' => $participants
        ]);
    }

}