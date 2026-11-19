<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Entity\Tutorial;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private SluggerInterface $slugger,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = [];
        for ($i = 1; $i <= 3; $i++) {
            $u = (new User())
                ->setEmail("user$i@devdocs.test")
                ->setPseudonyme($faker->userName())
                ->setRoles($i === 1 ? ['ROLE_ADMIN'] : []);
            $u->setPassword($this->hasher->hashPassword($u, 'password'));
            $manager->persist($u);
            $users[] = $u;
        }

        $categories = [];
        foreach (['PHP', 'DevOps', 'Frontend', 'Base de données', 'Sécurité'] as $name) {
            $c = (new Category())->setName($name)->setDescription($faker->sentence(10));
            $manager->persist($c);
            $categories[] = $c;
        }

        $tags = [];
        foreach (['Débutant', 'Performance', 'Sécurité', 'Docker', 'API', 'Git',
                'Testing', 'Avancé', 'Astuce', 'Bonnes pratiques'] as $name) {
            $t = (new Tag())->setName($name);
            $manager->persist($t);
            $tags[] = $t;
        }

        $tutorials = [];
        for ($i = 0; $i < 15; $i++) {
            $title = $faker->sentence(5);
            $tuto = (new Tutorial())
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title)->lower() . '-' . $i)
                ->setContent($faker->paragraphs(5, true))
                ->setAuthor($faker->randomElement($users))
                ->setCategory($faker->randomElement($categories));
            foreach ($faker->randomElements($tags, 3) as $tag) {
                $tuto->addTag($tag);
            }
            $manager->persist($tuto);
            $tutorials[] = $tuto;
        }

        for ($i = 0; $i < 30; $i++) {
            $manager->persist((new Comment())
                ->setContent($faker->realText(120))
                ->setAuthor($faker->randomElement($users))
                ->setTutorial($faker->randomElement($tutorials)));
        }

        $manager->flush();
    }
    }
