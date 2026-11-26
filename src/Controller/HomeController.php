<?php

namespace App\Controller;

use App\Repository\TutorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoryRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        TutorialRepository $tutorialRepository,
        CategoryRepository $categoryRepository
    ): Response {

        // Récupérer le mot recherché
        $searchTerm = trim($request->query->get('q', ''));

        // Préparer la requête Doctrine
        $queryBuilder = $tutorialRepository->createQueryBuilder('t')
            ->leftJoin('t.category', 'c')
            ->addSelect('c')
            ->leftJoin('t.author', 'u')
            ->addSelect('u')
            ->orderBy('t.createdAt', 'DESC');

        // Rechercher dans le titre si un mot est saisi
        if ($searchTerm !== '') {
            $queryBuilder
                ->andWhere('t.title LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%');
        } else {
            // Afficher les 6 derniers tutoriels
            $queryBuilder->setMaxResults(6);
        }

        $tutorials = $queryBuilder->getQuery()->getResult();

        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
       return $this->render('home/index.html.twig', [
    'tutorials' => $tutorials,
    'searchTerm' => $searchTerm,
    'categories' => $categories,
]);
    }
}