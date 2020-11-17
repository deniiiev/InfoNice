<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $categoryRepo;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, CategoryRepository $categoryRepo)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->categoryRepo = $categoryRepo;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('ru_RU');
        $slugger = new Slugify();


        // Users
        $user = new User();
        $user->setUsername('deniev');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'deniev'
        ));
        $manager->persist($user);

        // Categories
        $categories = [
            'politic' => 'Политика',
            'health' => 'Здоровье',
            'music' => 'Музыка',
            'sport' => 'Спорт',
            'documents' => 'Документы'
        ];

        foreach ($categories as $slug => $title) {
            $category = new Category();
            $category->setSlug($slug);
            $category->setTitle($title);
            $manager->persist($category);
            $manager->flush();
        }

        // Posts
        for ($p = 1; $p <= 10; $p++) {
            $sentence = $faker->sentence(8);
            $post = new Post();

            $post->setAuthor($user);
            $post->setTitle($sentence);
            $post->setCreatedAt(new DateTime('now - ' . $faker->numberBetween(1,20) . ' day'));
            $post->setPublishedAt(new DateTime('now - ' . $faker->numberBetween(1,20) . ' day'));
            $post->setSlug($slugger->slugify($sentence));
            $post->setImage('document.jpg');
            $post->setDescription($faker->text(300));
            $post->addCategory($this->categoryRepo->findOneBy(['title' => $categories[array_rand($categories)]]));

            $manager->persist($post);
        }

        $manager->flush();
    }
}
