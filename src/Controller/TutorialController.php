<?php

namespace App\Controller;

use App\Entity\Tutorial;
use App\Form\TutorialType;
use App\Repository\TutorialRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Security\TutorialVoter;

final class TutorialController extends AbstractController
{
    // INDEX : LISTE DES TUTORIELS
    #[Route('/tutorials', name: 'app_tutorials')]
    public function index(
        Request $request,
        TutorialRepository $tutorialRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository
    ): Response {

        $searchTerm = trim($request->query->get('q', ''));
        $categoryId = $request->query->get('category');
        $tagId = $request->query->get('tag');

        $queryBuilder = $tutorialRepository->createQueryBuilder('t')
            ->leftJoin('t.category', 'c')
            ->addSelect('c')
            ->leftJoin('t.author', 'u')
            ->addSelect('u')
            ->orderBy('t.createdAt', 'DESC');

        // Recherche
        if ($searchTerm !== '') {
            $queryBuilder
                ->andWhere('t.title LIKE :search OR t.content LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%');
        }

        // Filtre catégorie
        if ($categoryId !== null && $categoryId !== '') {
            $queryBuilder
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        // Filtre tag
        if ($tagId !== null && $tagId !== '') {
            $queryBuilder
                ->innerJoin('t.tags', 'tag')
                ->andWhere('tag.id = :tagId')
                ->setParameter('tagId', $tagId);
        }

        $tutorials = $queryBuilder->getQuery()->getResult();

        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        $tags = $tagRepository->findBy([], ['name' => 'ASC']);

        return $this->render('tutorial/index.html.twig', [
            'tutorials' => $tutorials,
            'searchTerm' => $searchTerm,
            'categories' => $categories,
            'tags' => $tags,
            'selectedCategory' => $categoryId,
            'selectedTag' => $tagId,
        ]);
    }

    // SHOW : DÉTAIL D'UN TUTORIEL
    #[Route('/tutorials/{slug}', name: 'app_tutorial_show')]
    public function show(
        string $slug,
        TutorialRepository $tutorialRepository
    ): Response {

        $tutorial = $tutorialRepository->findOneBy(['slug' => $slug]);

        if (!$tutorial) {
            throw $this->createNotFoundException('Tutorial not found.');
        }

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);

        return $this->render('tutorial/show.html.twig', [
            'tutorial' => $tutorial,
            'commentForm' => $commentForm,
        ]);
    }

    // CREATE : CRÉER UN TUTORIEL
    #[Route('/tutorial/new', name: 'app_tutorial_new')]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {

        $tutorial = new Tutorial();

        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tutorial->setSlug(
                strtolower($slugger->slug($tutorial->getTitle()))
            );

            $tutorial->setCreatedAt(new \DateTimeImmutable());
            $tutorial->setAuthor($this->getUser());

            $entityManager->persist($tutorial);
            $entityManager->flush();

            $this->addFlash('success', 'Tutorial created successfully!');

            return $this->redirectToRoute('app_tutorials');
        }

        return $this->render('tutorial/new.html.twig', [
            'form' => $form,
        ]);
    }

    // UPDATE : MODIFIER UN TUTORIEL
    #[Route('/tutorials/{slug}/edit', name: 'app_tutorial_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(
        string $slug,
        Request $request,
        TutorialRepository $tutorialRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {

        $tutorial = $tutorialRepository->findOneBy(['slug' => $slug]);

        if (!$tutorial) {
            throw $this->createNotFoundException('Tutorial not found.');
        }

        // Vérifier que l'utilisateur est l'auteur
        $this->denyAccessUnlessGranted(TutorialVoter::EDIT, $tutorial);
        

        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tutorial->setSlug(
                strtolower($slugger->slug($tutorial->getTitle()))
            );

            $entityManager->flush();

            $this->addFlash('success', 'Tutorial updated successfully!');

            return $this->redirectToRoute('app_tutorial_show', [
                'slug' => $tutorial->getSlug(),
            ]);
        }

        return $this->render('tutorial/edit.html.twig', [
            'form' => $form,
            'tutorial' => $tutorial,
        ]);
    }
    // DELETE : SUPPRIMER UN TUTORIEL
#[Route('/tutorials/{slug}/delete', name: 'app_tutorial_delete', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function delete(
    string $slug,
    Request $request,
    TutorialRepository $tutorialRepository,
    EntityManagerInterface $entityManager
): Response {

    $tutorial = $tutorialRepository->findOneBy(['slug' => $slug]);

    if (!$tutorial) {
        throw $this->createNotFoundException('Tutorial not found.');
    }

    // Vérifier les permissions avec le Voter
    $this->denyAccessUnlessGranted(TutorialVoter::DELETE, $tutorial);

    // Vérifier le CSRF token
    if (!$this->isCsrfTokenValid(
        'delete' . $tutorial->getId(),
        $request->request->get('_token')
    )) {
        throw $this->createAccessDeniedException('Invalid CSRF token.');
    }

    $entityManager->remove($tutorial);
    $entityManager->flush();

    $this->addFlash('success', 'Tutorial deleted successfully!');

    return $this->redirectToRoute('app_tutorials');
}
// ADD COMMENT
#[Route('/tutorials/{slug}/comment', name: 'app_tutorial_comment', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function addComment(
    string $slug,
    Request $request,
    TutorialRepository $tutorialRepository,
    EntityManagerInterface $entityManager
): Response {

    $tutorial = $tutorialRepository->findOneBy(['slug' => $slug]);

    if (!$tutorial) {
        throw $this->createNotFoundException('Tutorial not found.');
    }

    $comment = new Comment();

    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $comment->setAuthor($this->getUser());
        $comment->setTutorial($tutorial);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Comment added successfully!');

    } else {
        $this->addFlash('danger', 'Invalid comment.');
    }

    return $this->redirectToRoute('app_tutorial_show', [
        'slug' => $tutorial->getSlug(),
    ]);
}
}