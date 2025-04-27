<?php

namespace App\Controller;

use App\Entity\Stade;
use App\Form\StadeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StadeController extends AbstractController
{
    /**
     * @Route("/admin/stade/new", name="stade_new")
     */
    public function new(Request $request): Response
    {
        $stade = new Stade();
        $form = $this->createForm(StadeType::class, $stade);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde de l'entité dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($stade);
            $entityManager->flush();

            // Redirige vers une page de confirmation ou liste des stades
            return $this->redirectToRoute('stade_list');
        }

        return $this->render('stade/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/stade/{id}/edit", name="stade_edit")
     */
    public function edit(Request $request, Stade $stade): Response
    {
        $form = $this->createForm(StadeType::class, $stade);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour de l'entité dans la base de données
            $this->getDoctrine()->getManager()->flush();

            // Redirige vers une page de confirmation ou liste des stades
            return $this->redirectToRoute('stade_list');
        }

        return $this->render('stade/edit.html.twig', [
            'form' => $form->createView(),
            'stade' => $stade,
        ]);
    }
}
