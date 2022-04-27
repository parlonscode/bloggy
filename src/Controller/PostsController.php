<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAllPublishedOrdered();

        return $this->render('posts/index.html.twig', compact('posts'));
    }

    #[Route('/posts/{slug}', name: 'app_posts_show')]
    public function show(Post $post): Response
    {
        return $this->render('posts/show.html.twig', compact('post'));
    }
}
