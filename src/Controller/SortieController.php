<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnuleSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SortieController extends AbstractController
{

    /**
     *
     * @Route("/inscrire/{id}", name="sortie_inscrire", requirements={"id": "\d+"})
     */

     public function inscrire($id,SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
     {
        // on recupère la sortie où on veut s'inscrire via son id
            $sortie = $sortieRepository->find($id);

        // le $this->getUser() récupère la personne connectée
             $sortie -> addParticipant($this->getUser());

        // pour filtrer notre demande on recupere l'etat, le nb de participant
            $etat = $sortie->getEtat()->getLibelle();
            $nbparticipant = $sortie->getParticipants();
            $nbInscrit = count($nbparticipant);

        //condition pour adapter le message flash en fonction du filtre
        // les filtres sont appliqués dans le twig et aussi pour plus de sécurité
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

         // le redirectToRoute me permet de ne pas recharger la page juste me transférer
             return $this->redirectToRoute('main_home');
         }

    /**
     *
     * @Route("/desister/{id}", name="sortie_desister", requirements={"id": "\d+"})
     */
    public function desister($id,SortieRepository $sortieRepository, EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        // on recupere la sortie où on veut se désister via son id, puis meme méthode que pour s'inscrire
            $sortie = $sortieRepository->find($id);
            $sortie -> removeParticipant($this->getUser());
            $etat = $sortie->getEtat()->getLibelle();

        if($etat == 'Ouverte'  | $etat == 'Clôturée' && ((new \DateTime('now')) <= $sortie->getDateLimiteInscription())){

            $sortie ->setEtat($etatRepository->findOneBy(['libelle' => 'Ouverte']));

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
     * @Route ("/annuler/{id}", name="annuler_sortie")
     */
    public function annuler($id, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager, Request $request): Response{

        //récupère la sortie concernée par l'annulation via son id
            $sortie=$sortieRepository->find($id);

        //permet de modifier l'etat de ma sortie, la passe à 'Annulée'
            $etatCloture = $etatRepository->findOneBy(['libelle'=> 'Annulée']);
            $sortie->setEtat($etatCloture);

        //affichage de mon formulaire pour ajouter le motif de mon annulation
            $form = $this->createForm(AnnuleSortieType::class, $sortie);
            $form->handleRequest($request);

        //je ne vérifie pas avec isValid car sinon il va vérifier tous mes asserts de sortie.php et ca va bloquer
        if($form->isSubmitted()){

          // dans ce cas je modifie mes infosSortie par le motif d'annulation
            $form->get('infosSortie')->getData();

            $entityManager->persist($sortie);
            $entityManager->flush();

             return $this->render('sorties/sortie-detail.html.twig',[
                 "sortie"=> $sortie,
             ]);
        }
        return $this->render('sorties/annuler.html.twig',[
            "sortie"=> $sortie,
            'form' => $form->createView(),
        ]);
    }
}
