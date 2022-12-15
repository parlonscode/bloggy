<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
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

        $tags = [];
        for ($j = 1; $j <= 10; ++$j) {
            $tag = new Tag();
            $tag->setName($faker->unique()->word());
            $manager->persist($tag);
            $tags[] = $tag;
        }

        for ($i = 1; $i <= 80; ++$i) {
            $post = new Post();
            $post->setTitle($faker->unique()->sentence());
            $post->setBody($faker->paragraph(10));
            $post->setPublishedAt(
                $faker->boolean(75)
                ? \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-50 days', '+10 days')
                )
                : null
            );
            $post->setAuthor($faker->boolean(50) ? $user : $admin);

            foreach ($faker->randomElements($tags, 3) as $tag) {
                $post->addTag($tag);
            }

            $manager->persist($post);

            for ($k = 1; $k <= $faker->numberBetween(1, 5); ++$k) {
                $comment = new Comment();
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
