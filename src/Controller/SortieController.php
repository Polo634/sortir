<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{

    /**
     *
     * @Route("/inscrire/{id}", name="sortie_inscrire", requirements={"id": "\d+"})
     */

     public function inscrire($id,SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
     {
        $sortie = $sortieRepository->find($id);
        $sortie -> addParticipant($this->getUser());

        $etat = $sortie->getEtat()->getLibelle();
         $nbparticipant = $sortie->getParticipants();
        $nbInscrit = count($nbparticipant);

        if($etat == 'Ouverte'  && $nbInscrit <= ($sortie->getNbInscriptionsMax()) && ((new \DateTime('now')) <= $sortie->getDateLimiteInscription())){

         $entityManager->persist($sortie);
         $entityManager->flush();

         $this->addFlash('succes', 'Vous avez été inscrit avec succès, bonne sortie !');
        }
        elseif ($etat == 'Ouverte'  && $nbInscrit <= ($sortie->getNbInscriptionsMax()) && ((new \DateTime('now')) > $sortie->getDateLimiteInscription())){

            $this->addFlash('echec', 'Impossible de s\'inscrire à cette sortie, la date a été dépassée');

        }

        elseif ($etat == 'Ouverte'  && $nbInscrit > ($sortie->getNbInscriptionsMax()) && ((new \DateTime('now')) <= $sortie->getDateLimiteInscription())){

            $this->addFlash('echec', 'Impossible de s\'inscrire à cette sortie, le nombre d\'inscrit maximum a été atteint');

        }

        elseif ($etat != 'Ouverte'  && $nbInscrit <= ($sortie->getNbInscriptionsMax()) && ((new \DateTime('now')) <= $sortie->getDateLimiteInscription())){

            $this->addFlash('echec', 'Impossible de s\'inscrire à cette sortie, elle n\'est pas ouverte à la réservation');

        }
         return $this->redirectToRoute('main_home');
         }

    /**
     *
     * @Route("/desister/{id}", name="sortie_desister", requirements={"id": "\d+"})
     */
    public function desister($id,SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        $sortie = $sortieRepository->find($id);
        $sortie -> removeParticipant($this->getUser());
        $etat = $sortie->getEtat()->getLibelle();

        if($etat == 'Ouverte'  && ((new \DateTime('now')) <= $sortie->getDateLimiteInscription())){

            $entityManager->persist($sortie);
            $entityManager->flush();

        $this->addFlash('succes', 'Vous avez été désinscrit avec succès, à bientot !');
         }
        elseif (((new \DateTime('now')) > $sortie->getDateLimiteInscription())){

            $this->addFlash('echec', 'Impossible de se désinscrire, la date limite d\'inscription a été depassée!');

        }

        return $this->redirectToRoute('main_home');
    }

    /**
     *
     * @Route("/annuler/{id}", name="sortie_annuler", requirements={"id": "\d+"})
     */
/*
    public function annuler($id, SortieRepository $sortieRepository, Request $request): Response
    {
        $sortie = $sortieRepository->find($id);

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

    }
*/
}