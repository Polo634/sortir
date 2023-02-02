<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class MajEtatService
{

    private SortieRepository $sortieRepository;
    private EtatRepository $etatRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(SortieRepository $sortieRepository,
                                EtatRepository $etatRepository,
                                EntityManagerInterface $entityManager


    ){

        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;

    }
    public function majEtatSortie()
    {

        //mettre une sortie ouverte en clôturée en fonction de la date limite
        $etat = $this->etatRepository->findOneBy(['libelle' => 'Clôturée']);
        $sorties = $this->sortieRepository->chercherSortieOuvertes();
        foreach ($sorties as $sortie) {
            $sortie->setEtat($etat);
            $this->entityManager->persist($sortie);

        }

        $this->entityManager->flush();

        //mettre une sortie en état Activité en cours
        $etat = $this->etatRepository->findOneBy(['libelle' => 'Activité en cours']);
        $sorties = $this->sortieRepository->chercherSortieCloturees();
        foreach ($sorties as $sortie) {
            $sortie->setEtat($etat);
            $this->entityManager->persist($sortie);

        }

        $this->entityManager->flush();

        //mettre une sortie en Passée
        $etat = $this->etatRepository->findOneBy(['libelle' => 'Passée']);
        $sorties = $this->sortieRepository->chercherSortieEnCours();
        foreach ($sorties as $sortie) {
            $sortie->setEtat($etat);
            $this->entityManager->persist($sortie);

        }

        $this->entityManager->flush();

        //mettre une sortie en archivée
        $etat = $this->etatRepository->findOneBy(['libelle' => 'Archivée']);
        $sorties = $this->sortieRepository->chercherSortiePassee();
        foreach ($sorties as $sortie) {
            $sortie->setEtat($etat);
            $this->entityManager->persist($sortie);

        }

        $this->entityManager->flush();



        }

        public function majSortieNbInscrits(){

            //mettre une sortie en clôturée si nbr d'inscrits = nbr max de places
            $etat = $this->etatRepository->findOneBy(['libelle' => 'Clôturée']);
            $sorties = $this->sortieRepository->chercherSortieOuvertesPourNbInscrits();

            foreach ($sorties as $sortie) {
                    $sortie->setEtat($etat);
                    $this->entityManager->persist($sortie);
            }
            $this->entityManager->flush();


        }


}