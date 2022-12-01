<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            $post->setTitle($faker->unique()->sentence());
            $post->setBody($faker->paragraph(10));
            $post->setPublishedAt(
                $faker->boolean(75)
                ? \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-50 days', '-10 days')
                )
                : null
            );
            $post->setAuthor($faker->boolean(50) ? $user : $admin);
            $manager->persist($post);

            for ($j = 1; $j < $faker->numberBetween(1, 3); ++$j) {
                $tag = new Tag;
                $tag->setName($faker->unique()->word());
                $tag->addPost($post);
                $manager->persist($tag);
            }

            for ($k = 1; $k <= $faker->numberBetween(1, 5); ++$k) {
                $comment = new Comment;
                $comment->setName($faker->name());
                $comment->setEmail($faker->email());
                $comment->setBody($faker->paragraph());
                $comment->setIsActive($faker->boolean(90));
                $comment->setPost($post);
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
