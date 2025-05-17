<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $paginatedData = $userRepository->findPaginated($page, $limit);

        $pageCount = ceil($paginatedData['totalItems'] / $limit);

        return $this->render('user/index.html.twig', [
            'users' => $paginatedData['results'],
            'pagination' => [
                'currentPage' => $page,
                'pageCount' => $pageCount,
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $pageCount,
                'previousPage' => max(1, $page - 1),
                'nextPage' => min($pageCount, $page + 1),
            ],
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setDateInscription(new \DateTime());
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $selectedRole = $form->get('role')->getData();
            $user->setRoles([$selectedRole]);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->get('role')->setData(
            in_array('ROLE_ADMIN', $user->getRoles()) ? 'ROLE_ADMIN' : 'ROLE_USER'
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'app_user_search', methods: ['GET'])]
    public function search(UserRepository $userRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        $role = $request->query->get('role');
        $isVerified = $request->query->get('verified');

        $results = $userRepository->findPaginated(1, 100, $role, $isVerified, $search);

        return $this->render('user/_table.html.twig', [
            'users' => $results['results']
        ]);
    }

}
