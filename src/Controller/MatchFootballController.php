<?php

namespace App\Controller;

use App\Entity\MatchFootball;
use App\Form\MatchFootballType;
use App\Repository\MatchFootballRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/match')]
final class MatchFootballController extends AbstractController{
    #[Route(name: 'app_match_football_index', methods: ['GET'])]
    public function index(MatchFootballRepository $matchFootballRepository): Response
    {
        return $this->render('admin/match_football/index.html.twig', [
            'match_footballs' => $matchFootballRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_match_football_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matchFootball = new MatchFootball();
        $form = $this->createForm(MatchFootballType::class, $matchFootball, [
            'entity_manager' => $entityManager,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matchFootball->setBilletsVirageVendus(0);
            $matchFootball->setBilletsPelouseVendus(0);
            $matchFootball->setBilletsEnceinteVendus(0);
            $entityManager->persist($matchFootball);
            $entityManager->flush();

            return $this->redirectToRoute('app_match_football_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/match_football/new.html.twig', [
            'match_football' => $matchFootball,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_match_football_show', methods: ['GET'])]
    public function show(MatchFootball $matchFootball): Response
    {
        return $this->render('admin/match_football/show.html.twig', [
            'match_football' => $matchFootball,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_match_football_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MatchFootball $matchFootball, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchFootballType::class, $matchFootball, [
            'entity_manager' => $entityManager,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_match_football_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/match_football/edit.html.twig', [
            'match_football' => $matchFootball,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_match_football_delete', methods: ['POST'])]
    public function delete(Request $request, MatchFootball $matchFootball, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matchFootball->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($matchFootball);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_match_football_index', [], Response::HTTP_SEE_OTHER);
    }
}
