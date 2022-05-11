<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    public function __construct(private PostRepository $postRepository) {}

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $posts = $this->postRepository->findAllPublishedOrdered();

        return $this->render('posts/index.html.twig', compact('posts'));
    }

    #[Route(
        '/posts/{year}/{month}/{day}/{slug}',
        name: 'app_posts_show',
        requirements: [
            'year' => '[0-9]{4}',
            'month' => '[0-9]{2}',
            'day' => '[0-9]{2}',
            'slug' => '[a-z0-9\-]+',
        ],
        methods: ['GET']
    )]
    public function show(int $year, int $month, int $day, string $slug): Response
    {
        $post = $this->postRepository->findOneByPublishDateAndSlug($year, $month, $day, $slug);

        if (is_null($post)) {
            throw $this->createNotFoundException('Post not found!');
        }

        return $this->render('posts/show.html.twig', compact('post'));
    }
}
