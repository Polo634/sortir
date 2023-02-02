<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\PhotoProfilType;
use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



class ProfilController extends AbstractController

{

    /**
     * @Route("/profil/afficher/{id}", name="details")
     */
    public function details(int $id,
                            ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        return $this->render('profil/details.html.twig', [
            "participant" => $participant
        ]);

    }

    /**
     * @Route("/profil/modifier/{id}", name="modifier")
     */
    public function update(EntityManagerInterface      $em,
                           Request                     $request,
                           int                         $id,
                           UserPasswordHasherInterface $encoder): Response
    {

        $user = $em->getRepository(Participant::class)->find($id);
        $form = $this->createForm(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();

            if (null != $newPassword) {      //si le champs mot de passe est rempli

                //alors on hash le nouveau mot de passe et on le réécrit sur l'ancien en BDD
                $user->setMotPasse(
                    $encoder->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    ));
            }

            $em->flush();

            $this->addFlash('succes', 'Profil modifié avec succès.');


        }
        return $this->render("profil/modifier.html.twig", [
            'form' => $form->createView()]);


    }

    /**
     *
     * @Route("/profil/modification/photo", name="photo_profil")
     */
    public function modifPhoto(EntityManagerInterface $em,Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PhotoProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                //nom de fichier aléatoire et unique
                $safePicName = bin2hex(random_bytes(10)) . uniqid();
                $newPicName = $safePicName.'.'.$user->getModifPhoto()->guessExtension();

                //on déplace le fichier vers le dossier d'accueil
                //profile_pic_dir est défini dans services.yaml(chemin d'url plus court)
                $user->getModifPhoto()->move($this->getParameter('profile_pic_dir'), $newPicName);

                $user->setPhotoProfil($newPicName);

                $user->setModifPhoto(null);//a voir si ok mais si je ne met pas ca ca ne fonctionne pas^^


                $em->persist($user);
                $em->flush();

                //s'il y avait une autre photo précédemment...
                if (!empty($anciennePhotoProfil)){

                    //supprime la photo précédente
                    $piclocation = $this->getParameter('profile_pic_dir') . "/" . $anciennePhotoProfil;
                    if (file_exists($piclocation)){
                        unlink($piclocation);
                    }
                    $this->addFlash('succes', 'Photo de profil modifiée !');
                }
                //si nouvelle photo:
                else {
                    $this->addFlash('succes', 'Photo de profil ajoutée !');
                }

                return $this->redirectToRoute('details', ["id" => $user->getId()]);
            }

            $user->setModifPhoto(null);
        }

        $em->refresh($user);

        return $this->render('profil/photo_profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}