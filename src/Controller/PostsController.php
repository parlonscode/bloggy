<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        // TODO: filter to select only published posts
        // order by publishedAt DESC
        $posts = $postRepository->findAll();

        return $this->render('posts/index.html.twig', compact('posts'));
    }
}
