<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user = new User();
        $user->setName('Mercury Series');
        $user->setEmail('mercuryseries@gmail.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'secret123'));
        $manager->persist($user);

        $admin = new User();
        $admin->setName('Mister Admin');
        $admin->setEmail('admin@bloggy.wip');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'secret123'));
        $manager->persist($admin);

        for ($i = 1; $i <= 10; ++$i) {
            $post = new Post();
            $post->setTitle($title = $faker->unique()->sentence());
            $post->setSlug($this->slugger->slug(mb_strtolower($title)));
            $post->setBody($faker->paragraph(10));
            $post->setPublishedAt(
                $faker->boolean(75)
                ? \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-50 days', '-10 days'))
                : null
            );
            $post->setAuthor($faker->boolean(50) ? $user : $admin);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
