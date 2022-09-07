<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        dd($faker->name(), $faker->email(), $faker->realText());

        // $user = new User;
        // $user->setName('Mercury Series');
        // $user->setEmail('mercuryseries@gmail.com');
        // // secret123
        // $user->setPassword('$2y$13$CxN7gFXFLXu1v.nNP9paD.jvMiAaPx318BwOoFapnYYpvmbJv5l9e');
        // $manager->persist($user);
        
        // $admin = new User;
        // $admin->setName('Mister Admin');
        // $admin->setEmail('admin@bloggy.wip');
        // $admin->setRoles(['ROLE_ADMIN']);
        // // secret123
        // $admin->setPassword('$2y$13$dfAdM52nYyvgbmQjShEjFuOhphhWmx8hL/rwo4QbinMpSBzckfio.');
        // $manager->persist($admin);
        
        // for ($i = 1; $i <= 10; $i++) {
        //     $post = new Post;
        //     $post->setTitle('Post '.$i);
        //     $post->setSlug('post-'.$i);
        //     $post->setBody('El charabia here just to test.');
        //     $post->setPublishedAt(
        //         mt_rand(1, 10) >= 5
        //         ? new \DateTimeImmutable(sprintf('-%d days', mt_rand(10, 50)))
        //         : null
        //     );
        //     $post->setAuthor(mt_rand(1, 10) >= 5 ? $user : $admin);
        //     $manager->persist($post);
        // }

        // $manager->flush();
    }
}
