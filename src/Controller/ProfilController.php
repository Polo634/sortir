<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\PasswordUpdate;
use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



class ProfilController extends AbstractController

{

    /**
     * @Route("/profil/afficher/{id}", name="details")
     */
    public function details(int $id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        return $this->render('profil/details.html.twig', [
            "participant" => $participant
        ]);

    }


    /**
     * @Route("/profil/modifier/{id}", name="modifier")
     */
    public function update(EntityManagerInterface $em,
                           Request $request,
                           int $id,
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

            $this->addFlash('success', 'Profil modifié avec succès.');


        } return $this->render("profil/modifier.html.twig", [
            'form' => $form->createView()]);


     }}