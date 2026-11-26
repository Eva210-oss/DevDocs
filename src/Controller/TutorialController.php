<?php

namespace App\Controller;

use App\Entity\Tutorial;
use App\Form\TutorialType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Security\TutorialVoter;

final class TutorialController extends AbstractController
{
    #[Route('/tutorial', name: 'app_tutorial')]
    public function index(): Response
    {
        return $this->render('tutorial/index.html.twig', [
            'controller_name' => 'TutorialController',
        ]);
    }

    #[Route('/tutorial/new', name: 'app_tutorial_new', priority: 1)]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $tutorial = new Tutorial();
        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tutorial->setAuthor($this->getUser());
            $tutorial->setSlug($slugger->slug($tutorial->getTitle())->lower() . '-' . uniqid());
            $em->persist($tutorial);
            $em->flush();

            $this->addFlash('success', 'Tutoriel publié !');

            return $this->redirectToRoute('app_tutorial_show', ['slug' => $tutorial->getSlug()]);
        }

        return $this->render('tutorial/new.html.twig', ['form' => $form]);
    }

    #[Route('/tutorial/{slug}', name: 'app_tutorial_show')]
    public function show(Tutorial $tutorial, Request $request, EntityManagerInterface $em): Response
    {
        $form = null;

        if ($this->getUser()) {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setAuthor($this->getUser())->setTutorial($tutorial);
                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Commentaire ajouté !');

                return $this->redirectToRoute('app_tutorial_show', ['slug' => $tutorial->getSlug()]);
            }
        }

        return $this->render('tutorial/show.html.twig', [
            'tutorial' => $tutorial,
            'commentForm' => $form,
        ]);
    }
    #[Route('/tutorial/{slug}/edit', name: 'app_tutorial_edit')]
#[IsGranted('ROLE_USER')]
public function edit(
    Tutorial $tutorial,
    Request $request,
    EntityManagerInterface $em,
    SluggerInterface $slugger
): Response {

    // Vérifier que l'utilisateur est l'auteur
    if ($tutorial->getAuthor() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce tutoriel.');
    }

    $form = $this->createForm(TutorialType::class, $tutorial);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $tutorial->setSlug($slugger->slug($tutorial->getTitle())->lower() . '-' . uniqid());

        $em->flush();

        $this->addFlash('success', 'Tutoriel modifié avec succès !');

        return $this->redirectToRoute('app_tutorial_show', [
            'slug' => $tutorial->getSlug()
        ]);
    }

    return $this->render('tutorial/edit.html.twig', [
        'form' => $form,
        'tutorial' => $tutorial,
    ]);
}
    #[Route('/tutorial/{slug}/delete', name: 'app_tutorial_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Tutorial $tutorial, EntityManagerInterface $em): Response
    {
        // Vérifier que l'utilisateur est l'auteur
        if ($tutorial->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce tutoriel.');
        }

        $em->remove($tutorial);
        $em->flush();

        $this->addFlash('success', 'Tutoriel supprimé avec succès !');

        return $this->redirectToRoute('app_accueil');
    }
}