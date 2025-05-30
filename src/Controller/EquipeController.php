<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/equipe')]
final class EquipeController extends AbstractController
{
    #[Route(name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 5;
        $statut = $request->query->get('statut');
        $pays = $request->query->get('pays'); // (on ajoute le filtre pays)

        $paginatedData = $equipeRepository->findPaginated($page, $limit, $statut, $pays);

        // On récupère la liste unique des pays existants :
        $listePays = $equipeRepository->findDistinctPays();

        $pageCount = ceil($paginatedData['totalItems'] / $limit);

        return $this->render('admin/equipe/index.html.twig', [
            'equipes' => $paginatedData['results'],
            'pagination' => [
                'currentPage' => $page,
                'pageCount' => $pageCount,
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $pageCount,
                'previousPage' => max(1, $page - 1),
                'nextPage' => min($pageCount, $page + 1),
            ],
            'listePays' => $listePays, // 🔥 On envoie à Twig
        ]);
    }



    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logoFile')->getData();

            if ($logoFile) {
                $newFilename = uniqid().'.'.$logoFile->guessExtension();

                // Déplacez le fichier dans le répertoire public/uploads/logos
                $logoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/logos',
                    $newFilename
                );

                $equipe->setLogo($newFilename);
            }

            $entityManager->persist($equipe);
            $entityManager->flush();

            $this->addFlash('success', 'L\'équipe a été créée avec succès');
            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $currentLogo = $equipe->getLogo();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logoFile')->getData();

            if ($logoFile) {
                // Remove old logo if it exists
                if ($currentLogo) {
                    $oldLogoPath = $this->getParameter('kernel.project_dir').'/public/uploads/logos/'.$currentLogo;
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }

                // Upload new logo
                $newFilename = uniqid().'.'.$logoFile->guessExtension();
                $logoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/logos',
                    $newFilename
                );
                $equipe->setLogo($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'L\'équipe a été modifiée avec succès');
            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/archive', name: 'app_equipe_archive', methods: ['POST'])]
    public function archive(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('archive'.$equipe->getId(), $request->request->get('_token'))) {
            $equipe->setStatut('Archivé');
            $entityManager->flush();
            $this->addFlash('success', 'L\'équipe a été archivée avec succès');
        }

        return $this->redirectToRoute('app_equipe_index');
    }

    #[Route('/{id}/restore', name: 'app_equipe_restore', methods: ['POST'])]
    public function restore(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('restore'.$equipe->getId(), $request->request->get('_token'))) {
            $equipe->setStatut('Actif');
            $entityManager->flush();
            $this->addFlash('success', 'L\'équipe a été restaurée avec succès');
        }

        return $this->redirectToRoute('app_equipe_index');
    }

    #[Route('/search', name: 'app_equipe_search', methods: ['GET'])]
    public function search(Request $request, EquipeRepository $equipeRepository): Response
    {
        $search = $request->query->get('search');
        $statut = $request->query->get('statut');
        $pays = $request->query->get('pays');

        $result = $equipeRepository->findPaginated(1, 100, $statut, $pays, $search); // pas de pagination ici

        return $this->render('admin/equipe/_table.html.twig', [
            'equipes' => $result['results']
        ]);
    }

}
