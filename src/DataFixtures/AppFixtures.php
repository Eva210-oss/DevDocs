<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Tutorial;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Création des utilisateurs
        $users = [];

        $userData = [
            ['amal@devdocs.com', 'Amal', 'password123'],
            ['lina@devdocs.com', 'Lina', 'password123'],
            ['yassine@devdocs.com', 'Yassine', 'password123'],
        ];

        foreach ($userData as [$email, $pseudo, $password]) {
            $user = new User();
            $user->setEmail($email);
            $user->setPseudonyme($pseudo);
            $user->setRoles(['ROLE_USER']);

            $hashedPassword = $this->passwordHasher
                ->hashPassword($user, $password);

            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $users[] = $user;
        }

        // 2. Création des catégories
        $categoryData = [
            ['PHP', 'Tutoriels sur le langage PHP.'],
            ['Symfony', 'Développement avec le framework Symfony.'],
            ['JavaScript', 'Programmation JavaScript et développement web.'],
            ['Python', 'Apprentissage et utilisation du langage Python.'],
            ['Database', 'Gestion et manipulation des bases de données.'],
        ];

        $categories = [];

        foreach ($categoryData as [$name, $description]) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($description);

            $manager->persist($category);
            $categories[] = $category;
        }

        // 3. Création des tags
        $tagNames = [
            'Beginner',
            'Advanced',
            'Backend',
            'Frontend',
            'API',
            'Database',
            'Security',
            'OOP',
            'Web Development',
            'Programming',
        ];

        $tags = [];

        foreach ($tagNames as $name) {
            $tag = new Tag();
            $tag->setName($name);

            $manager->persist($tag);
            $tags[] = $tag;
        }

        // 4. Création des tutoriels
        $tutorialData = [
            ['Introduction à PHP', 'Découvrez les bases du langage PHP et apprenez à créer vos premières applications web.', 0, 0],
            ['Les variables en PHP', 'Apprenez à déclarer et utiliser les variables dans vos programmes PHP.', 0, 1],
            ['Les fonctions PHP', 'Découvrez comment créer des fonctions réutilisables pour organiser votre code.', 0, 2],
            ['Introduction à Symfony', 'Découvrez le framework Symfony et ses principales fonctionnalités pour développer des applications web.', 1, 0],
            ['Les Controllers Symfony', 'Apprenez à créer des controllers et à gérer les requêtes HTTP dans Symfony.', 1, 1],
            ['Twig et les templates', 'Découvrez le moteur de templates Twig pour organiser et afficher vos pages web.', 1, 2],
            ['Introduction à JavaScript', 'Découvrez les bases de JavaScript pour rendre vos pages web interactives.', 2, 0],
            ['Les événements JavaScript', 'Apprenez à gérer les événements utilisateur avec JavaScript.', 2, 1],
            ['Manipulation du DOM', 'Découvrez comment sélectionner et modifier les éléments HTML avec JavaScript.', 2, 2],
            ['Introduction à Python', 'Apprenez les bases de Python et écrivez vos premiers programmes.', 3, 0],
            ['Les listes en Python', 'Découvrez comment créer, parcourir et manipuler des listes en Python.', 3, 1],
            ['Les fonctions Python', 'Apprenez à organiser votre code Python avec des fonctions réutilisables.', 3, 2],
            ['Introduction à MySQL', 'Découvrez les bases de données relationnelles et les commandes essentielles de MySQL.', 4, 0],
            ['Les requêtes SQL', 'Apprenez à sélectionner, ajouter, modifier et supprimer des données avec SQL.', 4, 1],
            ['Les jointures SQL', 'Découvrez comment relier plusieurs tables avec les jointures SQL.', 4, 2],
        ];

        $tutorials = [];

        foreach ($tutorialData as $index => [$title, $content, $categoryIndex, $userIndex]) {
            $tutorial = new Tutorial();

            $tutorial->setTitle($title);
            $tutorial->setSlug(strtolower(str_replace(' ', '-', $title)));
            $tutorial->setContent($content);
            $tutorial->setCreatedAt(new \DateTimeImmutable());
            $tutorial->setAuthor($users[$userIndex]);
            $tutorial->setCategory($categories[$categoryIndex]);

            // Associer deux tags à chaque tutoriel
            $tutorial->addTag($tags[$index % count($tags)]);
            $tutorial->addTag($tags[($index + 1) % count($tags)]);

            $manager->persist($tutorial);
            $tutorials[] = $tutorial;
        }

        // 5. Création de 30 commentaires
        $commentTexts = [
            'Merci pour ce tutoriel, les explications sont très claires.',
            'Excellent contenu, cela m’a beaucoup aidé à comprendre.',
            'Très intéressant ! J’attends la suite avec impatience.',
            'Bravo pour ce travail, continuez comme ça.',
            'Est-ce possible d’avoir un exemple pratique supplémentaire ?',
        ];

        for ($i = 0; $i < 30; $i++) {
            $comment = new Comment();

            $comment->setContent($commentTexts[$i % count($commentTexts)]);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $comment->setAuthor($users[$i % count($users)]);
            $comment->setTutorial($tutorials[$i % count($tutorials)]);

            $manager->persist($comment);
        }

        // Enregistrement de toutes les données
        $manager->flush();
    }
}