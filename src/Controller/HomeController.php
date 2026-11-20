<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Tag;

use App\Repository\TutorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TutorialRepository $repo): Response
    {
        return $this->render('home/index.html.twig', [
            'tutorials' => $repo->findBy([], ['createdAt' => 'DESC'], 6),
        ]);
    }

    #[Route('/category/{id}', name: 'app_category')]
    public function byCategory(Category $category): Response
    {
        return $this->render('home/list.html.twig', [
            'title' => $category->getName(),
            'tutorials' => $category->getTutorials(),
        ]);
    }

    #[Route('/tag/{id}', name: 'app_tag')]
    public function byTag(Tag $tag): Response
    {
        return $this->render('home/list.html.twig', [
            'title' => '#' . $tag->getName(),
            'tutorials' => $tag->getTutorials(),
        ]);
    }

    
}